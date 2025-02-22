<?php 
error_reporting(E_ALL & ~E_NOTICE);
define( 'ROOT_DIR', dirname(dirname(dirname( __FILE__ ))) );
require_once ROOT_DIR . '/config.php';

if ( !defined('IN_INU') ) define( 'IN_INU', true );
define( 'CONFIG_DIR', ROOT_DIR . '/config' );
define( 'MOD_DIR', ROOT_DIR . '/model' );
define( 'API_DIR', ROOT_DIR . '/api' );
define( 'LIB_DIR', ROOT_DIR . '/lib' );
$SevidCfg = Common::getSevidCfg(1);//子服ID
require_once LIB_DIR . '/Core.php';
require_once LIB_DIR . '/MemcachedClass.php';
require_once API_DIR . '/Base.php';
Common::loadModel('CommonModel');

$params = $_REQUEST;
logDebug(var_export($params, 1));

$sns = 'youdong';
$openid = $sns . '_' . intval($_REQUEST['userid']);// 用户平台账户id，统一作为官方包用户
$sid = intval($_REQUEST['sid']);// 区服id
$money = floatval($_REQUEST['money']);// 订单金额
$orderno = trim($_REQUEST['orderno']);// 订单标识
$paytype = trim($_REQUEST['paytype']);// 订单类型
$ybNum = intval($money * 10);// 等价元宝数量
$currenttime = strtotime('now');
// 重定向请求到子分区

$servers_set = CommonModel::getValue('server_list');

$serverconf = json_decode($servers_set, 1);
if ( null == $serverconf ) {
	$serverconf = array();
}

if ( !empty($serverconf[$sid]) ) {
	$url = $serverconf[$sid]['domain'] . '/api/tbzc_callback_main.php';
	// 记录日志
	logDebug($url,$sid);
	$rs = Common::request($url, $params);
	print_r($rs);
	logDebug($rs,$sid);
} else {
	exitError(__LINE__, 'fail');
}

/**
 * @return json
 */
function exitError($errid, $errmsg='') {
	$error = array(
		'errcode' => $errid,
		'errMsg' => $errmsg,
	);
	logDebug(var_export($error, 1),$sid);
	exit($errmsg);
}

// 记录日志
function logDebug($msg,$sid) {
	$msg = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . ' : ' . $msg . PHP_EOL;
	Common::logMsg(LOG_PATH . SNS . '_' . $sid . '_' . strtr(basename(__FILE__), array('.'=>'_')) . date('Ymd') . '.log', $msg);
}
