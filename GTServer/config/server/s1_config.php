<?php 
defined('IN_INU') or exit('Access Denied');
return array(
'db' => array(
	'default' => array(
		'host' => '127.0.0.1',
		'port' => '3306',
		'user' => 'zhang',
		'passwd' => 'zhangs3',
		'name' => 's1_epgtmzch',
		),
	'sharding_1' => array(
		'host' => 's1_epgtmzch_game',
		'port' => '3306',
		'user' => '172.17.4.237',
		'passwd' => 'root',
		'name' => 's1_epgtmzch',
		),
	),
'memcache' => array(
	'data' => array(
		'0' => array(
			'host' => '127.0.0.1',
			'port' => '11211',
			'weight' => 100,
			'prekey' => 'EPGTMZCHS1s',
			'username' => 'memcached',
			'pass' => '',
			),
		),
	),
'redis' => array(
	'host' => '172.17.4.161',
	'port' => '6379',
	'pass' => 'play4fun88ylyz',
	),
'param' => array(
	'table_div' => 100,
	'table_bit' => 2,
	'user' => 'youdong',
	'pword' => '951623',
	),
);
