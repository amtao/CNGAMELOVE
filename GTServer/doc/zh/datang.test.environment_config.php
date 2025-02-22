<?php
/**
 * 服务器环境部署
 * @author wenyj
 */
define('SERVER_ID', '999');// server id
define('MEMHOST', '10.104.30.229');// memcache host
define('MEMPORT', '11274');// memcache port
define('REDISHOST', '10.104.30.229');// redis host
define('REDISPORT', '6440');// redis port
define('DBHOST', '10.104.30.229');// db host
define('DBPORT', '3306');// db port
define('DBUSER', 'datang');// db user
define('DBPASSWD', 'datang@youdong');// db password
define('DBNAME', 'datang');// db name// 's' . SERVER_ID . '_guaji'
define('PREFIX_KEY', 'DATANGTest' . 'S' . SERVER_ID . 's');// 缓存前缀
return array(
	// 全局公共配置，定义常量
	'config.php' => array(
		'define' => array(
			'IN_INU' => 'true',
			'SYNC_W' => 'true',// 直接写入数据库
			'GAME_MARK' => "'datang'",// 游戏标记
			'AGENT_CHANNEL_ALIAS' => "'DATANG'",// 区分渠道标识
			'LOG_PATH' => "'/data/logs/datang_log/' . date('Ymd') . '/'",// 日志目录
			'DOMAIN_HOST' => "'datang.test.zhisnet.cn'",// 入口域名
			'USE_PHPLOCK' => 'true',// 开启文件锁
			'LOCK_PATH' => "'/data/logs/datang_log/'",// 文件锁日志目录
			'FILE_PATH' => "'/data/logs/'",// 

			'AGENT_CHANNEL_NAME' => "'大唐测试服'",// 渠道名称, 作用于后台管理显示
			'IS_TEST_SERVER' => 'true',// 是否测试服务器
		),
	),
	// 全局公共配置，定义常量
	'public/servers/s' . SERVER_ID . '_config.php' => array(
		'define' => array(
			'SERVER_ID' => SERVER_ID,
			'MEMCACHED_PREFIX_KEY' => "'" . PREFIX_KEY . "'",// 多服务器共用缓存私钥
			'SERVER_TEST_OPEN' => 'true',// 999区默认开启允许外部人员访问
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
				),
				'param' => array (
					'table_div' => 1,
			        'table_bit' => 2,
			    	'user' => 'youdong',
			    	'pword' => '951623',
				),
			),
		),
	),
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
);