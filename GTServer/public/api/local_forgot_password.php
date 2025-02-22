<?php
/**
 * 自有账户重置密码接口
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

$email = trim($params['email']);
$now = strtotime('now');
$db = Common::getDftDb();


// 验证邮箱格式
if ( false == filter_var($email, FILTER_VALIDATE_EMAIL) ) {
	$result = array(
		'result' => 2000,
		'msg' => NOTE_ACCOUNT_EMAIL_INVALID,
	);
	exit(json_encode($result));
}

// 账户信息
$accountInfo = $db->fetchRow("select * from `local_account` where `email`='{$email}'");
if ( empty($accountInfo) ) {
	// 账户不存在
	$result = array(
		'result' => 2001,
		'msg' => NOTE_EMAIL_NO_BIND_ACCOUNT,
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

// 重置密码
$password = genRandString();
if ( $db->query("update `local_account` set `password`=md5('{$password}'), `utime`='{$now}' where `username`='{$accountInfo['username']}'") ) {
	
	// 邮件相关信息
	$mail_content = sprintf(MAIL_PASSWORD_RESET_CONTENT, date('Y-m-d H:i:s'), $password);
	
	// 加载phpmailer
	Common::loadLib('PHPMailer/class.phpmailer');
	$mail = new PHPMailer();//建立邮件发送类
	$mail->IsSMTP();// 使用SMTP方式发送
	$mail->Host = 'smtp.qq.com';// 您的企业邮局域名
	$mail->SMTPAuth = true;// 启用SMTP验证功能
	$mail->Username = 'account@youdong.com';// 邮局用户名(请填写完整的email地址)
	$mail->Password = 'youdong333';// 邮局密码
	$mail->Port = 25;// 默认端口号
	$mail->From = 'account@youdong.com';// 邮件发送者email地址
	$mail->FromName = 'account';// 发送者名称
	$mail->CharSet = 'utf-8';// 这里指定字符集
	$mail->Encoding = 'base64';
	$mail->AddAddress($email, $email);//收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
	$mail->Subject = '账号密码重置'; //邮件标题
	$mail->msgHTML($mail_content, dirname(__FILE__));// 按照html格式发送
	
	// 发送激活邮件
	if ( !$mail->Send() && MSDK_DEBUG ) {
		Common::logMsg($logfile, sprintf('==== send mail fail (%s)====case=%s', __LINE__, PHP_EOL . $mail->ErrorInfo));
	}
	
	// 成功
	$result = array(
		'result' => 1,
	);
	exit(json_encode($result));
} else {
	// 失败
	$result = array(
		'result' => 2003,
		'msg' => 'error_reset_password',
	);
	exit(json_encode($result));
}

$result = array(
	'result' => 0,
	'msg' => 'error',
);
exit(json_encode($result));

####################### Function ##################################

// 生成随机字符串
function genRandString($length=8, $type=4) {
	// 密码字符集，可任意添加你需要的字符
	switch ($type) {
		case 1:
			$chars = 'abcdefghijklmnopqrstuvwxyz';
			break;
		case 2:
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
		case 3:
			$chars = '0123456789';
			break;
		case 4:
		default:
			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			break;
	}
	$strlen = strlen($chars) - 1;
	$rand = '';
	for ( $i = 0; $i < $length; $i++ ) {
		// 这里提供两种字符获取方式
		// 第一种是使用 substr 截取$chars中的任意一位字符；
		// 第二种是取字符数组 $chars 的任意元素
		// $rand .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		$rand .= $chars[ mt_rand(0, $strlen) ];
	}
	return $rand;
}

