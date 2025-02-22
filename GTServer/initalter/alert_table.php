<?php
/**
 * @author WenYJ <wenyanji@youdong.com>
 * 20171208,版本上线，数据库表更新
 */
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
$serverList = ServerModel::getServList();
if (is_array($serverList)) {
    foreach ($serverList as $k => $v) {
        if (empty($v)) {
            continue;
        }
        $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
        if ( 999 == $SevidCfg1['sevid'] && (!defined('IS_TEST_SERVER') || false == IS_TEST_SERVER)) {
            continue;
        }
        if ( 0 < $serverID && $serverID != $SevidCfg1['sevid'] ) {
            continue;
        }
        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
            continue;
        }
	
	echo 'S' . $v['id'] . ' 开始处理' . PHP_EOL;
        $flowDb = Common::getMyDb('flow');
	$table_div = Common::get_table_div();
        for ($i = 0; $i < $table_div; $i++) {
	    $tableid = Common::computeTableId($i);
            $table = 'flow_record_' . $tableid;
            $sql = "ALTER TABLE " . $table ." "
		    . "MODIFY COLUMN `cha`  bigint(64) NULL DEFAULT 0 COMMENT '差值' AFTER `itemid`,"
		    . "MODIFY COLUMN `next`  bigint(64) NULL DEFAULT 0 COMMENT '新值' AFTER `cha`;";
	    if ( !$flowDb->query($sql) ) {
		echo 'S' . $v['id'] . ' ' . $table . '处理失败' . PHP_EOL;
	    }
            
	    
	    //流水详情表
	    $table = 'flow_records_' . $tableid;
	    $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
	    `flowid` bigint(64) DEFAULT 0 COMMENT '流水事件id',
	    `type` int(12) DEFAULT 0 COMMENT '配置id',
	    `itemid` varchar(255) DEFAULT NULL COMMENT '多种含义',
	    `cha` bigint(64) DEFAULT 0 COMMENT '差值',
	    `next` bigint(64) DEFAULT 0 COMMENT '新值'
	    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            if ( !$flowDb->query($sql) ) {
		echo 'S' . $v['id'] . ' ' . $table . '处理失败' . PHP_EOL;
	    }
        }
	
	//消费数据库
        $table = 'flow_consume';
	$sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
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

	if ( !$flowDb->query($sql) ) {
	    echo 'S' . $v['id'] . ' ' . $table . '处理失败' . PHP_EOL;
	}
	
	echo 'S' . $v['id'] . ' 处理结束' . PHP_EOL;
    }
}
exit('end');
