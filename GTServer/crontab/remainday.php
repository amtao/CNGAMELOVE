<?php
//数据统计
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
set_time_limit(0);
Common::loadModel('ServerModel');
$id = ServerModel::getDefaultServerId();
$SevidCfg1 = Common::getSevidCfg($id);
$flowDb = Common::getMyDb('flow');
$serverList = ServerModel::getServList();
$db = Common::getMyDb();
$begindt = date('Y-m-d 00:00:00', strtotime("-1 day"));
$enddt = date('Y-m-d 23:59:59', strtotime("-1 day"));
//缓存
$mem = Common::getMyMem();
$mkey = 'remainday_crontab';
$status = $mem->get($mkey);
//日期重置
if ($begindt != $status['day']){
    $status['status'] = 1;
    $status['number'] = 0;
    $status['day'] = $begindt;
    $mem->set($mkey, $status);
    return;
}
//状态结束不执行
if ($status != false && $status['status'] == 0){
    return;
}
//缓存重置3,5,7,14,20
if ($status == false){
    $status['status'] = 1;
    $status['number'] = 1;
    $status['day'] = $begindt;
    $mem->set($mkey, $status);
}else{
    if ($status['number'] == 0){
        $status['number'] = 1;
        $mem->set($mkey, $status);
    }elseif($status['number'] == 1){
        $status['number'] = 2;
        $mem->set($mkey, $status);
    }elseif($status['number'] == 2){
        $status['number'] = 4;
        $mem->set($mkey, $status);
    }elseif($status['number'] == 4){
        $status['number'] = 6;
        $mem->set($mkey, $status);
    }elseif($status['number'] == 6){
        $status['number'] = 13;
        $mem->set($mkey, $status);
    }elseif($status['number'] == 13){
        $status['number'] = 29;
        $mem->set($mkey, $status);
    }elseif($status['number'] == 29){
        $status['number'] = 59;
        $mem->set($mkey, $status);
    }elseif($status['number'] == 59){
        $status['number'] = 89;
        $status['status'] = 0;
        $mem->set($mkey, $status);
    }
}
echo $status['number'];
//注册人数
$beginDateTime = strtotime($begindt)-86400*$status['number'];
$endDateTime = strtotime($enddt)-86400*$status['number'];
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
//留存
$begin = $beginDateTime + 86400*$status['number'];
$end = $endDateTime + 86400*$status['number'];
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
$date = date('Y-m-d H:i:s', $beginDateTime);
$sql = "SELECT `info` FROM `remain` WHERE `date`='{$date}';";
$result = $flowDb->fetchArray($sql);
if (is_array($result)){
    foreach ($result as $k => $v){
        $infos = json_decode($v['info'], true);
    }
}
foreach ($info as $k => $v){
    $infos[$k] = $v;
}
$infos = json_encode($infos);
unset($result, $sql, $k, $v);
$sql = "UPDATE `remain` SET `info`='{$infos}' WHERE `date`='{$date}';";
$result = $flowDb->query($sql);
echo $result;
?>