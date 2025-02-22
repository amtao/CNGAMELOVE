<?php
/**
 * 自有账户绑定接口，区别于官方帐户系统，账户只对单独游戏有效
 * @author wenyj
 * @version
 *  - 20150723, init
 */
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';

/*
if ( !defined('MSDK_DEBUG') ) {
	define('MSDK_DEBUG', true);
}
*/

// 记录request参数
$logfile = LOG_PATH . strtr(basename(__FILE__), array('.'=>'_')) . date('Ymd') . '.log';
$params = $_REQUEST;
if ( MSDK_DEBUG ) {
	Common::logMsg($logfile, sprintf('==== request (%s)====%s', __LINE__, PHP_EOL . $_SERVER ['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . PHP_EOL . var_export($params, 1)));
}

$type = strtolower(trim($params['type']));// 登录方式
$account = trim($params['account']);// 默认账户
$bindID = trim($params['bindid']);// fbid or email
$now = strtotime('now');
$db = Common::getMyDb();

// 账户信息
$accountInfo = $db->fetchRow("select * from `local_account` where `username`='{$account}'");
if ( empty($accountInfo) ) {
	// 账户不存在
	$result = array(
		'result' => 2001,
		'msg' => NOTE_ACCOUNT_LOGIN_2,
	);
	exit(json_encode($result));
} elseif ( '0' != $accountInfo['status'] ) {
	// 账户被冻结
	$result = array(
		'result' => 2002,
		'msg' => NOTE_ACCOUNT_LOGIN_3,
	);
	exit(json_encode($result));
}

// 绑定facebook
if ( 'bindfb' == $type ) {
	if ( !empty($accountInfo['fbid']) ) {
		$result = array(
			'result' => 3001,
			'msg' => NOTE_ACCOUNT_BINDED_FACEBOOK,
		);
		exit(json_encode($result));
	}
	if ( 0 < $db->getCount('local_account', "`fbid`='{$bindID}'") ) {
		$result = array(
			'result' => 3002,
			'msg' => NOTE_FACEBOOK_BINDED_ACCOUNT,
		);
		exit(json_encode($result));
	}
	if ( $db->query("update `local_account` set `fbid`='{$bindID}', `utime`='{$now}' where `username`='{$account}'") ) {
		// 成功
		$result = array(
			'result' => 1,
		);
		exit(json_encode($result));
	} else {
		// 失败
		$result = array(
			'result' => 3003,
			'msg' => NOTE_ACCOUNT_BIND_1,
		);
		exit(json_encode($result));
	}
}

// 解绑facebook
if ( 'unbindfb' == $type ) {
	if ( empty($accountInfo['fbid']) ) {
		$result = array(
			'result' => 4001,
			'msg' => NOTE_ACCOUNT_FB_NO_BIND,
		);
		exit(json_encode($result));
	}

	if ( $bindID != $accountInfo['fbid'] ) {
		$result = array(
			'result' => 4002,
			'msg' => NOTE_ACCOUNT_BIND_3,
		);
		exit(json_encode($result));
	}
	if ( $db->query("update `local_account` set `fbid`='', `utime`='{$now}' where `username`='{$account}'") ) {
		// 成功
		$result = array(
			'result' => 1,
		);
		exit(json_encode($result));
	} else {
		// 失败
		$result = array(
			'result' => 4003,
			'msg' => NOTE_ACCOUNT_BIND_2,
		);
		exit(json_encode($result));
	}
}

// 绑定email
if ( 'bindemail' == $type ) {
	// 验证邮箱格式
	if ( false == filter_var($bindID, FILTER_VALIDATE_EMAIL) ) {
		$result = array(
			'result' => 5000,
			'msg' => NOTE_ACCOUNT_EMAIL_INVALID,
		);
		exit(json_encode($result));
	}
	if ( !empty($accountInfo['email']) ) {
		$result = array(
			'result' => 5001,
			'msg' => NOTE_ACCOUNT_BINDED_EMAIL,
		);
		exit(json_encode($result));
	}
	if ( 0 < $db->getCount('local_account', "`email`='{$bindID}'") ) {
		$result = array(
			'result' => 5002,
			'msg' => NOTE_EMAIL_BINDED_ACCOUNT,
		);
		exit(json_encode($result));
	}
	$password = trim($params['password']);
	if ( $db->query("update `local_account` set `email`='{$bindID}', `password`=md5('{$password}'), `utime`='{$now}' where `username`='{$account}'") ) {
		// 成功
		$result = array(
			'result' => 1,
		);
		exit(json_encode($result));
	} else {
		// 失败
		$result = array(
			'result' => 5003,
			'msg' => NOTE_ACCOUNT_BIND_1,
		);
		exit(json_encode($result));
	}
}

// 解绑email
if ( 'unbindemail' == $type ) {
	// 验证邮箱格式
	if ( false == filter_var($bindID, FILTER_VALIDATE_EMAIL) ) {
		$result = array(
			'result' => 6000,
			'msg' => NOTE_ACCOUNT_EMAIL_INVALID,
		);
		exit(json_encode($result));
	}
	if ( empty($accountInfo['email']) ) {
		$result = array(
			'result' => 6001,
			'msg' => NOTE_ACCOUNT_EMAIL_NO_BIND,
		);
		exit(json_encode($result));
	}

	if ( $bindID != $accountInfo['email'] ) {
		$result = array(
			'result' => 6002,
			'msg' => NOTE_ACCOUNT_BIND_4,
		);
		exit(json_encode($result));
	}
	if ( md5(trim($params['password'])) != $accountInfo['password'] ) {
		$result = array(
			'result' => 6003,
			'msg' => NOTE_ACCOUNT_PASSWORD_INVALID,
		);
		exit(json_encode($result));
	}
	if ( $db->query("update `local_account` set `email`='', `password`='', `utime`='{$now}' where `username`='{$account}'") ) {
		// 成功
		$result = array(
			'result' => 1,
		);
		exit(json_encode($result));
	} else {
		// 失败
		$result = array(
			'result' => 6004,
			'msg' => NOTE_ACCOUNT_BIND_2,
		);
		exit(json_encode($result));
	}
}

$result = array(
	'result' => 0,
	'msg' => 'error',
);
exit(json_encode($result));
