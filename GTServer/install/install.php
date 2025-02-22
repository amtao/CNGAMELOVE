<?php 
/**
 * 游戏服务器部署脚本
 * @author: wenyj
 * @version:
 * 		+ 20140422
 */
if ( !function_exists('system') ) {
	exit('function \'system\' is not available.');
}
define( 'ROOT_DIR' , dirname( __FILE__ ) . '/../' );


// 生成公共配置文件，注意确认environment_config.php文件中的服务器相关配置是正确的
$command = 'php ' . ROOT_DIR . 'install/batch_gen_files.php';
$result = system($command, $status);
echo PHP_EOL . '--------------------------------' . PHP_EOL;
echo 'command: ' . $command . PHP_EOL;
echo 'result: ' . $status . PHP_EOL;
echo PHP_EOL . '--------------------------------' . PHP_EOL;
if ( false === strpos($result, 'error') ) {
	// it's true,so pass
} else {
	exit($result);
}

require_once ROOT_DIR . 'install/environment_config.php';
// 初始化数据库
echo PHP_EOL . '-------------数据库表开始创建，可能时间有些长-------------------' . PHP_EOL;
include ROOT_DIR . 'init/init.php';
echo PHP_EOL . '-----------------数据库表创建完成---------------' . PHP_EOL;


exit('--end--');
