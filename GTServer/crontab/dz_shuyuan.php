<?php
/**
 * 后台配置文件脚本
 * 调用方式：每分钟跑一次
 *
 */
set_time_limit(0);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$serverList = ServerModel::getServList();
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
if ( is_array($serverList) ) {

    foreach ($serverList as $k => $v) {
        if ( empty($v) ) {
            continue;
        }
        $Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID

        echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

        if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {
          //  echo PHP_EOL, '>>>跳过', PHP_EOL;
          //  continue;
        }
        if ( 0 < $serverID && $serverID != $Sev_Cfg['sevid'] ) {
            echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }

        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
            && $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
            echo PHP_EOL, '>>>从服跳过', PHP_EOL;
            continue;
        }

        $open_day = ServerModel::isOpen($Sev_Cfg['sevid']);
        //过滤未开服的
        if($open_day <= 0){
            continue;
        }
        echo '生效时间'.$open_day."\n";
        shuyuan(10,0);
    }
}

exit();


/*
 * 根据桌位购买记录更新act数据
 */
function shuyuan($level,$count){
    $stime = time();
    $db = Common::getMyDb('flow');
    $db2 = Common::getMyDb();
    for ($i=0;$i<100;$i++) {
        if($i < 10){
            $table = 'flow_event_0'.$i;
        }else{
            $table = 'flow_event_'.$i;
        }
        $sql = 'select `uid`,count(id) as num from `'.$table.'` 
        where `model` = "school" and `ctrl` = "buydesk" group by `uid` having count(id)>'.$count.';';
        $userArray = $db->fetchArray($sql);
        if(empty($userArray)){continue;}
        foreach ($userArray as $v) {
            //act_table
            $table_id = $v['uid']%100;
            $table = $table_id>=10?'act_'.$table_id:'act_0'.$table_id;
            $sql = 'select `tjson` from '.$table.' where `actid` = 15 and `uid` = '.$v['uid'];
            $act_data = $db2->fetchRow($sql);//获取数据
            if (empty($act_data)) {continue;}
            $json = json_decode($act_data['tjson'], true);
            if($json['data']['desk'] - $v['num'] >= 1){continue;}

            //更新到最新
            $json['data']['desk'] = $v['num'] +1;
            $new_json = json_encode($json, JSON_UNESCAPED_UNICODE);
            $sql = "update {$table} set `tjson`='{$new_json}' where `actid`=15 and `uid`={$v['uid']};";
            if ($db2->query($sql)) {
                $cache = Common::getCacheByUid($v['uid']);
                $key = $v['uid'].'_act_15';
                $cache->delete($key);
                echo "uid:".$v['uid'].",num:".$json['data']['desk'].PHP_EOL;
            }
            unset($act_data, $json, $new_json, $sql);
        }
        unset($sql, $userArray);
    }
    echo '耗时：'.(time()-$stime);
}

