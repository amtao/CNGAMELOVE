<?php

require_once dirname( __FILE__ ) . '/../public/common.inc.php';
$AUTO_INCREMENT_START = 10086;

//服务器ID
$sevid = intval($_SERVER['argv'][1]);

if (empty($sevid)){
    eixt('错误啦!!!!!!!!!');
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
$flowDb = Common::getDftDb('flow');
$table_div = Common::get_table_div();
$sqls = array();
$flowSqls = array();
for ($i = 0 ; $i < $table_div ; $i++)
{
    //流水事件表
    $table = 'flow_event_'.Common::computeTableId($i);
    $flowSqls[] = "drop table if exists `{$table}`";
    $flowSqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
    `id` bigint(64) NOT NULL AUTO_INCREMENT COMMENT 'ID',
	`uid` bigint(64) DEFAULT 0,
	`model` varchar(255) DEFAULT NULL COMMENT '模块',
	`ctrl`  varchar(255) DEFAULT NULL COMMENT '来源',
	`params`  varchar(2056) DEFAULT NULL COMMENT '参数',
	`ftime` int(12) DEFAULT 0 COMMENT '时间',
	`ip`  varchar(36) DEFAULT NULL COMMENT 'ip地址',
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    //流水详情表
    $table = 'flow_record_'.Common::computeTableId($i);
    $flowSqls[] = "drop table if exists `{$table}`";
    $flowSqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
	`flowid` bigint(64) DEFAULT 0 COMMENT '流水事件id',
	`type` int(12) DEFAULT 0 COMMENT '配置id',
	`itemid` varchar(255) DEFAULT NULL COMMENT '多种含义',
	`cha` int(32) DEFAULT 0 COMMENT '差值',
	`next` int(32) DEFAULT 0 COMMENT '新值'
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
}

if($SevidCfg['sevid'] == 999 || $SevidCfg['sevid'] == 1) {
    $flowSqls[] = "CREATE TABLE `admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin` varchar(255) NOT NULL DEFAULT '' COMMENT '操作人',
  `model` varchar(255) NOT NULL DEFAULT '' COMMENT '模块',
  `control` varchar(255) NOT NULL DEFAULT '' COMMENT '控制器',
  `data` text NOT NULL COMMENT '操作信息',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
}
$flowSqls[] = "CREATE TABLE IF NOT EXISTS `remain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` varchar(20) NOT NULL DEFAULT '' COMMENT '日期',
  `login` text NOT NULL,
  `register` text NOT NULL,
  `info` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
$flowSqls[] = "CREATE TABLE `act_item_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `actid` int(10) DEFAULT '0' COMMENT '活动类型编号',
  `itemid` varchar(255) DEFAULT NULL COMMENT '多种含义',
  `num` bigint(64) DEFAULT '0' COMMENT '数量',
  `ftime` int(12) DEFAULT '0' COMMENT '时间',
  PRIMARY KEY (`id`),
  KEY `idx_actid` (`actid`),
  KEY `idx_actid_ftime` (`actid`, `ftime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
foreach ($flowSqls as $flowSql){
    $result = $flowDb->query($flowSql);
    if (empty($result)){
        echo $flowSql;
    }
    echo $result;
}

echo PHP_EOL;

