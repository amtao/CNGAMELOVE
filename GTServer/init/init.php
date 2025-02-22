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

$dbInit = Common::getDbBySevId($SevidCfg['sevid']);
$dbFlowInit = Common::getDbBySevId($SevidCfg['sevid'], 'flow');
$dbInit->query("CREATE DATABASE s".$SevidCfg['sevid']."_xianyu;");
$dbFlowInit->query("CREATE DATABASE s".$SevidCfg['sevid']."_xianyu_flow;");


$config = Common::getConfig(GAME_MARK."/AllServerDbConfig");
Common::loadLib( 'Db' );
$db = new Db( $config[$sevid]["game"]);
$flowDb = new Db( $config[$sevid]["flow"]);
$table_div = Common::get_table_div();
$sqls = array();
$flowSqls = array();
for ($i = 0 ; $i < $table_div ; $i++)
{
	$table = 'user_'.Common::computeTableId($i); //用户表
	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
	`uid` bigint(64)  NOT NULL COMMENT 'uid',
	`name` varchar(64) NOT NULL DEFAULT '' COMMENT '名字',
	`job` int(10) DEFAULT '0' COMMENT '头像ID',
	`sex` int(10) DEFAULT '0' COMMENT '性别 女=2',
	
	/*基础数值信息*/
	`level` int(10) DEFAULT '1' COMMENT '官阶',
	`exp` bigint(20) DEFAULT '0' COMMENT '政绩',
	`vip` int(10) DEFAULT '0' COMMENT 'VIP等级',
	`step` int(10) DEFAULT '0' COMMENT '账号进度',
	`guide` int(10) DEFAULT '0' COMMENT '新手引导步骤',
	
	`cash_sys` bigint(20) DEFAULT '0' COMMENT '系统钻石',
	`cash_buy` bigint(20) DEFAULT '0' COMMENT '充值钻石',
	`cash_use` bigint(20) DEFAULT '0' COMMENT '消耗钻石',
	
	`coin` bigint(20) DEFAULT '0' COMMENT '金币',
	`food` bigint(20) DEFAULT '0' COMMENT '粮草',
	`army` bigint(20) DEFAULT '0' COMMENT '军队',
	
	`bmap` int(10) DEFAULT '0' COMMENT '地图大关ID',
	`smap` int(10) DEFAULT '0' COMMENT '地图小关ID',
	`mkill` bigint(20) DEFAULT '0' COMMENT '已经杀掉的小兵数量/已伤的BOSS血量',
	
	`baby_num` int(10) DEFAULT '0' COMMENT '子嗣席位',
	`cb_time` int(12) DEFAULT '0' COMMENT '朝拜时间',
	
	`clubid` bigint(64) DEFAULT '0' COMMENT '联盟ID',
	
	`mw_num` int(10) DEFAULT '0' COMMENT '名望数值',
	`mw_day` int(10) DEFAULT '0' COMMENT '名望每日产出',
	`xuanyan` varchar(128) NOT NULL DEFAULT '' COMMENT '宣言',
	
	/*设置信息*/
	`voice` int(1) DEFAULT 1 COMMENT '声音开关',
	`music` int(1) DEFAULT 1 COMMENT '音乐开关',
	
	/*统计信息*/
	`regtime` int(12) DEFAULT '0' COMMENT '注册时间?',
	`lastlogin` int(12) DEFAULT '0' COMMENT '最后一次登陆时间',
	`loginday` int(10) DEFAULT '0' COMMENT '累计登陆天数',
	
	`platform` varchar(50) DEFAULT ''  COMMENT '渠道标识',
	`channel_id` varchar(20) DEFAULT ''  COMMENT '渠道',
	`ip` varchar(30)  default null comment '注册Ip',
	`clothe` varchar(128)  default null comment '时装',
	`allJob` varchar(128)  default '[]' comment '所有脸型',
	`dresscoin` bigint(20)  default '0' comment '伙伴装扮货币',

	
	UNIQUE KEY `uid` (`uid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	
	//门客表
	$table = 'hero_'.Common::computeTableId($i);
	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
	`uid` bigint(64) DEFAULT 0,
	`heroid` int(10) DEFAULT 0 COMMENT '门客ID',
	`level` int(10) DEFAULT 0 COMMENT '等级',
	`exp` int(12) DEFAULT 0 COMMENT '经验',	
	`zzexp` int(12) DEFAULT 0 COMMENT '资质技能经验点',
	`pkexp` int(12) DEFAULT 0 COMMENT 'PK技能经验点',
	`senior` int(10) DEFAULT 0 COMMENT '进阶',
	`epskill` varchar(256) DEFAULT 0 COMMENT '资质技能序列',
	`pkskill` varchar(256) DEFAULT 0 COMMENT 'PK 技能序列',
	`ghskill` varchar(256) DEFAULT 0 COMMENT '光环 技能序列',
	`e1` int(10) DEFAULT 0 COMMENT '嗑药属性',
	`e2` int(10) DEFAULT 0 COMMENT '嗑药属性',
	`e3` int(10) DEFAULT 0 COMMENT '嗑药属性',
	`e4` int(10) DEFAULT 0 COMMENT '嗑药属性',
	`num` int(10) DEFAULT 0 COMMENT '徒弟个数',
	`love` int(10) DEFAULT 0 COMMENT '好感度',
	`star` int(10) DEFAULT 0 COMMENT '星级',
	PRIMARY KEY  `idx_euipi_uid` (`uid`,`heroid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	
	//后宫表
	$table = 'wife_'.Common::computeTableId($i);
	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
	`uid` bigint(64) DEFAULT 0,
	`wifeid` int(10) DEFAULT 0 COMMENT '红颜ID',
	`love` int(10) DEFAULT 0 COMMENT '亲密度',
	`flower` int(12) DEFAULT 0 COMMENT '魅力值',
	`exp` int(12) DEFAULT 0 COMMENT '红颜经验',
	`skill` varchar(128) DEFAULT 0 COMMENT '技能等级',
	`state` int(1) DEFAULT 0 COMMENT '状态',
	PRIMARY KEY  `idx_euipi_uid` (`uid`,`wifeid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	
	//子嗣表
	$table = 'son_'.Common::computeTableId($i);
	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
	`uid` bigint(64) DEFAULT 0,
	`sonuid` bigint(10) NOT NULL AUTO_INCREMENT COMMENT '子嗣流水ID',
	`name` varchar(64) NOT NULL DEFAULT '' COMMENT '名字',
	`sex` int(2) NOT NULL DEFAULT 0 COMMENT '性别',
	`mom` int(10) DEFAULT 0 COMMENT '母亲wifeid',
	`state` int(2) DEFAULT 0 COMMENT '状态',
	`e1` int(10) DEFAULT 0 COMMENT '属性',
	`e2` int(10) DEFAULT 0 COMMENT '属性',
	`e3` int(10) DEFAULT 0 COMMENT '属性',
	`e4` int(10) DEFAULT 0 COMMENT '属性',
	
	`talent` int(10) DEFAULT 0 COMMENT '天赋品级',
	`cpoto` int(10) DEFAULT 0 COMMENT '儿童形象',
	`level` int(10) DEFAULT 0 COMMENT '当前等级',
	`exp` int(10) DEFAULT 0 COMMENT '等级经验',
	`power` int(10) DEFAULT 0 COMMENT '活力值',
	`ptime` int(12) DEFAULT 0 COMMENT '上次培养恢复时间',
	
	`honor` int(10) DEFAULT 0 COMMENT '科举名次',
	
	`tquid` bigint(64) DEFAULT 0 COMMENT '提亲UID (等于0 表示全服提亲)',
	`tqitem` bigint(10) DEFAULT 0 COMMENT '提亲道具ID(可能退还)',
	`spuid` bigint(64) DEFAULT 0 COMMENT '配偶UID',
	`spsonuid` bigint(64) DEFAULT 0 COMMENT '配偶流水ID',
	`sptime` int(12) DEFAULT 0 COMMENT '结婚时间',
	PRIMARY KEY  `idx_euipi_uid` (`sonuid`),
	KEY `idx_uid` (`uid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	
	//道具表
	$table = 'item_'.Common::computeTableId($i);
	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
	`uid` bigint(64) DEFAULT 0,
	`itemid` int(10) DEFAULT 0 COMMENT '道具ID',
	`count` int(10) DEFAULT 0 COMMENT '数量',
	UNIQUE KEY `idx_uid` (`uid`,`itemid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	
	//活动数据表
	$table = 'act_'.Common::computeTableId($i);
	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
	`uid` bigint(64) DEFAULT 0,
	`actid` int(10) DEFAULT 0 COMMENT '活动类型编号',
	`tjson` text DEFAULT '' COMMENT '活动数据',
	UNIQUE KEY `idx_uid` (`uid`,`actid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	
	//邮件表     邮件类型  mtype 0:无道具列表  1:有道具列表 2:其他
	$table = 'mail_'.Common::computeTableId($i);
	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
	`mid` bigint(64) NOT NULL AUTO_INCREMENT COMMENT '邮件ID',
	`uid` bigint(64) DEFAULT 0,
	`mtitle` varchar(255) DEFAULT NULL COMMENT '标题',
	`mcontent`  varchar(255) DEFAULT NULL COMMENT '内容',
	`items` text  COMMENT '道具列表',
	`mtype` int(1) DEFAULT 0 COMMENT '邮件类型 0123',
	`fts` int(12) DEFAULT 0 COMMENT '发送时间',
	`rts` int(12) DEFAULT 0 COMMENT '读取时间',
	`isdel` int(1) DEFAULT 0 COMMENT '是否已被删除',
	`link` text COMMENT '外部连接',
	PRIMARY KEY  `idx_mid` (`mid`),
	KEY `idx_uid` (`uid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

	//卡牌表
	$table = 'card_'.Common::computeTableId($i);
	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
	`uid` bigint(64) DEFAULT 0,
	`cardid` int(10) DEFAULT 0 COMMENT '卡牌ID',
	`level` int(10) DEFAULT 0 COMMENT '等级',
	`star` int(12) DEFAULT 0 COMMENT '星级',
	`isEquip` int(10) DEFAULT 0 COMMENT '是否在守护',
	`imprintLv` int(10) DEFAULT 0 COMMENT '印痕等级',
	`flowerPoint` varchar(128)  default '[]' comment '卡牌升华',
	`isClotheEquip` int(10) DEFAULT 0 COMMENT '是否装备在服装上',
	PRIMARY KEY  `idx_card_uid` (`uid`,`cardid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

	//宝物表
	$table = 'baowu_'.Common::computeTableId($i);
	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
	`uid` bigint(64) DEFAULT 0,
	`baowuid` int(10) DEFAULT 0 COMMENT '四海奇珍宝物ID',
	`level` int(10) DEFAULT 0 COMMENT '等级',
	`star` int(12) DEFAULT 0 COMMENT '星级',	
	PRIMARY KEY  `idx_baowu_uid` (`uid`,`baowuid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	

    //流水事件表
    $table = 'flow_event_'.Common::computeTableId($i);
    $flowSqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
    `id` bigint(64) NOT NULL AUTO_INCREMENT COMMENT 'ID',
	`uid` bigint(64) DEFAULT 0,
	`model` varchar(255) DEFAULT NULL COMMENT '模块',
	`ctrl`  varchar(255) DEFAULT NULL COMMENT '来源',
	`params`  varchar(2056) DEFAULT NULL COMMENT '参数',
	`ftime` int(12) DEFAULT 0 COMMENT '时间',
	`ip`  varchar(36) DEFAULT NULL COMMENT 'ip地址',
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    //流水详情表
    $table = 'flow_record_'.Common::computeTableId($i);
    $flowSqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
	`flowid` bigint(64) DEFAULT 0 COMMENT '流水事件id',
	`type` int(12) DEFAULT 0 COMMENT '配置id',
	`itemid` varchar(255) DEFAULT NULL COMMENT '多种含义',
	`cha` bigint(64) DEFAULT 0 COMMENT '差值',
	`next` bigint(64) DEFAULT 0 COMMENT '新值'
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

	//流水详情表
	$table = 'flow_records_'.Common::computeTableId($i);
	$flowSqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
	`flowid` bigint(64) DEFAULT 0 COMMENT '流水事件id',
	`type` int(12) DEFAULT 0 COMMENT '配置id',
	`itemid` varchar(255) DEFAULT NULL COMMENT '多种含义',
	`cha` bigint(64) DEFAULT 0 COMMENT '差值',
	`next` bigint(64) DEFAULT 0 COMMENT '新值'
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	
}

//活动公共数据表
$sqls[] = "CREATE TABLE IF NOT EXISTS `sev_act` (
  `key` int(10) DEFAULT 0 COMMENT '活动类型编号',
  `hcid` int(10) DEFAULT 0 COMMENT '分组ID',
  `did` int(10) DEFAULT 0 COMMENT '重置ID',
  `value` longtext,
  PRIMARY KEY (`key`,`hcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


//公会
$table = 'club'; //公会表
$sqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (

`cid` int(10)  NOT NULL AUTO_INCREMENT COMMENT '公会id',
`name` varchar(64) NOT NULL DEFAULT '' COMMENT '公会名字',
`level` int(3) DEFAULT '1' COMMENT '公会等级',
`exp` int(12) DEFAULT '0' COMMENT '联盟总经验',
`fund` int(12) DEFAULT '0' COMMENT '联盟财富值/基金',
`qq` int(12) DEFAULT '0' COMMENT 'QQ群',
`weixin` varchar(32) DEFAULT '0' COMMENT '微信群',
`password` varchar(32) NOT NULL DEFAULT '' COMMENT '联盟密码',
`outmsg` varchar(256) DEFAULT '' COMMENT '对外宣言',
`notice` varchar(256) DEFAULT '' COMMENT '公告',
`isJoin` int(2) DEFAULT '0' COMMENT '是否允许其他玩家随机加入',
`ctime` int(12) DEFAULT 0 COMMENT '联盟创建时间',
`members` text  COMMENT '成员列表',
`ftime` int(12) DEFAULT 0 COMMENT '每日更新时间',
`lsjLv` int(3) DEFAULT '1' COMMENT '理事间等级',
`spLv` int(3) DEFAULT '1' COMMENT '商铺等级',
`dissolutionTime` int(20) DEFAULT '0' COMMENT '解散冷却时间',
`jytLv` int(3) DEFAULT '1' COMMENT '谏言堂等级',
 PRIMARY KEY (`cid`),
 KEY `idx_cid` (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT={$CLUB_AUTO_START} DEFAULT CHARSET=utf8 COMMENT='公会表';";
$sqls[] = "ALTER TABLE `{$table}` AUTO_INCREMENT={$CLUB_AUTO_START};";
$sqls[] = "insert into `{$table}` set `cid`={$CLUB_AUTO_START},`password`='123456', `name` ='test';";



//子嗣全服提亲表
$sqls[] = "CREATE TABLE IF NOT EXISTS `son_marry` (
	`uid` BIGINT(64) NOT NULL COMMENT '玩家uid',
	`sonuid` INT NULL COMMENT '子嗣ID',
	`sex` TINYINT NULL COMMENT '性别',
	`honor` TINYINT NULL COMMENT '秀才',
	`otime` INT(12) NULL COMMENT '过期时间',
	`ishonor` int(2) NOT NULL DEFAULT 0 COMMENT '是否身份匹配',
	PRIMARY KEY (`uid`, `sonuid`),
	INDEX `honor` (`honor`),
	INDEX `sex` (`sex`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT";


//姓名表
$sqls[] = "CREATE TABLE IF NOT EXISTS `index_name` (
	`name` varchar(64) COMMENT '名字',
	`uid` bigint(64)  NOT NULL COMMENT 'uid',
	PRIMARY KEY  `index_name` (`name`),
	UNIQUE INDEX `uid` (`uid`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8";

// 订单表
$sqls[] = "CREATE TABLE IF NOT EXISTS `t_order` (
  `orderid` int(11) NOT NULL AUTO_INCREMENT COMMENT '订单号',
  `openid` varchar(255) DEFAULT '' COMMENT '关联gm_sharding的ustr',
  `roleid` bigint(64) DEFAULT 0 COMMENT '角色ID',
  `money` float(11,2) DEFAULT 0 COMMENT '实际充值金额, 精确到分',
  `diamond` int(11) DEFAULT 0 COMMENT '虚拟货币数量(元宝/钻石)',
  `status` tinyint(3) DEFAULT 0 COMMENT '充值状态, 0-待处理, 1-成功, 2-已通知',
  `ctime` int(11) DEFAULT 0 COMMENT '订单创建时间',
  `ptime` int(11) DEFAULT 0 COMMENT '订单支付时间',
  `tradeno` varchar(255) DEFAULT '' COMMENT '平台订单号',
  `platform` varchar(50) DEFAULT '' COMMENT '平台标识',
  `paytype` varchar(255) DEFAULT '' COMMENT '支付方式，不同平台代号不同',
  `gift_bag` varchar(255) DEFAULT '' COMMENT '充值描述',
  PRIMARY KEY (`orderid`),
  KEY `idx_uid` (`roleid`)
) ENGINE=InnoDB AUTO_INCREMENT={$AUTO_INCREMENT_START} DEFAULT CHARSET=utf8 COMMENT='订单表';";
$sqls[] = "ALTER TABLE `t_order` AUTO_INCREMENT={$AUTO_INCREMENT_START};";

// 路由表
$sqls[] = "CREATE TABLE IF NOT EXISTS `gm_sharding` (
			  `uid` bigint(64) NOT NULL AUTO_INCREMENT,
			  `ustr` varchar(255) NOT NULL,
			  `sharding_id` tinyint(3) NOT NULL,
			  PRIMARY KEY (`uid`),
			  KEY `ustr` (`ustr`)
			) ENGINE=InnoDB AUTO_INCREMENT={$AUTO_INCREMENT_START} DEFAULT CHARSET=utf8;";
$sqls[] = "ALTER TABLE `gm_sharding` AUTO_INCREMENT={$AUTO_INCREMENT_START};";
$sqls[] = "insert into `gm_sharding` set `uid`='{$AUTO_INCREMENT_START}', `ustr` ='gm', `sharding_id`='1';";

//设备表
$sqls[] = "CREATE TABLE `device` (
  `uid` bigint(16) unsigned NOT NULL DEFAULT '0' COMMENT '玩家UID',
  `device` varchar(64) NOT NULL DEFAULT '' COMMENT '设备号',
  `platform` varchar(64) NOT NULL DEFAULT '' COMMENT '平台',
  `time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '时间',
  `param` varchar(1024) NOT NULL DEFAULT '' COMMENT '其他参数',
  PRIMARY KEY (`uid`,`device`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

//风险设备表
$sqls[] = "CREATE TABLE `risk_device` (
  `device` varchar(64) NOT NULL DEFAULT '' COMMENT '设备号',
  `value` mediumtext,
  PRIMARY KEY (`device`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

//脚本记录表
$table = 'run';
$sqls[] = "CREATE TABLE IF NOT EXISTS `{$table}` (
`key` varchar(64) NOT NULL DEFAULT '' ,
`vjson` text DEFAULT '' COMMENT '活动数据',
UNIQUE KEY `idx_uid` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

//公共存储表
$sqls[] = "CREATE TABLE IF NOT EXISTS `vo_common` (
  `key`  varchar(255) NOT NULL COMMENT '键值',
  `value`  longtext NULL COMMENT '值',
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

//活动配置表
$table = 'game_act';
//$sqls[]="drop table if exists `{$table}`;";
$sqls[]="CREATE TABLE `{$table}`(
        `id` bigint(64) NOT NULL AUTO_INCREMENT,
        `act_key` varchar(60) NOT NULL COMMENT '活动key',
        `server` varchar(255) DEFAULT 'all' COMMENT '服数',
        `sort` int(11) DEFAULT 99999 COMMENT '排序',
        `audit` tinyint(1) DEFAULT 0 COMMENT '审核状态: 0未审核,1审核通过,2审核不通过',
        `auser` varchar(60) DEFAULT '' COMMENT '审核人名称',
        `atime` int(11) DEFAULT 0 COMMENT '审核时间',
        `contents` mediumtext DEFAULT NULL COMMENT '活动内容',
        `status` tinyint(1) DEFAULT 0 COMMENT '记录状态：0正常，1删除',
        PRIMARY KEY (`id`)
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

//活动模板表
$table = 'game_act_template';
//$sqls[]="drop table if exists `{$table}`;";
$sqls[]="CREATE TABLE `{$table}`(
        `id` bigint(64) NOT NULL AUTO_INCREMENT,
        `title` varchar(60) NOT NULL COMMENT '活动名称',
        `act_key` varchar(60) NOT NULL COMMENT '活动key',
        `auser` varchar(60) DEFAULT '' COMMENT '添加或修改人',
        `atime` int(11) DEFAULT 0 COMMENT '添加或修改时间',
        `contents` mediumtext DEFAULT NULL COMMENT '活动内容',
        `status` tinyint(1) DEFAULT 0 COMMENT '记录状态：0正常，1删除',
        PRIMARY KEY (`id`)
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

//基础配置表
$table = 'game_config';
//$sqls[]="drop table if exists `{$table}`;";
$sqls[]="CREATE TABLE `{$table}`(
        `id` bigint(64) NOT NULL AUTO_INCREMENT,
        `config_key` varchar(60) NOT NULL COMMENT '活动key',
        `server` varchar(255) DEFAULT 'all' COMMENT '服数',
        `contents` mediumtext DEFAULT NULL COMMENT '活动内容',
        `status` tinyint(1) DEFAULT 0 COMMENT '记录状态：0正常，1删除',
        PRIMARY KEY (`id`)
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
	
if($SevidCfg['sevid'] == 999 || $SevidCfg['sevid'] == 1) {
    //激活码/兑换码表
    $sqls[] = "CREATE TABLE IF NOT EXISTS `acode` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `acode` varchar(64) NOT NULL COMMENT '激活码',
      `act_key` varchar(64) NOT NULL DEFAULT '' COMMENT '兑换活动的标识',
  `type` int(10) NOT NULL DEFAULT '0' COMMENT '激活码种类 1:全服 2：单服',
  `sevid` int(10) NOT NULL DEFAULT '0' COMMENT '服务器ID',
  `uid` bigint(64) NOT NULL DEFAULT '0' COMMENT '兑换者UID',
  `ctime` int(11) unsigned DEFAULT '0' COMMENT '生成时间',
  `utime` int(11) unsigned DEFAULT '0' COMMENT '兑换时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_acode` (`acode`) USING BTREE,
  KEY `idx_act` (`act_key`,`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    //注册记录表
    $sqls[] = "CREATE TABLE IF NOT EXISTS `register` (
        `openid` varchar(255) NOT NULL,
        `reg_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
        `platform` varchar(255) NOT NULL DEFAULT '' COMMENT '平台',
        `servid` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '服务器id',
        `uid` int(11) NOT NULL DEFAULT '0' COMMENT '第一次注册的uid',
        `data` varchar(2048) NOT NULL DEFAULT '' COMMENT '存储uid',
        PRIMARY KEY (`openid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		  //角色转移表
    $sqls[] = "CREATE TABLE `gm_login` (
    	`id` bigint(64) NOT NULL AUTO_INCREMENT,
		`oldUID` int(11) NOT NULL DEFAULT '0' COMMENT '老的uid',
		`newUID` int(11) NOT NULL DEFAULT '0' COMMENT '新的uid',
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

	//后台帐号表
    $sqls[] = "CREATE TABLE `admin_user` (
		`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
		`user` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
		`name` varchar(64) NOT NULL DEFAULT '' COMMENT '用户昵称',
		`pwd` varchar(64) NOT NULL DEFAULT '' COMMENT '用户密码',
		`power` varchar(250) NOT NULL DEFAULT '' COMMENT '权限',
		`status` int(2) DEFAULT '0' COMMENT '是否删除',
		PRIMARY KEY (`id`)
		) DEFAULT CHARSET=utf8;";

	//每日剩余元宝统计表
    $sqls[] = "CREATE TABLE `user_diamond_day_log` (
	  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	  `sevId` int(11) NOT NULL DEFAULT '1' COMMENT '服务器id',
	  `diamond` bigint(50) DEFAULT '0' COMMENT '剩余钻石',
	  `dayTime` int(12) DEFAULT '0' COMMENT '记录时间',
	  KEY `id` (`id`)
	) DEFAULT CHARSET=utf8;";
    //登录记录表
    $sqls[] = "CREATE TABLE IF NOT EXISTS `login_log` (
       `openid` varchar(255) NOT NULL DEFAULT '',
       `login_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录时间',
                   `platform` varchar(255) NOT NULL DEFAULT '' COMMENT '平台',
                   `servid` int(11) NOT NULL DEFAULT '1' COMMENT '服务器id',
                    KEY `login` (`openid`,`login_time`) USING BTREE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    
    //错误订单-苹果
	$sqls[] = "CREATE TABLE IF NOT EXISTS `fail_order` (
	    `id` bigint(64) NOT NULL AUTO_INCREMENT COMMENT 'ID',
		`type` INT(2) NULL COMMENT '',
		`cs1` varchar(64) NULL DEFAULT '',
		`cs2` varchar(64) NULL DEFAULT '',
		`cs3` varchar(64) NULL DEFAULT '时间',
		`cs4` varchar(64) NULL DEFAULT 'uid',
		`cs5` varchar(64) NULL DEFAULT '',
		`cs6` varchar(64) NULL DEFAULT '',
		`cs7` longtext,
		`cs8` varchar(64) NOT NULL DEFAULT '',
		PRIMARY KEY (`id`),
		INDEX `cs4` (`cs4`)
	)
	COLLATE='utf8_general_ci'
	ENGINE=InnoDB
	ROW_FORMAT=DEFAULT";
	
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
    
    //后台操作日志
    $flowSqls[] = "CREATE TABLE `admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin` varchar(255) NOT NULL DEFAULT '' COMMENT '操作人',
  `model` varchar(255) NOT NULL DEFAULT '' COMMENT '模块',
  `control` varchar(255) NOT NULL DEFAULT '' COMMENT '控制器',
  `data` text NOT NULL COMMENT '操作信息',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(255) NOT NULL DEFAULT '' COMMENT 'ip',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

	$flowSqls[] = "CREATE TABLE `pandect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `platform` varchar(255) NOT NULL DEFAULT '' COMMENT '平台',
  `register` int(10) unsigned NOT NULL DEFAULT '0',
  `income` int(10) NOT NULL DEFAULT '0',
  `pay_man` int(10) NOT NULL DEFAULT '0',
  `pay_count` int(10) NOT NULL DEFAULT '0',
  `new_pay` int(10) NOT NULL DEFAULT '0',
  `new_income` int(10) NOT NULL DEFAULT '0',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '时间',
  `other` varchar(255) NOT NULL DEFAULT '' COMMENT '其他参数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8;";
}

if($SevidCfg['sevid'] == 999) {
    $sqls[] = "CREATE TABLE `front_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `string` text COMMENT '参数',
  `time` int(10) unsigned NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

$flowSqls[] = "CREATE TABLE `remain`  (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`date` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '日期',
	`login` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`register` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`info` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 291 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact";
}

//聊天数据库
$flowSqls[] = "CREATE TABLE `flow_chat` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `vip` int(10) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '官阶',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '玩家名称',
  `type` int(255) unsigned NOT NULL DEFAULT '1',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '聊天内容',
  `other` varchar(255) NOT NULL DEFAULT '' COMMENT '其他参数',
  `time` int(10) unsigned NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;";

//消费数据库
$flowSqls[] = "CREATE TABLE `flow_consume` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(64) NOT NULL,
  `type` int(10) NOT NULL DEFAULT '1',
  `num` bigint(32) NOT NULL DEFAULT '0',
  `from` varchar(255) NOT NULL DEFAULT '',
  `other` varchar(255) NOT NULL DEFAULT '' COMMENT '其他参数',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

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



//版本控制表
$sqls[] = "CREATE TABLE `version_management` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `channel_id` varchar(50) NOT NULL COMMENT '渠道标识',
  `base_ver` varchar(50) NOT NULL COMMENT '包版本',
  `cdn_path` varchar(255) NOT NULL DEFAULT '' COMMENT '热更新地址',
  `is_constraint` varchar(50) NOT NULL COMMENT '是否强制更新,1.是 0.否',
  `constraint_path` varchar(255) NOT NULL DEFAULT '' COMMENT '强制更新地址',
  `all_version` varchar(50) NOT NULL COMMENT '生产服版本',
  `white_version` varchar(50) NOT NULL COMMENT '白名单版本',
  `server_list_url` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器列表地址',
  `is_ts` varchar(50) NOT NULL DEFAULT '' COMMENT '是否提审加密',
  PRIMARY KEY (`id`),
  UNIQUE KEY `version_management_channel_id_base_ver` (`channel_id`,`base_ver`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

//用户步骤表
$sqls[] = "CREATE TABLE `user_step` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` bigint(20) unsigned NOT NULL COMMENT '用户ID',
  `step_id` int(10)  NOT NULL DEFAULT '0' COMMENT '步骤ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$sqls[] = "CREATE TABLE `friend_love` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	`uid` bigint(64) NOT NULL COMMENT 'uid',
	`fuid` bigint(64) NOT NULL COMMENT 'fuid',
	`love` int(10) DEFAULT '0' COMMENT '亲密度',
	`level` int(10) DEFAULT '0' COMMENT '等级',
	`status` int(10) DEFAULT '0' COMMENT '状态：0.正常， 1.删除',
	KEY `id` (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

  //客服聊天表
$sqls[] = "CREATE TABLE `service_chat_log` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	`uid` bigint(64) DEFAULT '0',
	`is_service` int(10) DEFAULT '0' COMMENT '0.用户  1.客服',
	`content` text COMMENT '聊天内容',
	`send_time` int(12) DEFAULT '0' COMMENT '发送时间',
	`is_read` int(10) DEFAULT '0' COMMENT '0.未读  1.已读',
	`is_close` int(10) DEFAULT '0' COMMENT '0.开启  1.关闭',
	`from` varchar(50) DEFAULT '' COMMENT '客服帐号',
	PRIMARY KEY (`id`),
	KEY `idx_uid` (`uid`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
  
  //在线人数
  $sqls[] = "CREATE TABLE `user_on_line_count` (
	`date` int(12) DEFAULT '0' COMMENT '跑批时间',
	`count` int(10) DEFAULT '0' COMMENT '人数',
	UNIQUE KEY `date` (`date`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
  
  //在线时长
  $sqls[] = "CREATE TABLE `user_on_line_time` (
	`date` int(12) DEFAULT '0' COMMENT '跑批时间',
	`uid` bigint(64) NOT NULL COMMENT 'uid',
	`lineTime` int(12) DEFAULT '0' COMMENT '在线时长',
	KEY (`date`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
  
  $sqls[] = "CREATE TABLE `service_chat_automatic` (
	`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
	`uid` bigint(64) DEFAULT '0',
	`cId` varchar(20) NOT NULL DEFAULT '' COMMENT '点击问题',
	`click_time` int(12) DEFAULT '0' COMMENT '点击事件',
	PRIMARY KEY (`id`),
	KEY `idx_uid` (`uid`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

foreach ($sqls as $sql){
	$rt = $db->query($sql);
	if (empty($rt)){
		echo $sql;
	}
	echo $rt;
}

foreach ($flowSqls as $flowSql){
    $result = $flowDb->query($flowSql);
    if (empty($result)){
        echo $flowSql;
    }
    echo $result;
}

echo PHP_EOL;

