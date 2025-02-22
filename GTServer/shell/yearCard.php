<?php
/**
 * 年卡订正脚本
 *
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ClubModel');
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
            echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
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
        year();
    }
}

exit();


function year(){
    $year = array();
//    $sql = "select count(roleid) as num,`roleid` as uid from `t_order`  where `money` = 288  group by `roleid`";
    $db = Common::getMyDb();
//    $data = $db->fetchArray($sql);
//    foreach ($data as $v){
//        $Act68Model = Master::getAct68($v['uid']);
//        unset($Act68Model->info[2]['daytime']);
//        $Act68Model->save();
//        $Act68Model->ht_destroy();
//        echo $v['uid'],PHP_EOL;
//    }
    $sql = "select `roleid` as uid,`ptime` from `t_order` where `money` = 288 and `status` = 1 order by `orderid`;";
    $data = $db->fetchArray($sql);
    foreach($data as $v){
        if(!isset($year[$v['uid']]['ptime'])){
            $year[$v['uid']]['ptime'] = $v['ptime'];
        }
        $year[$v['uid']]['num'] += 1;
    }
    foreach($year as $k => $v){
        $Act68Model = Master::getAct68($k);
        $Act68Model->info[2]['daytime'] = $v['ptime'] + 365*24*60*60*$v['num'];
        $Act68Model->save();
        $Act68Model->ht_destroy();
        echo "UID:".$k,PHP_EOL;
    }
}