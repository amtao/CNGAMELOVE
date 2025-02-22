<?php
/**
 * 服务器环境部署
 * @author lph
 * temp
 */

define('SERVER_ID', '1');// server id
define('MEMHOST', 'm-wz98d4a9c4fd4484.memcache.rds.aliyuncs.com');// memcache host
define('MEMPORT', '11211');// memcache port
define('MEMUSERNAME', 'qbcymzyw1'); //此处要改（mem的名字）
define('MEMPASS', 'qbcymzyw911K');// memcache pass
define('REDISHOST', 'r-wz9210403fffa4f4.redis.rds.aliyuncs.com');// redis host
define('REDISPORT', '6379');// redis port
define('REDISPASS', 'qbcymzyw911K');// redis pass
define('DBHOST', 'rm-wz9855a5g1530i80q.mysql.rds.aliyuncs.com');// db host
define('DBPORT', '3306');// db port
define('DBUSER', 'qbcymzyw');// db user
define('DBPASSWD', 'qbcymzyw@zhisnet');// db password
define('DBNAME', 's' . SERVER_ID . '_qbcymzyw');// db name
define('PREFIX_KEY', 'QBCYMZYW' . 'S' . SERVER_ID . 's');// 缓存前缀
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
			'GAME_MARK' => "'qbcymzyw'",// 游戏标记
			'AGENT_CHANNEL_ALIAS' => "'QBCYMZYW'",// 区分渠道标识
			'LOG_PATH' => "'/data/logs/qbcymzyw_log/' . date('Ymd') . '/'",// 日志目录
			'DOMAIN_HOST' => "'qbcymzyw.commpad.cn'",// 入口域名
			'USE_PHPLOCK' => 'true',// 开启文件锁
			'LOCK_PATH' => "'/data/logs/qbcymzyw_log/'",// 文件锁日志目录
			'FILE_PATH' => "'/data/logs/'",//

			'AGENT_CHANNEL_NAME' => "'拇指游玩-权霸朝野'",// 渠道名称, 作用于后台管理显示
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
							'username' => MEMUSERNAME, //此处要改（mem的名字）
                            'pass' => MEMPASS,
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