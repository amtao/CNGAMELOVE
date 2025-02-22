<?php
/**
 * 服务器环境部署
 * @author wenyj
 * temp
 */

define('SERVER_ID', '1');// server id
define('MEMHOST', '10.66.229.204');// memcache host
define('MEMPORT', '9101');// memcache port
define('REDISHOST', '10.66.229.210');// redis host
define('REDISPORT', '6379');// redis port
define('REDISPASS', 'crs-8plmzld9:gjypmzyw@zhisnet');// redis port
define('DBHOST', '10.66.220.143');// db host
define('DBPORT', '3306');// db port
define('DBUSER', 'gjypmzyw');// db user
define('DBPASSWD', 'gjypmzyw@zhisnet');// db password
define('DBNAME', 's' . SERVER_ID . '_gjypmzyw');// db name
define('PREFIX_KEY', 'GJYPMZYW' . 'S' . SERVER_ID . 's');// 缓存前缀
return array(
	// 路由配置文件1
	'config/Sharding.php' => array(
		'return' => array(
			'array' => array (
				'1' => 'sharding_1',
			),
		),
	),
	// 路由配置文件2
	'config/ShardingRand.php' => array(
		'return' => array(
			'array' => array (1),
		),
	),
	
	// 全局公共配置，定义常量
	'config.php' => array(
		'define' => array(
			'IN_INU' => 'true',
			'SYNC_W' => 'false',// 直接写入数据库
			'GAME_MARK' => "'gjypmzyw'",// 游戏标记
			'AGENT_CHANNEL_ALIAS' => "'GJYPMZYW'",// 区分渠道标识
			'LOG_PATH' => "'/data/logs/gjypmzyw_log/' . date('Ymd') . '/'",// 日志目录
			'DOMAIN_HOST' => "'gjypmzyw.zhisnet.cn'",// 入口域名
			'USE_PHPLOCK' => 'true',// 开启文件锁
			'LOCK_PATH' => "'/data/logs/gjypmzyw_log/'",// 文件锁日志目录
			'FILE_PATH' => "'/data/logs/'",//

			'AGENT_CHANNEL_NAME' => "'官居一品拇指游玩'",// 渠道名称, 作用于后台管理显示
			'IS_TEST_SERVER' => 'false',// 是否测试服务器
		),
	),
	// 全局公共配置，定义常量
	'public/servers/s' . SERVER_ID . '_config.php' => array(
		'define' => array(
			'SERVER_ID' => SERVER_ID,
			'MEMCACHED_PREFIX_KEY' => "'" . PREFIX_KEY . "'",// 多服务器共用缓存私钥
		),
	),
	'public/servers/s' . SERVER_ID . '.php' => array(
		'load' => array(
			sprintf("dirname(dirname(dirname( __FILE__ ))) . '/public/servers/s%s_config.php'", SERVER_ID),
			"dirname(dirname(dirname( __FILE__ ))) . '/public/cmd.php'",
		),
	),
	'public/admin/s' . SERVER_ID . '.php' => array(
		'load' => array(
			sprintf("dirname(dirname(dirname( __FILE__ ))) . '/public/servers/s%s_config.php'", SERVER_ID),
			"dirname(dirname(dirname( __FILE__ ))) . '/public/admin.php'",
		),
	),
	// 缓存等配置文件
	'config/server/s' . SERVER_ID . '_config.php' => array(
		'defined' => array(
			'IN_INU' => "or exit('Access Denied')",
		),
		'return' => array(
			'array' => array (
				'db' => array (
					'default' => array (
						'host' => DBHOST,
						'port' => DBPORT,
						'user' => DBUSER,
						'passwd' => DBPASSWD,
						'name' => DBNAME,
					),
					'sharding_1' => array (
						'host' => DBHOST,
						'port' => DBPORT,
						'user' => DBUSER,
						'passwd' => DBPASSWD,
						'name' => DBNAME,
					),
				),
				'memcache' => array (
					'data' => array (
						'0' => array (
							'host' => MEMHOST,
							'port' => MEMPORT,
							'weight' => 100,
							'prekey' => PREFIX_KEY,// 多服务器共用缓存私钥
						),
					),
				),
				'redis' => array (
					'host' => REDISHOST,
					'port' => REDISPORT,
				    'pass' => REDISPASS,
				),
				'param' => array (
					'table_div' => (999 == SERVER_ID) ? 1 : 100,
			        'table_bit' => 2,
			    	'user' => 'youdong',
			    	'pword' => '951623',
				),
			),
		),
	),
);