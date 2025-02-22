<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1
 * Time: 16:37
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../../public/common.inc.php';
$sid = intval($_SERVER['argv'][1]);
if(empty($sid)){
    echo '请输入区服',PHP_EOL;exit();
}
$Sev_Cfg = Common::getSevidCfg($sid);
$db = Common::getComDb();
    //创建SQL表
    $table = 'phone_num';
    //新创建sql表
    $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
  `uid` bigint(64) unsigned NOT NULL COMMENT '用户id',
  `phone` bigint(64) unsigned DEFAULT NULL COMMENT '手机号码',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$rt = $db->query($sql);
if($rt){
    echo '建表成功',PHP_EOL;
}else{
    echo '建表失败',PHP_EOL;
}