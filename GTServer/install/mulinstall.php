<?php 
/**
 * 游戏多服务器部署脚本
 * @author: wenyj
 * @version:
 * 		+ 20150602
 */
if ( !function_exists('system') ) {
	exit('function \'system\' is not available.');
}
define( 'ROOT_DIR' , dirname( __FILE__ ) . '/../' );

$path = dirname( __FILE__ ) . '/../';

// 传奇-android
$serverConf = include 'server_config.php';
if ( empty($serverConf) ) {
	exit('-------- server_config error --------------');
}

$MEMHOST = trim($serverConf['mem']['host']);
$MEMPORT = intval($serverConf['mem']['port']);
$REDISHOST = trim($serverConf['redis']['host']);
$DBHOST = trim($serverConf['db']['host']);
$DBPORT = trim($serverConf['db']['port']);
$DBUSER = trim($serverConf['db']['user']);
$DBPASSWD = trim($serverConf['db']['passwd']);

$server1 = intval($serverConf['sid0']);
$server2 = intval($serverConf['sid1']);
$redisPort0 = intval($serverConf['redis']['port']);

$count = $server2 - $server1;
if ( 0 >= $server2 || 0 >= $server1 || 0 > $count ) {
	exit('-------- server error --------------');
}

for ($i=0; $i<=$count; $i++) {
	$SERVER_ID = $server1 + $i;
	$REDISPORT = $redisPort0 + $i;
	$DBNAME = sprintf($serverConf['db']['name'], $SERVER_ID);
	$PREFIX_KEY = sprintf($serverConf['prefix'], $SERVER_ID);
	
	echo PHP_EOL, '>>> server:', $SERVER_ID, ';redisport:', $REDISPORT, ';dbname:', $DBNAME, ';prefix:', $PREFIX_KEY, PHP_EOL;
	
	include 'temp.environment_config.php';
	
	if ( empty($tempContent) ) {
		echo '>>>>>>  tempContent-error', PHP_EOL;
	}
	
	file_put_contents($path . 'install/environment_config.php', $tempContent);//FILE_APPEND
	$command = 'php ' . ROOT_DIR . 'install/install.php';
	$result = system($command, $status);
	echo '>>>>>>  command: ' . PHP_EOL . $command . PHP_EOL;
	echo '>>>>>>  result: ' . PHP_EOL . $status . PHP_EOL;
	if ( false === strpos($result, 'error') ) {
		// it's true,so pass
	} else {
		exit($result);
	}
}
exit('--end--');
