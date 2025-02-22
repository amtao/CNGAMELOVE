<?php
/**
 * @author wenyj
 *
 * 根据平台配置，生成后台数据统计配置文件并支持批量拷贝指定平台配置文件
 */
header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Mon, 9 May 1983 09:00:00 GMT" );
header( 'P3P: CP="CAO PSA OUR"' );
header( "Content-type: text/html; charset=utf-8" );
define( 'D_S' , DIRECTORY_SEPARATOR);  //DIRECTORY_SEPARATOR  : php内部表示反斜杠的意思 '/'
define( 'GAMEURL' , dirname(dirname( __FILE__ )) .D_S);
include  GAMEURL.'config.php';
define( 'SNS_PLATFORM_DIR',GAMEURL.'config'.D_S.'platform'.D_S );

echo '----------------回调配置开始---------------',PHP_EOL;

if(!file_exists(SNS_PLATFORM_DIR.GAME_MARK.D_S.'platform_config.php')){
	echo SNS_PLATFORM_DIR.GAME_MARK.D_S.'platform_config.php', PHP_EOL;
	echo 'platform_config.php not exist', PHP_EOL;
	exit();
}

$platform_config = include SNS_PLATFORM_DIR.GAME_MARK.D_S.'platform_config.php';
if(empty($platform_config)){
	echo 'platform_config为空', PHP_EOL;
	exit();
}
foreach($platform_config as $platform => $pfinfo){
	$str = '';
	$cfg_name = $platform.'_cfg';
	foreach($pfinfo['define'] as $k => $v){
		$str .= "define('{$k}', {$v});".PHP_EOL;
	}
	file_put_contents(SNS_PLATFORM_DIR . $cfg_name . '.php', $str);//FILE_APPEND
	
	if(!file_exists(SNS_PLATFORM_DIR . $cfg_name . '.php')){
		echo $cfg_name . '.php----error', PHP_EOL;
		exit();
	}else{
		echo $cfg_name . '.php----ok', PHP_EOL;
	}
}
echo '----------------回调配置完成---------------',PHP_EOL;
exit();

