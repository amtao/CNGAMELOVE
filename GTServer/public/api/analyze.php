<?php
/**
 * 数据统计接口，供外部调用
 * @author : wenyj
 * @version :
 *   + 20150413, init
 */
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';

Common::loadModel('ServerModel');
Common::loadModel('OrderModel');

$serverList = ServerModel::getServList();

//echo var_export($serverList, 1);
//$_SERVER['REQUEST_TIME'] = strtotime('yesterday');
$defaultTime = strtotime('today');
$beginTime = (empty($_REQUEST['begindt'])) ? $defaultTime : strtotime($_REQUEST['begindt']);
$endTime = (empty($_REQUEST['enddt'])) ? $defaultTime + 86400 : strtotime($_REQUEST['enddt'] . ' +1 days');
$totalConsume = 0;

$sql = sprintf("select sum(`realmoney`) as `money` from `t_order` 
	where `status`>='%s' and `ctime`>='%s' and `ctime`<'%s'", 
	OrderModel::STATUS_TRANSFER, $beginTime, $endTime);

//echo PHP_EOL, '----------------begin----------------------', PHP_EOL;
//echo PHP_EOL, 'sql==', $sql, PHP_EOL;

if ( is_array($serverList) ) {
	$maxServerID = (defined('PASS_SEV_CRONTAB_MAXID') && 0 < PASS_SEV_CRONTAB_MAXID) ? PASS_SEV_CRONTAB_MAXID : 0;
	
	foreach ($serverList as $k => $v) {
		if ( empty($v) ) {
			continue;
		}
		
		$SevidCfg = Common::getSevidCfg($v['index']);//子服ID
		
		if ( 0 < $maxServerID && $SevidCfg['sevid'] > $maxServerID ) {
			continue;
		}

		if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg['sevid'] ) {
			//echo PHP_EOL, '>>>跳过', PHP_EOL;
			continue;
		}

		$db = Common::getMyDb();
		$record = $db->fetchRow($sql);
		$totalConsume += floatval($record['money']);

		//echo PHP_EOL, '>>>执行完毕。', PHP_EOL;
	}
}
echo $totalConsume;

//echo PHP_EOL, '----------------end----------------------', PHP_EOL;
exit();

