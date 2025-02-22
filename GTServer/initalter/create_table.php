<?php
require_once dirname(dirname(__FILE__)) . '/common.inc.php';
Common::loadModel('ServerModel');
$serverList = ServerModel::getServList();
if (is_array($serverList)) {
    foreach ($serverList as $k => $v) {
        if (empty($v)) {
            continue;
        }
        if ( 999 == $v['id']) {
            continue;
        }
        $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID

        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
            continue;
        }
        $db = Common::getMyDb();
        $sql = "CREATE TABLE `device` (
  `uid` mediumint(16) unsigned NOT NULL DEFAULT '0' COMMENT '玩家UID',
  `device` varchar(64) NOT NULL DEFAULT '' COMMENT '设备号',
  `platform` varchar(64) NOT NULL DEFAULT '' COMMENT '平台',
  `time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '时间',
  `param` varchar(1024) NOT NULL DEFAULT '' COMMENT '其他参数',
  PRIMARY KEY (`device`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $result = $db->query($sql);
        if ($result){
            echo $v['id'].'设备表创建成功';
        }

        unset($sql, $result);
        $sql = "CREATE TABLE `risk_device` (
  `device` varchar(64) NOT NULL DEFAULT '' COMMENT '设备号',
  `value` mediumtext,
  PRIMARY KEY (`device`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $result = $db->query($sql);
        if ($result){
            echo $v['id'].'风险表创建成功';
        }
        unset($sql, $result);
    }
}
