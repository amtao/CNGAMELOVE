<?php
//数据统计
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
set_time_limit(0);
ini_set('memory_limit','4000M');
Common::loadModel('ServerModel');
$id = ServerModel::getDefaultServerId();
$SevidCfg1 = Common::getSevidCfg($id);

$nowTime = time();
$oldDay = strtotime('-31 day', $nowTime);
// $oldDay = strtotime("2019-11-12 00:00:00");
$day = date('Ymd', $oldDay);
$begindt = date('Y-m-d 00:00:00', $oldDay);
$enddt = date('Y-m-d 23:59:59', $oldDay);

$flowDb = Common::getDbBySevId(1, 'flow');
$db = Common::getDbBySevId(1);
$beginDateTime = strtotime($begindt);
$endDateTime = strtotime($enddt);

//登录人数
$sqlStartTime = strtotime('+1 day', $beginDateTime);
$sqlEndTime = strtotime('+29 day', $endDateTime);
$sql = "select `openid` AS o,`platform` AS p,`login_time` AS l from `login_log` where `login_time`<={$sqlEndTime} and `login_time`>={$sqlStartTime}";
$loginLogRes = $db->fetchArray($sql);

$loginLog = array();
foreach ($loginLogRes as $lk => $lv){
    $p = $lv['p'];
    $l = date('Ymd', $lv['l']);
    $loginLog[$l][$p]['openid'][] = $lv['o'];
}
unset($loginLogRes, $sql, $lk, $lv);

$data = array();
$infoData = array();
$sql = "select `openid` AS o,`reg_time` AS r,`platform` AS p,`uid` AS u,`data` AS d from `register` where `reg_time`<={$endDateTime} and `reg_time`>={$beginDateTime}";
$result = $db->fetchArray($sql);
foreach ($result as $key => $value){

    $p = $value['p'];
    $data[$p]['openid'] += 1;
    for ($i = 1; $i < 30; $i++) { 
        $twTime = date('Ymd', strtotime('+'.$i.' day', $beginDateTime));
        if ( isset($loginLog[$twTime][$p]['openid']) && in_array($value['o'], $loginLog[$twTime][$p]['openid']) ) {
            $infoData["d".$i][$p] += 1;
        }
    }
}

$register = array();
foreach ($data as $dk => $dv){

    $register[] = array("r" => $dv['openid'], "p" => $dk );
}
$register = json_encode($register);
unset($result, $sql, $key, $value);

$sql = "SELECT count(DISTINCT `openid`) as l,`platform` AS p FROM `login_log` WHERE `login_time`>=".$beginDateTime.' AND `login_time`<='.$endDateTime." GROUP BY p";
$result = $db->fetchArray($sql);

$loginData = array();
foreach ($result as $k => $v){

    $loginData[] = array("l" => $v["l"],"p" => $v["p"]);
}
unset($result, $sql);

$login = json_encode($loginData);
$info = json_encode($infoData);
$sql = "SELECT * FROM `remain` WHERE `date`='{$day}'";
$result = $flowDb->fetchArray($sql);
if (!empty($result)){
    $sql = "UPDATE `remain` SET `login`='{$login}',`register`='{$register}',`info`='{$info}' WHERE `date`='{$day}'";
    $result = $flowDb->query($sql);
}else{
    $sql = "INSERT INTO `remain` (`date`, `login`, `register`, `info`) VALUES ('{$day}', '{$login}', '{$register}', '{$info}');";
    $result = $flowDb->query($sql);
}

?>