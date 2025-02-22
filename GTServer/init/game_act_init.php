<?php

require_once dirname( __FILE__ ) . '/../public/common.inc.php';
$AUTO_INCREMENT_START = 10086;

//服务器ID
$sevid = intval($_SERVER['argv'][1]);

if (empty($sevid)){
    exit('错误啦!!!!!!!!!');
}

$start = $sevid <> 999 ? 1 : $sevid;
$end = $sevid;

for ($sevid = $start; $sevid <= $end; $sevid++) {
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

    $table = 'game_act';
    $sqls[]="drop table if exists `{$table}`;";
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

    $table = 'game_act_template';
    $sqls[]="drop table if exists `{$table}`;";
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

    $table = 'game_config';
    $sqls[]="drop table if exists `{$table}`;";
    $sqls[]="CREATE TABLE `{$table}`(
        `id` bigint(64) NOT NULL AUTO_INCREMENT,
        `config_key` varchar(60) NOT NULL COMMENT '活动key',
        `server` varchar(255) DEFAULT 'all' COMMENT '服数',
        `contents` mediumtext DEFAULT NULL COMMENT '活动内容',
        `status` tinyint(1) DEFAULT 0 COMMENT '记录状态：0正常，1删除',
        PRIMARY KEY (`id`)
    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

    foreach ($sqls as $sql){
        $rt = $db->query($sql);
        if (empty($rt)){
            echo $sql;
        }
        echo $rt;
    }
}
echo PHP_EOL;

