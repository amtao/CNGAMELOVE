<?php 
/**
 * 定时删除流水脚本
 * 调用方式：每天凌晨4:30执行
 * 
 */
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('DeleteModel');

$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
echo PHP_EOL, 'serverID=', $serverID, PHP_EOL;

$serverList = ServerModel::getServList();
$defaultSid = ServerModel::getDefaultServerId();
$btime = microtime(true);
echo PHP_EOL, '----------------默认服务器'.$defaultSid.'----------------------', PHP_EOL;
echo PHP_EOL, '----------------begin----------------------', PHP_EOL;

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
		
		// 删除邮件
		$time = microtime(true);
		DeleteModel::flowDelete($SevidCfg['sevid']);
		echo PHP_EOL, '>>>执行完毕。flowDeleteTimeCost=', (microtime(true) - $time) , PHP_EOL;
	}
}

echo PHP_EOL, '----------------end----------------------', PHP_EOL;
echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
exit();
