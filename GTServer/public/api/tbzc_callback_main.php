<?php

error_reporting(E_ALL & ~E_NOTICE);
header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Mon, 9 May 1983 09:00:00 GMT" );
header( 'P3P: CP="CAO PSA OUR"' );
header( "Content-type: text/html; charset=utf-8" );
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

$sns = 'youdong';
$openid = $sns . '_' . intval($_REQUEST['userid']);// 用户平台账户id，统一作为官方包用户
$sid = intval($_REQUEST['sid']);// 区服id
$money = floatval($_REQUEST['money']);// 订单金额
$orderno = trim($_REQUEST['orderno']);// 订单标识
$paytype = trim($_REQUEST['paytype']);// 订单类型
$ybNum = intval($money * 10);// 等价元宝数量
$currenttime = strtotime('now');
$SevidCfg = Common::getSevidCfg($sid);//子服ID
$params = $_REQUEST;
logDebug(var_export($params, 1),$sid);
//-------------------------------------------
Common::loadModel('OrderModel');
$OrderModel = new OrderModel($uid);
// 验证平台订单是否存在
$orderInfo = OrderModel::getInfoByTradeno($orderno);
if ( is_array($orderInfo) && 0 <= $orderInfo['status'] ) {
	echo "ok";
	exit;
}
//订单写入，订单状态改为0，不要混淆真实订单
$uids = Common::getUid($openid);//获取UID
$uid = $uids['uid'];
$order = array(
		'status' => 0,
		'openid' => $openid,
		'roleid' => $uid,
		'realmoney' => $_REQUEST['money'],
		'idealmoney' => $ybNum,
		'itemtype' => 1,
		'itemnum' => 1,
		'itemid' => 2,
		'platform' => 'local',
		'channal' => 0,
		'ptime' => $_SERVER['REQUEST_TIME'],
		'utime' => $_SERVER['REQUEST_TIME'],
		'tradeno' => $_REQUEST['orderno'],
		'paytype' => $_REQUEST['paytype'],
);
$OrderModel->newOrder($order);
//-------------------------------------------
//创建用户并加钱
function createUser($openid,$ybNum) {
	$uids = Common::getUid($openid);//获取UID
	$uid = $uids['uid'];
	Common::loadModel('UserModel');
	$UserModel = new UserModel($uid);
	if ( !empty($UserModel->info) && empty($UserModel->info['uid']) ) {
		$UserModel->clear_mem();// 删除缓存 重新生成
		print 'clear_mem'.PHP_EOL;
	}
	if (empty($UserModel->info['uid'])) {//新用户
		//初始化新用户
		$UserModel->newuser(array(
				'uid' => $uid,
				'channel_id' => 0,
				'platform' => 'local',
				'lang' => '',
		));
	}
	if ($UserModel->info['step'] == 1){//取名字
		$rand_name = Common::getLang('rand_name');
		while(1){
			$name = '';
			for ($i=0 ; $i < $rand_name['len'] ; $i++){
				$name .= $rand_name['names'][$i][array_rand($rand_name['names'][$i])];
			}
			if (Game::chick_name($uid,$name)){
				break;
			}
		}
		$u_update = array(
				'name' => $name,
				'job' => rand(1,3),
				'sex' => rand(1,2),
				'step' => 5,
				'pvetime' => $_SERVER['REQUEST_TIME'],
		);
	}
	//加等级 ＋ 物品
	//计算等级所需经验值
	if ($UserModel->info['level'] < 10){
		$level = rand(10,50);//10到50级
		$exp_cfg = Game::getcfg_byid('cfg_exp','lv');
		if (isset($exp_cfg[$level])){
			$u_update['exp'] = $exp_cfg[$level]['allexp'] - $UserModel->info['exp'];
			if ($UserModel->info['level'] > $level){
				$u_update['level'] = $level;
			}
		}
	}
	$huodong = array (
			'battle' => 1,
			'map' => 1,
			'skill' => 2,
			'partner' => 1,
			'boss' => 9,
			'delequipnew' => 1,
			'newskill' => 0,
			'element' => 1,
			'senior' => 1,
			'pva' => 1,
	);
	$UserModel->setActivitys('guide', $huodong);
	$UserModel->add_vip_pay(intval($ybNum));
	$UserModel->update($u_update);
	$UserModel->additem(2,$ybNum);
	$UserModel->good_morning();
	$UserModel->destroy();
}
createUser($openid,$ybNum);//创建用户并加钱
//--------------------------------------------
/**
 * @return json
 */
function exitError($errid, $errmsg='') {
	$error = array(
			'errcode' => $errid,
			'errMsg' => $errmsg,
	);
	logDebug(var_export($error, 1));
	exit($errmsg);
}

// 记录日志
function logDebug($msg,$sid) {
	$msg = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . ' : ' . $msg . PHP_EOL;
	Common::logMsg(LOG_PATH . SNS . '_' . $sid . '_' . strtr(basename(__FILE__), array('.'=>'_')) . date('Ymd') . '.log', $msg);
}
echo  "ok";