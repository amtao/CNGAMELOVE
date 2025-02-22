<?php
//数据统计
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
Common::loadModel('OrderModel');
Common::loadModel('ServerModel');
$serverList = ServerModel::getServList();
$begindt = strtotime(date('Y-m-d'))-86400;
$enddt = strtotime(date('Y-m-d'));
if (!empty($_REQUEST['begindt']) &&  !empty($_REQUEST['enddt'])){
    $begindt = strtotime($_REQUEST['begindt']);
    $enddt = strtotime($_REQUEST['enddt']);
}
$SevidCfg1 = Common::getSevidCfg(1);//子服ID
$db = Common::getDbBySevId($SevidCfg1['sevid']);
//注册
$sql = "SELECT `platform`,count(`uid`) AS uidCount FROM `register` WHERE `reg_time`>=".$begindt." AND `reg_time`<".$enddt." AND `uid`>0 GROUP BY `platform`";
$result = $db->fetchArray($sql);
foreach ($result as $rk => $rv){
    $data[$rv['platform']]['register'] = $data[$rv['platform']]['register'] + $rv['uidCount'];
}

if ($enddt-$begindt<=86400*2){
    unset($sql, $result, $rk, $rv);
    //登录统计
    $sql = "SELECT `platform`, COUNT(DISTINCT `openid`) AS uidCount FROM `login_log` WHERE `login_time`>=".$begindt.' AND `login_time`<'.$enddt.' GROUP BY `platform` ';
    $result = $db->fetchArray($sql);
    foreach ($result as $rk => $rv){
        $data[$rv['platform']]['totalLogin'] = $data[$rv['platform']]['totalLogin'] + $rv['uidCount'];
    }
}
unset($sql, $result);
if (is_array($serverList)) {
    foreach ($serverList as $k => $v) {
        if (empty($v)) {
            continue;
        }
        $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
        if ( 999 == $SevidCfg1['sevid'] ) {
            continue;
        }
        if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
            continue;
        }
        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
            continue;
        }
        $db = Common::getDbBySevId($SevidCfg1['sevid']);
        // 公共用的前缀
        $sql = "select sum(`money`) as `totalMoney`,`platform` from `t_order` where `status`>0 and `ptime`>=" . $begindt . " and `ptime`<" . $enddt.' group by `platform`';
        $result = $db->fetchArray($sql);
        if (is_array($result) && ! empty($result)) {
            foreach ($result as $k => $v) {
                $data[$v['platform']]['total'] += (float) $v['totalMoney'];
            }
        }
        //总充值
        unset($result, $sql);
        $sql = "select sum(`money`) as `totalMoney`,`platform` from `t_order` where `status`>0  group by `platform`";
        $result = $db->fetchArray($sql);
        if (is_array($result) && ! empty($result)) {
            foreach ($result as $k => $v) {
                $data[$v['platform']]['totalMoney'] += (float) $v['totalMoney'];
            }
        }
    }
}
echo json_encode($data);
