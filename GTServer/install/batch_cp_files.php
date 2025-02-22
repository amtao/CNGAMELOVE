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
define( 'ROOT_DIR', dirname(dirname( __FILE__ )) . DIRECTORY_SEPARATOR );
if ( !defined('IN_INU') ) define( 'IN_INU', true );
include ROOT_DIR . 'config.php';
require_once ROOT_DIR . 'lib/Function.common.php';

$gamealias = GAME_MARK;

if ( !file_exists(ROOT_DIR . 'config/platform/platform_config.php') ) {
	echo 'platform_config.php not exist', PHP_EOL;
	exit('-some thing error-' . PHP_EOL);
}
$platform_config = include ROOT_DIR . 'config/platform/platform_config.php';

if ( !file_exists(ROOT_DIR . "config/platform/{$gamealias}/platform_environment_config.php") ) {
	echo 'platform_environment_config.php not exist', PHP_EOL;
	exit('-some thing error-' . PHP_EOL);
}
$platform_environment_config = include ROOT_DIR . "config/platform/{$gamealias}/platform_environment_config.php";
if ( !is_array($platform_environment_config) || empty($platform_environment_config) ) {
	echo 'platform_environment_config.php not exist', PHP_EOL;
	exit('-some thing error-' . PHP_EOL);
}

$specifyArr = array();//指定生成的平台，在新平台上线时使用
$environment_conf = array();

echo '-------------begin-gen-platfromdb-conf-----------------' . PHP_EOL;
if ( is_array($platform_config) ) {
	foreach ( $platform_config as $platform => $v) {
		/*
		if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && AGENT_CHANNEL_ALIAS != $v['channel'] ) {
			continue;
		}
		*/
		// cp config
		if ( !empty($specifyArr) && !in_array($platform, $specifyArr) ) {
			continue;
		}
		// 通过配置动态生成平台配置
		$tmp = 'config/platform/' . $platform . '_config.php';
		if ( isset($platform_environment_config[$platform]) ) {
			$environment_conf[$tmp] = $platform_environment_config[$platform];
		}
		// 通过配置动态生成平台子类的回调处理文件
		if ( isset($v['extends']) ) {
			$tmp = 'public/pay/' . $platform . '_callback.php';
			if ( isset($platform_environment_config[$platform]) ) {
				$environment_conf[$tmp] = array(
					'define' => array(
						'SNS' => "'{$platform}'",
					),
					'load' => array(
						sprintf("dirname( __FILE__ ) . '/%s_callback.php'", $v['extends']),
					),
				);
			}
		}
		
	}
}

if ( is_array($environment_conf) ) {
	foreach ( $environment_conf as $filename => $fileset) {
		$data = '<?php ' . PHP_EOL;
		
		if ( is_array($fileset) ) {
			foreach ($fileset as $type => $conf) {
				switch ($type) {
					case 'define':
						$data .= str_define($conf);
						break;
					case 'defined':
						$data .= str_defined($conf);
						break;
					case 'load':
						$data .= str_load($conf);
						break;
					case 'return':
						$data .= str_return($conf);
						break;
				}
			}
		}
		
		file_put_contents(ROOT_DIR . $filename, $data);//FILE_APPEND
		if ( !file_exists(ROOT_DIR . $filename) ) {
			echo 'file not exist: ', ROOT_DIR . $filename, PHP_EOL;
			exit('-some thing error-' . PHP_EOL);
		} else {
			echo 'file:', ROOT_DIR . $filename, ' ok.' , PHP_EOL;
		}
	}
}

echo '-------------end-gen-platfromdb-conf-----------------' , PHP_EOL;

exit();
