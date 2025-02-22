<?php
//数据统计
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
set_time_limit(0);
Common::loadModel('ServerModel');
$id = ServerModel::getDefaultServerId();
$SevidCfg1 = Common::getSevidCfg($id);
$begindt = '2018-11-01 00:00:00';
$enddt = '2019-08-08 23:59:59';
$mem = Common::getMyMem();
$mkey = 'old_remain_crontab';
$times = $mem->get($mkey);
if (strtotime($times['start']) == strtotime(date('Y-m-d', strtotime("-1 day")))){
    return false;
}
if ($times == false){
    $begindt = '2017-10-01 00:00:00';
    $enddt = '2017-10-01 23:59:59';
    $times['start'] = $begindt;
    $times['end'] = $enddt;
    $mem->set($mkey, $times);
}else{
    $begindt = date('Y-m-d H:i:s', strtotime($times['start'])+86400);
    $enddt = date('Y-m-d H:i:s', strtotime($times['end'])+86400);
    $times['start'] = $begindt;
    $times['end'] = $enddt;
    $mem->set($mkey, $times);
}
$flowDb = Common::getMyDb('flow');
$serverList = ServerModel::getServList();
$db = Common::getMyDb();
$beginDateTime = strtotime($begindt);
$endDateTime = strtotime($enddt);
$info = array();
$sql = "select  `openid` AS o,`reg_time` AS r,`platform` AS p,`servid` AS s,`data` AS d from `register` where `reg_time`<={$endDateTime} and `reg_time`>={$beginDateTime}";
$result = $db->fetchArray($sql);
foreach ($result as $key => $value){
    $data[$value['p']]['openid'][$value['o']] = 1;
}
foreach ($data as $dk => $dv){
    $register[$dk] = count($dv['openid']);
}
$register = json_encode($register);
unset($result, $sql, $key, $value);
//登录人数
$begin = $beginDateTime;
$end = $endDateTime;
$sql = "SELECT count(DISTINCT `openid`) as `login`,`platform` FROM `login_log` WHERE `login_time`>=".$begin.' AND `login_time`<='.$end.' GROUP BY `platform`';
$result = $db->fetchArray($sql);
$login = json_encode($result);
unset($result, $sql, $k, $v);
//次日留存
$begin = $beginDateTime + 86400;
$end = $endDateTime + 86400;
if ($begin <= strtotime(date("Y-m-d"))) {
    $sql = "SELECT DISTINCT `openid`,`platform` FROM `login_log` WHERE `login_time`>=" . $begin . ' AND `login_time`<=' . $end;
    $result = $db->fetchArray($sql);
    foreach ($result as $k => $v) {
        if (isset($data[$v['platform']]['openid'][$v['openid']])) {
            $info[date("Y-m-d", $begin)][$v['platform']] += 1;
        }
    }
    unset($result, $sql, $k, $v);
}
//三日留存
$begin = $beginDateTime + 86400*2;
$end = $endDateTime + 86400*2;
if ($begin <= strtotime(date("Y-m-d"))) {
    $sql = "SELECT DISTINCT `openid`,`platform` FROM `login_log` WHERE `login_time`>=" . $begin . ' AND `login_time`<=' . $end;
    $result = $db->fetchArray($sql);
    foreach ($result as $k => $v) {
        if (isset($data[$v['platform']]['openid'][$v['openid']])) {
            $info[date("Y-m-d", $begin)][$v['platform']] += 1;
        }
    }
    unset($result, $sql, $k, $v);
}
//五日留存
$begin = $beginDateTime + 86400*4;
$end = $endDateTime + 86400*4;
if ($begin <= strtotime(date("Y-m-d"))) {
    $sql = "SELECT DISTINCT `openid`,`platform` FROM `login_log` WHERE `login_time`>=" . $begin . ' AND `login_time`<=' . $end;
    $result = $db->fetchArray($sql);
    foreach ($result as $k => $v) {
        if (isset($data[$v['platform']]['openid'][$v['openid']])) {
            $info[date("Y-m-d", $begin)][$v['platform']] += 1;
        }
    }
    unset($result, $sql, $k, $v);
}
//七日留存
$begin = $beginDateTime + 86400 * 6;
$end = $endDateTime + 86400 * 6;
if ($begin <= strtotime(date("Y-m-d"))) {
    $sql = "SELECT DISTINCT `openid`,`platform` FROM `login_log` WHERE `login_time`>=" . $begin . ' AND `login_time`<=' . $end;
    $result = $db->fetchArray($sql);
    foreach ($result as $k => $v) {
        if (isset($data[$v['platform']]['openid'][$v['openid']])) {
            $info[date("Y-m-d", $begin)][$v['platform']] += 1;
        }
    }
    unset($result, $sql, $k, $v);
}
//十四日留存
$begin = $beginDateTime + 86400*13;
$end = $endDateTime + 86400*13;
if ($begin <= strtotime(date("Y-m-d"))) {
    $sql = "SELECT DISTINCT `openid`,`platform` FROM `login_log` WHERE `login_time`>=" . $begin . ' AND `login_time`<=' . $end;
    $result = $db->fetchArray($sql);
    foreach ($result as $k => $v) {
        if (isset($data[$v['platform']]['openid'][$v['openid']])) {
            $info[date("Y-m-d", $begin)][$v['platform']] += 1;
        }
    }
    unset($result, $sql, $k, $v);
}
//月日留存
$begin = $beginDateTime + 86400*29;
$end = $endDateTime + 86400*29;
if ($begin <= strtotime(date("Y-m-d"))){
    $sql = "SELECT DISTINCT `openid`,`platform` FROM `login_log` WHERE `login_time`>=".$begin.' AND `login_time`<='.$end;
    $result = $db->fetchArray($sql);
    foreach ($result as $k => $v){
        if (isset($data[$v['platform']]['openid'][$v['openid']])){
            $info[date("Y-m-d", $begin)][$v['platform']] += 1;
        }
    }
    unset($result, $sql, $k, $v);
}

//2月日留存
$begin = $beginDateTime + 86400*59;
$end = $endDateTime + 86400*59;
if ($begin <= strtotime(date("Y-m-d"))){
    $sql = "SELECT DISTINCT `openid`,`platform` FROM `login_log` WHERE `login_time`>=".$begin.' AND `login_time`<='.$end;
    $result = $db->fetchArray($sql);
    foreach ($result as $k => $v){
        if (isset($data[$v['platform']]['openid'][$v['openid']])){
            $info[date("Y-m-d", $begin)][$v['platform']] += 1;
        }
    }
    unset($result, $sql, $k, $v);
}

//3月日留存
$begin = $beginDateTime + 86400*89;
$end = $endDateTime + 86400*89;
if ($begin <= strtotime(date("Y-m-d"))){
    $sql = "SELECT DISTINCT `openid`,`platform` FROM `login_log` WHERE `login_time`>=".$begin.' AND `login_time`<='.$end;
    $result = $db->fetchArray($sql);
    foreach ($result as $k => $v){
        if (isset($data[$v['platform']]['openid'][$v['openid']])){
            $info[date("Y-m-d", $begin)][$v['platform']] += 1;
        }
    }
    unset($result, $sql, $k, $v);
}

$jsonInfo = json_encode($info);
$sql = "INSERT INTO `remain` (`date`, `login`, `register`, `info`) VALUES ('{$begindt}', '{$login}', '{$register}', '{$jsonInfo}');";
$result = $flowDb->query($sql);
echo  $result;
?>
