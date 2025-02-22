<?php
require_once dirname(dirname(__FILE__)) . '/common.inc.php';
Common::loadModel('OrderModel');
Common::loadModel('ServerModel');
$serverList = ServerModel::getServList();
$begindt = strtotime(date('Y-m-d'));
$enddt = strtotime(date('Y-m-d'))+ 86400;
if (!empty($_REQUEST['begindt']) &&  !empty($_REQUEST['enddt'])){
    $begindt = strtotime($_REQUEST['begindt']);
    $enddt = strtotime($_REQUEST['enddt']);
}
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
        $sql0 = "select date_format(from_unixtime(`ptime`), '%Y-%m') as `dtime`,
            sum(`money`) as `rmbs`,`platform` from `t_order` 
            where `status`>0 and `ptime`>=" . $begindt . " and `ptime`<" . $enddt;

        $sql1 = $sql0 . ' group by `platform`,`dtime` order by `dtime`';
        $feizhichong = $db->fetchArray($sql1);
        if (is_array($feizhichong) && ! empty($feizhichong)) {
            foreach ($feizhichong as $k => $v) {
                $list[$v['platform']][$v['dtime']]['total'] += (float) $v['rmbs'];
            }
        }
        $sql2 = $sql0 . " and (`paytype`='zfb' or `paytype`='wx')  group by `platform`,`dtime` order by `dtime`";
        $zhichong = $db->fetchArray($sql2);
        if (is_array($zhichong) && ! empty($zhichong)) {
            foreach ($zhichong as $k => $v) {
                $list[$v['platform']][$v['dtime']]['zc'] += (float) $v['rmbs'];
            }
        }
    }
}
echo json_encode($list);