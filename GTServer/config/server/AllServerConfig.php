<?php
//数据库配置
$minServerID = $maxServerID = 1;

switch (AGENT_CHANNEL_ALIAS) {
	
	
	//皇帝-测试服
	case 'KINGTest':
	default:
		$minServerID = $maxServerID = 999;
		$tplDBCfg = array(
			'host' => '127.0.0.1',
			'port' => '3306',
			'user' => 'king',
			'passwd' => 'king@youdong',
			'name' => 'king',
		);
		break;
}

$DBCfg = array();
for ($i=$minServerID; $i<=$maxServerID; $i++) {
	$DBCfg[AGENT_CHANNEL_ALIAS][$i] = array(
		'host' => $tplDBCfg['host'],
		'port' => $tplDBCfg['port'],
		'user' => $tplDBCfg['user'],
		'passwd' => $tplDBCfg['passwd'],
		'name' => sprintf($tplDBCfg['name'], $i),
	);
}

return $DBCfg;
