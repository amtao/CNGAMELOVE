<?php
/**
 * 自有账户登录接口，区别于官方帐户系统，账户只对单独游戏有效
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

$loginType = strtolower(trim($params['logintype']));// 登录方式
$account = trim($params['account']);// 对应登录方式的账户
$type = $params['type'];// 暂时针对email登录方式，1=表示注册
$now = strtotime('now');
$db = Common::getMyDb();

// type = local
if ( 'local' == $loginType ) {
	// account is empty, need to create a new account
	if ( empty($account) ) {
		$username = md5(md5(AGENT_CHANNEL_ALIAS . genRandString() . microtime()));
		if ( $db->query("insert into `local_account` (`username`, `ctime`) values ('{$username}', '{$now}')") ) {
			// 插入成功
			$result = array(
				'result' => 1,
				'data' => array(
					'account' => $username,
					'bindFB' => false,
					'bindEmail' => false,
				),
			);
			exit(json_encode($result));
		} else {
			// 插入失败
			$result = array(
				'result' => 2001,
				'msg' => NOTE_ACCOUNT_LOGIN_1,
			);
			exit(json_encode($result));
		}
	}
	// account is not empty, check it valid
	else {
		$accountInfo = $db->fetchRow("select * from `local_account` where `username`='{$account}'");
		if ( empty($accountInfo) ) {
			// 账户不存在
			$result = array(
				'result' => 2002,
				'msg' => NOTE_ACCOUNT_LOGIN_2,
			);
			exit(json_encode($result));
		} elseif ( '0' != $accountInfo['status'] ) {
			// 账户被冻结
			$result = array(
				'result' => 2003,
				'msg' => NOTE_ACCOUNT_LOGIN_3,
			);
			exit(json_encode($result));
		} else {
			$result = array(
				'result' => 1,
				'data' => array(
					'account' => $account,
					'bindFB' => empty($accountInfo['fbid']) ? false : true,
					'bindEmail' => empty($accountInfo['email']) ? false : true,
				),
			);
			exit(json_encode($result));
		}
	}
}

if ( empty($account) ) {
	$result = array(
		'result' => 2010,
		'msg' => NOTE_ACCOUNT_NAME_IS_NULL,
	);
	exit(json_encode($result));
}

// type = fb
if ( 'fb' == $loginType ) {
	$accountInfo = $db->fetchRow("select * from `local_account` where `fbid`='{$account}'");
	if ( empty($accountInfo) ) {
		$username = md5(md5(AGENT_CHANNEL_ALIAS . genRandString() . microtime()) . $account);
		if ( $db->query("insert into `local_account` (`username`, `ctime`, `fbid`) values ('{$username}', '{$now}', '{$account}')") ) {
			// 插入成功
			$result = array(
				'result' => 1,
				'data' => array(
					'account' => $username,
					'bindFB' => true,
					'bindEmail' => false,
				),
			);
			exit(json_encode($result));
		} else {
			// 插入失败
			$result = array(
				'result' => 2101,
				'msg' => NOTE_ACCOUNT_LOGIN_1,
			);
			exit(json_encode($result));
		}
	} elseif ( '0' != $accountInfo['status'] ) {
		// 账户被冻结
		$result = array(
			'result' => 2102,
			'msg' => NOTE_ACCOUNT_LOGIN_3,
		);
		exit(json_encode($result));
	} else {
		$result = array(
			'result' => 1,
			'data' => array(
				'account' => $accountInfo['username'],
				'bindFB' => true,
				'bindEmail' => empty($accountInfo['email']) ? false : true,
			),
		);
		exit(json_encode($result));
	}
}

// type = email
if ( 'email' == $loginType ) {
	// 验证邮箱格式
	if ( false == filter_var($account, FILTER_VALIDATE_EMAIL) ) {
		$result = array(
			'result' => 2011,
			'msg' => NOTE_ACCOUNT_EMAIL_INVALID,
		);
		exit(json_encode($result));
	}
	$password = trim($params['password']);
	$accountInfo = $db->fetchRow("select * from `local_account` where `email`='{$account}'");
    // type=1，注册邮箱
    if ( '1' == $type ) {
        if ( empty($accountInfo) ) {
            $username = md5(md5(AGENT_CHANNEL_ALIAS . genRandString() . microtime()) . $account);
    		if ( $db->query("insert into `local_account` (`username`, `ctime`, `email`, `password`) values ('{$username}', '{$now}', '{$account}', md5('{$password}'))") ) {
    			// 插入成功
    			$result = array(
    				'result' => 1,
    				'data' => array(
    					'account' => $username,
    					'bindFB' => false,
    					'bindEmail' => true,
    				),
    			);
    			exit(json_encode($result));
    		} else {
    			// 插入失败
    			$result = array(
    				'result' => 2201,
    				'msg' => NOTE_ACCOUNT_LOGIN_1,
    			);
    			exit(json_encode($result));
    		}
        } else {
            // 账户存在
        	$result = array(
        		'result' => 2201,
        		'msg' => NOTE_EMAIL_BINDED_ACCOUNT,
        	);
        	exit(json_encode($result));
        }
    } else {
        if ( empty($accountInfo) ) {
            // 账户不存在
			$result = array(
				'result' => 2201,
				'msg' => NOTE_ACCOUNT_LOGIN_2,
			);
			exit(json_encode($result));
    	} elseif ( '0' != $accountInfo['status'] ) {
    		// 账户被冻结
    		$result = array(
    			'result' => 2202,
    			'msg' => NOTE_ACCOUNT_LOGIN_3,
    		);
    		exit(json_encode($result));
    	} elseif ( md5($password) != trim($accountInfo['password']) ) {
    		// 密码不对应
    		$result = array(
    			'result' => 2203,
    			'msg' => NOTE_ACCOUNT_PASSWORD_INVALID,
    		);
    		exit(json_encode($result));
    	} else {
    		$result = array(
    			'result' => 1,
    			'data' => array(
    				'account' => $accountInfo['username'],
    				'bindFB' => empty($accountInfo['fbid']) ? false : true,
    				'bindEmail' => true,
    			),
    		);
    		exit(json_encode($result));
    	}
    }
	
}

$result = array(
	'result' => 0,
	'msg' => 'error',
);
exit(json_encode($result));

####################### Function ##################################

// 生成随机字符串
function genRandString($length=8) {
	$rand = '';
	for ($i = 0; $i < $length; $i++) {
		$rand .= chr(mt_rand(33, 126));
	}
	return $rand;
}

