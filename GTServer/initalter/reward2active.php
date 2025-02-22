<?php

require_once dirname(__FILE__) . '/../public/common.inc.php';
$AUTO_INCREMENT_START = 10086;

//服务器ID
$sevid = intval($_SERVER['argv'][1]);

if (empty($sevid)) {
    exit('错误啦!!!!!!!!!');
}
$SevidCfg = Common::getSevidCfg($sevid);
echo PHP_EOL . 'init,server id = ' . $SevidCfg['sevid'] . PHP_EOL;
if ($SevidCfg['sevid'] <> 999) {
    $AUTO_INCREMENT_START = $SevidCfg['sevid'] * 1000000;
}


if (0 > $SevidCfg['sevid']) {
    exit('SERVER_ID invalid');
}
$db = Common::getMyDb();
$table_div = Common::get_table_div();
$sqls = array();
for ($i = 0; $i < $table_div; $i++) {
    
}

if ($SevidCfg['sevid'] == 999 || $SevidCfg['sevid'] == 1) {
    // 运营兑换活动
    $sqls[] = "
CREATE TABLE IF NOT EXISTS `reward2active` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `actkey` varchar(32) NOT NULL DEFAULT '' COMMENT '兑换活动的标识,充值活动：recharge',
    `awardno` varchar(64) NOT NULL COMMENT '奖励唯一标识',
    `type` varchar(32) NOT NULL DEFAULT '' COMMENT '奖励的档次',
    `pf` varchar(64) NOT NULL DEFAULT '' COMMENT '渠道平台',
    `sid` int(11) NOT NULL DEFAULT '0' COMMENT '服务器ID',
    `uid` bigint(64) NOT NULL DEFAULT '0' COMMENT '兑换者UID',
    `ctime` int(11) unsigned DEFAULT '0' COMMENT '兑换时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_acode` (`actkey`,`awardno`) USING BTREE,
    KEY `idx_act` (`actkey`,`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运营活动兑换记录表';";
}


foreach ($sqls as $sql) {
    $rt = $db->query($sql);
    if (empty($rt)) {
	echo $sql;
    }
    echo $rt;
}


echo PHP_EOL;

