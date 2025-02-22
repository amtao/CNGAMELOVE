<?php
//统计

require_once dirname(__FILE__) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

//$uid = intval($_SERVER['argv'][1]);// $uid
$startTime = strtotime($_SERVER['argv'][1].' 00:00:00');// $startTime
$endTime = strtotime($_SERVER['argv'][2].' 23:59:59');  //$endTime
$startTime2 = strtotime($_SERVER['argv'][3].' 00:00:00');
$endTime2 = strtotime($_SERVER['argv'][4].' 23:59:59');


Common::loadModel('ServerModel');
$id = ServerModel::getDefaultServerId();
$SevidCfg = Common::getSevidCfg($id);
$Db = Common::getDbBySevId($id);

$sql = "select `openid` from `register` where `reg_time`>={$startTime} and `reg_time`<={$endTime} and `platform` = 'anfengiosgsjp'";

$result1 = $Db->fetchArray($sql);
$reg = array();
foreach ($result1 as $r){
    $reg[] = $r['openid'];
}

$sql = "select `openid` from `login_log` where `login_time`>={$startTime2} and `login_time`<={$endTime2} and `platform` = 'anfengiosgsjp'";

$result2 = $Db->fetchArray($sql);
$log = array();
foreach ($result2 as $r){
    $log[] = $r['openid'];
}

$log = array_unique($log);

$count1 = 0;
$count2 = count($reg);

foreach ($reg as $r){
    if (in_array($r,$log)) {
        $count1 ++;
    }
}
echo  number_format($count1/$count2, 2);