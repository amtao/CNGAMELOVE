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


if ( 0 > $SevidCfg['sevid'] ) {
	exit('SERVER_ID invalid');
}
$db = Common::getMyDb();
$table_div = Common::get_table_div();
$sqls = array();
for ($i = 0 ; $i < $table_div ; $i++)
{
	$table = 'user_'.Common::computeTableId($i);
	$sqls[] = "ALTER TABLE `{$table}` ADD COLUMN `dresscoin`  int(10) NOT NULL DEFAULT '0' COMMENT '装扮货币';";
	//门客表
	// $table = 'hero_'.Common::computeTableId($i);
	// $sqls[] = "ALTER TABLE `{$table}` ADD COLUMN `star`  int(10) NOT NULL DEFAULT '0' COMMENT '星级';";
	// $sqls[] = "ALTER TABLE `{$table}` ADD COLUMN `num`  int(10) NOT NULL DEFAULT '0' COMMENT '徒弟数量';";
	// $sqls[] = "ALTER TABLE `{$table}` ADD COLUMN `love`  int(10) NOT NULL DEFAULT '0' COMMENT '亲密度';";
	//$table = 'user_'.Common::computeTableId($i); //用户表
	//$sqls[] = "ALTER TABLE `{$table}` ADD COLUMN `allJob`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '[]' COMMENT '所有脸型' AFTER `clothe`;";
	//卡牌表
	// $table = 'baowu_'.Common::computeTableId($i);
	// $sqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
	// `uid` bigint(64) DEFAULT 0,
	// `baowuid` int(10) DEFAULT 0 COMMENT '四海奇珍宝物ID',
	// `level` int(10) DEFAULT 0 COMMENT '等级',
	// `star` int(12) DEFAULT 0 COMMENT '星级',	
	// PRIMARY KEY  `idx_baowu_uid` (`uid`,`baowuid`)
	// ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	// $sqls[] = "CREATE TABLE `service_chat_log` (
	// 	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	// 	`uid` bigint(64) DEFAULT '0',
	// 	`is_service` int(10) DEFAULT '0' COMMENT '0.用户  1.客服',
	// 	`content` text COMMENT '聊天内容',
	// 	`send_time` int(12) DEFAULT '0' COMMENT '发送时间',
	// 	`is_read` int(10) DEFAULT '0' COMMENT '0.未读  1.已读',
	// 	`is_close` int(10) DEFAULT '0' COMMENT '0.开启  1.关闭',
	// 	`from` varchar(50) DEFAULT '' COMMENT '客服帐号',
	// 	PRIMARY KEY (`id`),
	// 	KEY `idx_uid` (`uid`)
	//   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	  
	//   //在线人数
	//   $sqls[] = "CREATE TABLE `user_on_line_count` (
	// 	`date` int(12) DEFAULT '0' COMMENT '跑批时间',
	// 	`count` int(10) DEFAULT '0' COMMENT '人数',
	// 	UNIQUE KEY `date` (`date`)
	//   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	  
	//   //在线时长
	//   $sqls[] = "CREATE TABLE `user_on_line_time` (
	// 	`date` int(12) DEFAULT '0' COMMENT '跑批时间',
	// 	`uid` bigint(64) NOT NULL COMMENT 'uid',
	// 	`lineTime` int(12) DEFAULT '0' COMMENT '在线时长',
	// 	KEY (`date`)
	//   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	  
	//   $sqls[] = "CREATE TABLE `service_chat_automatic` (
	// 	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	// 	`uid` bigint(64) DEFAULT '0',
	// 	`cId` varchar(20) NOT NULL DEFAULT '' COMMENT '点击问题',
	// 	`click_time` int(12) DEFAULT '0' COMMENT '点击事件',
	// 	PRIMARY KEY (`id`),
	// 	KEY `idx_uid` (`uid`)
	//   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	  //角色转移表
	//   $sqls[] = "CREATE TABLE `gm_login` (
    // 	`id` bigint(64) NOT NULL AUTO_INCREMENT,
	// 	`oldUID` int(11) NOT NULL DEFAULT '0' COMMENT '老的uid',
	// 	`newUID` int(11) NOT NULL DEFAULT '0' COMMENT '新的uid',
	// 	PRIMARY KEY (`openid`)
	// 	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

	// //后台帐号表
    // $sqls[] = "CREATE TABLE `admin_user` (
	// 	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	// 	`user` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
	// 	`name` varchar(64) NOT NULL DEFAULT '' COMMENT '用户昵称',
	// 	`pwd` varchar(64) NOT NULL DEFAULT '' COMMENT '用户密码',
	// 	`power` varchar(250) NOT NULL DEFAULT '' COMMENT '权限',
	// 	`status` int(2) DEFAULT '0' COMMENT '是否删除',
	// 	PRIMARY KEY (`id`)
	// 	) DEFAULT CHARSET=utf8;";

	// //每日剩余元宝统计表
    // $sqls[] = "CREATE TABLE `user_diamond_day_log` (
	//   `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	//   `sevId` int(11) NOT NULL DEFAULT '1' COMMENT '服务器id',
	//   `diamond` bigint(50) DEFAULT '0' COMMENT '剩余钻石',
	//   `dayTime` int(12) DEFAULT '0' COMMENT '记录时间',
	//   KEY `id` (`id`)
	// ) DEFAULT CHARSET=utf8;";
}
/*
$sqls[] ="CREATE TABLE `user_step` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	`uid` bigint(20) unsigned NOT NULL COMMENT '用户ID',
	`step_id` int(10)  NOT NULL DEFAULT '0' COMMENT '步骤ID',
	PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$sqls[] ="ALTER TABLE `user_step` MODIFY COLUMN `step_id`  int(10) NOT NULL DEFAULT 0 COMMENT '步骤ID' AFTER `uid`;";
*/

foreach ($sqls as $sql){
	
	$rt = $db->query($sql);
	if (empty($rt)){
		echo $sql;
	}
	echo $rt;
}


echo PHP_EOL;

