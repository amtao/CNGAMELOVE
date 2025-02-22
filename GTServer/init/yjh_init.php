<?php

require_once dirname( __FILE__ ) . '/../public/common.inc.php';
$AUTO_INCREMENT_START = 10086;

//服务器ID
$sevid = intval($_SERVER['argv'][1]);



if (empty($sevid)){
    exit('错误啦!!!!!!!!!');
}
$SevidCfg = Common::getSevidCfg($sevid);
echo PHP_EOL . 'init,server id = ' . $SevidCfg['sevid'] . PHP_EOL;
if( $SevidCfg['sevid'] <> 999 ){
	$AUTO_INCREMENT_START = $SevidCfg['sevid']  * 1000000;
}

//公会自增id
$CLUB_AUTO_START = 100;
if( $SevidCfg['sevid'] <> 999 ){
	$CLUB_AUTO_START = $SevidCfg['sevid']  * 10000;
}


if ( 0 > $SevidCfg['sevid'] ) {
	exit('SERVER_ID invalid');
}
$db = Common::getMyDb();

$sqls = array();

//检查订单的id
$sqls[] = "CREATE TABLE IF NOT EXISTS `check_oid` (
    `id` bigint(64) NOT NULL AUTO_INCREMENT COMMENT 'ID',
	`pt` varchar(64) NULL DEFAULT '',
	`sid` int(3) DEFAULT '0',
	`oid` varchar(64) NULL DEFAULT '',
	PRIMARY KEY (`id`),
	INDEX `cs4` (`oid`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT";



foreach ($sqls as $sql){
	$rt = $db->query($sql);
	if (empty($rt)){
		echo $sql;
	}
	echo $rt;
}


echo PHP_EOL;

