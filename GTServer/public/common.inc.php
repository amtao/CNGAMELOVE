<?php
/**
 * 通用包含
 * @author: wenyj
 * @version
 * 	- 20141105
 */
header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Mon, 9 May 1983 09:00:00 GMT" );
header( 'P3P: CP="CAO PSA OUR"' );
header( "Content-type: text/html; charset=utf-8" );
header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
#header( "Access-Control-Allow-Origin:*" );

if (defined("DEBUG_EVN") && DEBUG_EVN) {
    error_reporting(E_ALL^E_NOTICE);
    ini_set('display_errors', 'On');//debug环境直接输出错误
}
else {
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    ini_set('error_log', '/data/logs/php_error_'.date("Ymd").'.log');
}

//session_start();
define( 'ROOT_DIR' , dirname( dirname( __FILE__ ) ) );
define( 'CONFIG_DIR', ROOT_DIR . '/config' );
define( 'MOD_DIR', ROOT_DIR . '/model' );
define( 'LIB_DIR', ROOT_DIR . '/lib' );
define( 'API_DIR', ROOT_DIR . '/api' );
define( 'CONTROLLER_DIR', ROOT_DIR . '/controller' );
define( 'PUBLIC_DIR', ROOT_DIR . '/public' );
require_once ROOT_DIR . '/config.php';
require_once LIB_DIR . '/MemcachedClass.php';
$languageTag = defined("DEFAULT_LANG") ? DEFAULT_LANG : "zh";
require_once ROOT_DIR . "/lang/{$languageTag}/language.php";
// 没有指定服务器id的情况下默认以1服作为入口服读取
if ( !defined('SERVER_ID') ) {
	$_SERVER_ID = ( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) ? 999 : 1;
} else {
	$_SERVER_ID = intval(SERVER_ID);
}

$serCfgFile = ROOT_DIR . sprintf('/public/servers/s%s_config.php', $_SERVER_ID);
if ( !file_exists($serCfgFile) ) {
	exit($serCfgFile . ' not exist');
}
require_once $serCfgFile;
require_once LIB_DIR . '/Core.php';
require_once API_DIR . '/Base.php';
require_once CONTROLLER_DIR . '/PlatformBase.php';
Common::loadModel('Master');
//指定哪些皮使用数据库存储活动配置
$gameActFromDBPF = array(
    'xianyu','gt_kt'
);
if (in_array(GAME_MARK, $gameActFromDBPF) && !defined('SWITCH_GAME_ACT_FROM_DB')) {
    define('SWITCH_GAME_ACT_FROM_DB', true);
}
$gameActFromDBPF = array('ypdckw');
if (in_array(GAME_MARK, $gameActFromDBPF) && !defined('SWITCH_GAME_CONFIG_FROM_DB')) {
    define('SWITCH_GAME_CONFIG_FROM_DB', true);
}
require_once LIB_DIR. '/TaPhpSdk.php';