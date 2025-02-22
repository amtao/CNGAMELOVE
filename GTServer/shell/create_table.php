<?php
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
$serverList = ServerModel::getServList();
$id = ServerModel::getDefaultServerId();
$SevidCfg1 = Common::getSevidCfg($id);
$db = Common::getMyDb('flow');
$sql = "CREATE TABLE IF NOT EXISTS `custom_service` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
  `player` bigint(16) NOT NULL,
  `custom` varchar(64) NOT NULL DEFAULT '' COMMENT '客服',
  `qq` bigint(16) NOT NULL DEFAULT '0' COMMENT 'qq',
  `mobile` bigint(11) NOT NULL DEFAULT '0',
  `change_time` int(10) NOT NULL,
  `time` int(10) NOT NULL,
  `remarks` text NOT NULL COMMENT '备注信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;";
$result = $db->query($sql);
echo $result;
