<?php 
/**
 * 定时删除脚本
 * 调用方式：每天凌晨4:30执行
 * 
 */
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
define('DELETE_MAIL_LIMIT_DAY', 7);
define('DELETE_ACODE_LIMIT_DAY', 7);
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
		DeleteModel::mailDelete($SevidCfg['sevid']);
		echo PHP_EOL, '>>>执行完毕。MailDeleteTimeCost=', (microtime(true) - $time) , PHP_EOL;
		
		
		if($SevidCfg['sevid'] == $defaultSid){
		    //删除兑换码
    		$time = microtime(true);
    		DeleteModel::acodeDetail($SevidCfg['sevid']);
    		echo PHP_EOL, '>>>执行完毕。AcodeDeleteTimeCost=', (microtime(true) - $time) , PHP_EOL;
    		//删除封设备数据
    		$time = microtime(true);
    		DeleteModel::fengsbDetail();
    		echo PHP_EOL, '>>>执行完毕。fengsbDeleteTimeCost=', (microtime(true) - $time) , PHP_EOL;
    		
		}
	}
}

echo PHP_EOL, '----------------end----------------------', PHP_EOL;
echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
exit();
