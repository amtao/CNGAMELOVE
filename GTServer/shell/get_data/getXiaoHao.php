<?php
/**
 * 获取小号信息
 */

set_time_limit(0);
require_once dirname(__FILE__) . '/../../public/common.inc.php';
header("content-type:text/csv; charset=UTF-8");
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rank.csv");
header("Pragma: no-cache");
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$serverList = ServerModel::getServList();
$btime = microtime(true);
echo "UID\tVIP\t最后登录时间",PHP_EOL;
//echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
if ( is_array($serverList) ) {
    $total = array();
    foreach ($serverList as $k => $v) {
        if ( empty($v) ) {
            continue;
        }
        $Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID

        //echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

        if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {
           // echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }
        if ( 0 < $serverID && $serverID != $Sev_Cfg['sevid'] ) {
            //echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }

        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
            && $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
           // echo PHP_EOL, '>>>从服跳过', PHP_EOL;
            continue;
        }

        $open_day = ServerModel::isOpen($Sev_Cfg['sevid']);
        //过滤未开服的
        if($open_day <= 0){
            continue;
        }
        echo $Sev_Cfg['sevid'].':total:'.getData(),PHP_EOL;
    }
}

function getData(){
    $db = Common::getMyDb('flow');
    $total = 0;
    $sTime = strtotime('2018-06-17 00:00:00');
    $eTime = strtotime('2018-06-28 00:00:00');
    for($i = 0;$i<100;$i++){
        if($i < 10){
            $table = '0'.$i;
        }else{
            $table = $i;
        }
        $sql = "select sum(cha) as total from `flow_event_{$table}` JOIN `flow_records_{$table}` where `flowid` = `id` and (`itemid` = 142 or `itemid` = 141) and `cha` > 0
and `ftime` > {$sTime} and `ftime` < {$eTime};";
        $data = $db->fetchArray($sql);
        foreach ($data as $value){
            $total += $value['total'];
        }
    }
    return $total;

}