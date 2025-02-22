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
$serverList = ServerModel::getServList();
$totalMoney = 0;
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
    $sql = "select sum(`money`) as totalMoney from `t_order` where `status`>0 and `ptime`<{$enddt} and `ptime`>{$begindt}";
    $money= $db->fetchArray($sql);
    $totalMoney += floatval($money[0]["totalMoney"]);
}
echo $totalMoney;





