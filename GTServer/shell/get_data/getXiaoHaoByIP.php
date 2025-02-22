<?php
/**
 * 获取小号信息
 */

set_time_limit(0);
require_once dirname(__FILE__) . '/../../public/common.inc.php';

Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$serverList = ServerModel::getServList();
$btime = microtime(true);

header("content-type:text/csv; charset=UTF-8");
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rank.csv");
header("Pragma: no-cache");

if ( is_array($serverList) ) {
    $total = array();
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
        getData();
    }
}

function getData(){
    $time = strtotime("2018-06-11 00:00:00");
    $db = Common::getMyDb();
    for($i = 0;$i<100;$i++){
        if($i < 10){
            $table = 'user_0'.$i;
        }else{
            $table = 'user_'.$i;
        }
        $sql = "select `uid`,`name`,`level`,`cash_buy`,`ip` from {$table} where `regtime`>{$time};";
        $data = $db->fetchArray($sql);
        foreach ($data as $value) {
            echo $value['uid'].','.$value['name'].','.$value['level'].','.$value['cash_buy'].','.$value['ip'],PHP_EOL;
        }
    }

}