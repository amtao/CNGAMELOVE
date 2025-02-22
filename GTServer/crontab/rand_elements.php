<?php 
/**
 * 定时修改全服价格
 * 调用方式：00 22 * * *
 * 
 */
set_time_limit(0);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
$serverList = ServerModel::getServList();

$serverID = intval($_SERVER['argv'][1]);
if ($serverID == 999) {
    $serverID = 999;
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $Sev707Model = Master::getSev707();
    $Sev707Model->randElement();
    Common::loadModel("Master");
    Master::click_destroy();
    echo "999执行成功";

    exit();
}

Common::loadModel("Master");
if ( is_array($serverList) ) {
	foreach ($serverList as $k => $v) {
		if ( empty($v) ) {
			continue;
		}
		$SevidCfg = Common::getSevidCfg($v['id']);//子服ID
		echo PHP_EOL, '服务器ID：', $SevidCfg['sevid'], PHP_EOL;
		
		if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg['sevid'] ) {
			echo PHP_EOL, '>>>跳过', PHP_EOL;
			continue;
		}
		if ( 0 < $serverID && $serverID != $SevidCfg['sevid'] ) {
			echo PHP_EOL, '>>>跳过', PHP_EOL;
			continue;
		}
		if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
			&& $SevidCfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
			echo PHP_EOL, '>>>从服跳过', PHP_EOL;
			continue;
        }
        $Sev707Model = Master::getSev707();
        $Sev707Model->randElement();
	}
}
Master::click_destroy();

echo "执行成功";
exit();
