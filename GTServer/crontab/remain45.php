<?php
//数据统计
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
set_time_limit(0);
ini_set('memory_limit','4000M');
Common::loadModel('ServerModel');
$id = ServerModel::getDefaultServerId();
$SevidCfg1 = Common::getSevidCfg($id);

$nowTime = time();
$lastDay = strtotime('-1 day', $nowTime);
$nowDayS = date('Y-m-d 00:00:00', $lastDay);
$nowDayE = date('Y-m-d 23:59:59', $lastDay);
$startTime = strtotime($nowDayS);
$endTime = strtotime($nowDayE);
$flowDb = Common::getDbBySevId(1, 'flow');
$db = Common::getDbBySevId(1);

//登录人数
$sql = "select `openid` AS o,`platform` AS p from `login_log` where `login_time`<={$endTime} and `login_time`>={$startTime}";
$loginLogRes = $db->fetchArray($sql);

$loginLog = array();
foreach ($loginLogRes as $lk => $lv){
    $p = $lv['p'];
    $loginLog[$p]['openid'][] = $lv['o'];
}
unset($loginLogRes, $sql, $lk, $lv);

$dayList = array(44, 59, 89);
foreach ($dayList as $dk => $dv) {

    $oldDay = strtotime('-'.$dv.' day', $lastDay);
    $day = date('Ymd', $oldDay);
    $begindt = date('Y-m-d 00:00:00', $oldDay);
    $enddt = date('Y-m-d 23:59:59', $oldDay);
    $beginDateTime = strtotime($begindt);
    $endDateTime = strtotime($enddt);

    $sql = "SELECT * FROM `remain` WHERE `date`='{$day}'";
    $result = $flowDb->fetchArray($sql);

    if (empty($result)){
        echo 'no error';
        continue;
    }

    $info = json_decode($result[0]["info"], true);
    $data = array();
    $infoData = array();
    $sql = "select `openid` AS o,`reg_time` AS r,`platform` AS p,`uid` AS u,`data` AS d from `register` where `reg_time`<={$endDateTime} and `reg_time`>={$beginDateTime}";
    $result = $db->fetchArray($sql);
    foreach ($result as $key => $value){

        $p = $value['p'];
        $data[$p]['openid'] += 1;

        if ( isset($loginLog[$p]['openid']) && in_array($value['o'], $loginLog[$p]['openid']) ) {
            $infoData[$p] += 1;
        }
    }

    $info["d".$dv] = $infoData;
    $newInfo = json_encode($info);
    $sql = "UPDATE `remain` SET `info`='{$newInfo}' WHERE `date`='{$day}'";
    $flowDb->query($sql);
}






?>