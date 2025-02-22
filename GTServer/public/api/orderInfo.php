<?php
ini_set("display_errors","On");
error_reporting(E_ALL);
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
$params = $_REQUEST;
$beginTime = $_REQUEST['beginTime'];
$endTime = $_REQUEST['endTime'];
$beginser = $_REQUEST['beginser'];
$endser = $_REQUEST['endser'];
if (empty($_REQUEST['beginTime'])){
    $beginTime = strtotime(date('Y-m-d'));
    $endTime = strtotime(date('Y-m-d 23:59:59'));
}
if (empty($_REQUEST['beginser'])){
    $beginser = 23;
    $endser = 33;
}
set_time_limit(0);
Common::loadModel('ServerModel');
$serverList = ServerModel::getServList();
$data = array();
if (is_array($serverList)) {
    foreach ($serverList as $k => $v) {
        if (empty($v)) {
            continue;
        }
        if (!($v['id'] >= $beginser && $v['id'] <= $endser)) {
            continue;
        }

        $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
        if (999 == $SevidCfg1['sevid']) {
            continue;
        }
        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
            continue;
        }
        $db = Common::getMyDb();
        $sql = "SELECT DISTINCT `roleid`,sum(`money`) AS totalMoney FROM `t_order` WHERE `ptime`>={$beginTime} AND `ptime`<={$endTime} GROUP BY `roleid`";
        $result = $db->fetchArray($sql);
        if (!empty($result)){
            foreach ($result as $rk => $rv){
                $actModel = Master::getAct260($rv['roleid']);
                if ($rv['totalMoney']-$actModel->info['cons']>0){
                    echo '更新';
                    echo $rv['roleid'].'|'. $rv['totalMoney'].'|'.$actModel->info['cons'].'<br/>';
                    $num = $rv['totalMoney']-$actModel->info['cons'];
                    $actModel->add($num);
                    $actModel->ht_destroy();
                }
                echo $rv['roleid'].'|'. $rv['totalMoney'].'|'.$actModel->info['cons'].'<br/>';
                $data[$rv['roleid']] = $rv['totalMoney'];
                unset($actModel);
            }
        }
    }
}
?>