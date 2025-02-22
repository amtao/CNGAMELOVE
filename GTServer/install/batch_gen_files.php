<?php
header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Mon, 9 May 1983 09:00:00 GMT" );
header( 'P3P: CP="CAO PSA OUR"' );
header( "Content-type: text/html; charset=utf-8" );
$path = dirname( __FILE__ ) . '/../';
require_once $path . 'lib/Function.common.php';

$environment_conf = include $path . 'install/environment_config.php';

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
		
		file_put_contents($path . $filename, $data);//FILE_APPEND
		if ( !file_exists($path . $filename) ) {
			echo 'file not exist: ', $path . $filename, PHP_EOL;
			exit('-some thing error-' . PHP_EOL);
		} else {
			echo 'file:', $path . $filename, ' ok.' , PHP_EOL;
		}
	}
}