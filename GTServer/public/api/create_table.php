<?php
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
Common::loadModel('ServerModel');
$serverList = ServerModel::getServList();
if (is_array($serverList)) {
    foreach ($serverList as $k => $v) {
        if (empty($v)) {
            continue;
        }
        if (999 == $v['id']) {
            continue;
        }
        $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
            continue;
        }
        $db = Common::getMyDb();
        $sql = "alter table `device` modify column `uid` bigint(16);";
        $result = $db->query($sql);
        if ($result){
            echo $v['id'].'设备表修改成功';
        }
        unset($sql, $result);
    }
}
