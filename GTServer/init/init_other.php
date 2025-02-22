<?php
/**
 * 服务器环境部署
 * @author wenyj
 * temp
 */

header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Mon, 9 May 1983 09:00:00 GMT" );
header( 'P3P: CP="CAO PSA OUR"' );
header( "Content-type: text/html; charset=utf-8" );


//服务器ID
$sevid = intval($_SERVER['argv'][1]);
//  EPZJFHOVERGAT  EPZJFH  EPGTMZCH
define('PREFIX_KEY', 'EPZJFHOVERGAT' . 'S' . $sevid . 's');//-------------要修改============
$fine_conf = array(
	// 全局公共配置，定义常量
	'public/servers/s' . $sevid . '_config.php' => array(
		'define' => array(
			'SERVER_ID' => $sevid,
			'MEMCACHED_PREFIX_KEY' => "'" . PREFIX_KEY . "'",// 多服务器共用缓存私钥
		),
	),
	'public/servers/s' . $sevid . '.php' => array(
		'load' => array(
			sprintf("dirname(dirname(dirname( __FILE__ ))) . '/public/servers/s%s_config.php'", $sevid),
			"dirname(dirname(dirname( __FILE__ ))) . '/public/cmd.php'",
		),
	),
	
);




$path = dirname( __FILE__ ) . '/../';
require_once $path . 'lib/Function.common.php';


foreach ( $fine_conf as $filename => $fileset) {
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
	
	file_put_contents($path . $filename, $data);//FILE_APPEND
	if ( !file_exists($path . $filename) ) {
		echo 'file not exist: ', $path . $filename, PHP_EOL;
		exit('-some thing error-' . PHP_EOL);
	} else {
		echo 'file:', $path . $filename, ' ok.' , PHP_EOL;
	}
}
