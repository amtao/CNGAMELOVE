<?php
/*
 * 新建违法用户流水表
 * */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

$Sev_Cfg = Common::getSevidCfg(1);//子服ID

echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

buide();

exit();


function buide(){
    $flowDb = Common::getDftDb('flow');
    $sql = "CREATE TABLE `illegal` (
  `id` bigint(64) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(64) DEFAULT '0',
  `type` int(12) DEFAULT NULL COMMENT '类型 1:翰林院 2.。。。',
  `time` int(12) DEFAULT '0' COMMENT '时间',
  `tjson` text COMMENT '参数数据',
  PRIMARY KEY (`id`),
  KEY `uidtype` (`uid`,`type`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;";
    if($flowDb->query($sql)){
        echo 1;
    }else{
        echo 0;
    }
}
