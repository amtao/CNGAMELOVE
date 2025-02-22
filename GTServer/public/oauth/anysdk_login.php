<?php
/*
 * anysdk框架会在渠道客户端登录成功时会将渠道登录成功返回的验证信息通知到游戏服务器上这个脚本，
 * 需要将通知的内容全部返回给anysdk服务器上，获取anysdk上的登录信息之后返回响应给客户端，
 * 如果游戏逻辑需要可以在返回的ext域中存放游戏逻辑相关的数据（比如开发商服务器内部设定用户标识），
 * 这些数据会在游戏客户端获取到登录成功回调时附带的msg信息里完整的拿到，然后用来执行相应的游戏逻辑
 */
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';

if ( !defined('MSDK_DEBUG') ) {
	define('MSDK_DEBUG', true);
}

// 记录request参数
$logfile = LOG_PATH . strtr(basename(__FILE__), array('.'=>'_')) . date('Ymd') . '.log';
$params = $_REQUEST;
if ( MSDK_DEBUG ) {
	Common::logMsg($logfile, sprintf('==== request (%s)====%s', __LINE__, PHP_EOL . $_SERVER ['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . PHP_EOL . var_export($params, 1)));
}

if ( !(isset($params['channel']) && isset($params['uapi_key']) && isset($params['uapi_secret'])) ) {
	exit('parameter not complete');
}

$url = 'http://oauth.anysdk.com/api/User/LoginOauth/';
$reslut = Common::request($url, $params);

if ( MSDK_DEBUG ) {
	Common::logMsg($logfile, sprintf('==== response (%s)====%s', __LINE__, PHP_EOL . $url . PHP_EOL . var_export($reslut, 1)));
}
if ( empty($reslut) ) {
	exit('loginOauth response empty');
}
/* 
$reslut = json_decode($reslut, true);
if ( MSDK_DEBUG ) {
	Common::logMsg($logfile, sprintf('==== response-jsondecode =====%s', __LINE__, PHP_EOL . var_export($reslut, 1)));
}
if ( 'ok' != $reslut['status'] ) {
	exit('login fail.' . $reslut['data']['error']);
}
$reslut['ext'] = '';// 如果有需要透传给客户端的参数可以通过这个值处理
$reslut = json_encode($reslut);
*/
exit($reslut);
