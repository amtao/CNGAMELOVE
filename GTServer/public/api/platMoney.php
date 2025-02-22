<?php
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
$begindt = strtotime(date('Y-m-d'));
$enddt = strtotime(date('Y-m-d H:i:s'));
if (!empty($_REQUEST['begindt']) &&  !empty($_REQUEST['enddt'])){
    $begindt = strtotime($_REQUEST['begindt']);
    $enddt = strtotime($_REQUEST['enddt']);
}
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
Common::loadModel('ServerModel');
Common::loadModel('OrderModel');
$serverList = ServerModel::getServList();
$totalMoney = 0;
$platformList = OrderModel::get_all_platform();
foreach ($serverList as $k => $v) {
    if ( empty($v) ) {
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
    unset($sql);
    $sql = "select sum(`money`) as totalMoney from `t_order` where `status`>0 and `ptime`<{$enddt} and `ptime`>={$begindt}";
    $money= $db->fetchArray($sql);
    $totalMoney += $money[0]["totalMoney"];
    /*unset($sql, $money);
    $sql = "select `roleid`,`money` from `t_order` where `status`>0 and `ptime`<{$enddt} and `ptime`>{$begindt} and `platform`='local'";
    $localInfo = $db->fetchArray($sql);
    if (!empty($localInfo)){
        $uids = array();
        foreach ($localInfo as $lk => $lv){
            if (!in_array($lv['roleid'],$uids)){
                $uids[] = $lv['roleid'];
            }
            $uidMoney[$lv['roleid']] += $lv['money'];
        }
        $uid = implode(',',$uids);
        unset($sql);
        $registerDb = Common::getDbBySevId(ServerModel::getDefaultServerId());
        unset($sql);
        $sql = "SELECT `platform`,`uid` FROM `register` WHERE `uid` IN (".$uid.")";
        $registerInfo = $registerDb->fetchArray($sql);
        foreach ($registerInfo as $rk => $rv){
            $data[$rv['platform']]['total'] += $uidMoney[$rv['uid']];
        }
    }*/

    $sql2 =  "SELECT sum(`money`) AS totalMoney,`platform` FROM `t_order` WHERE `status`>0 AND `ptime`<{$enddt} AND `ptime`>={$begindt} GROUP BY `platform`";
    $money= $db->fetchArray($sql2);
    foreach ($money as $mk => $mv){
        $data[$mv['platform']]['pfname'] = $platformList[$mv['platform']];
        $data[$mv['platform']]['total'] = $data[$mv['platform']]['total'] + $mv['totalMoney'];
        $data[$mv['platform']]['zc'] = 0;
    }
}
$info['list'] = $data;
$info['totalMoney'] = $totalMoney;
echo json_encode($info);





