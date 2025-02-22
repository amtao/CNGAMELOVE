<?php
/**
 * 后台配置文件脚本
 * 调用方式：每分钟跑一次
 *
 */
set_time_limit(0);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
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

        delete_mem();
    }
}

function delete_mem(){
    $db = Common::getMyDb();
    $uids = array();
    for ($i=0;$i<=99;$i++){
        $table = $i < 10 ? 'act_0'.$i : 'act_'.$i;
        $sql = "select `uid` from {$table} WHERE `actid`=129";
        $res = $db->fetchArray($sql);
        if(!empty($res)){
            $uids = array_merge($uids,$res);
        }
    }
    if(!empty($uids)){
        $mem = Common::getMyMem();
        foreach ($uids as $uid_arr){
            echo $uid_arr['uid'],PHP_EOL;
            $mem->delete($uid_arr['uid'].'_team');
        }
    }
}
