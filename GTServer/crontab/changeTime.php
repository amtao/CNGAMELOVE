<?php 
/**
 * 后台配置文件脚本
 * 调用方式：每分钟跑一次
 * 
 */
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
$SevidCfg = Common::getSevidCfg(1);
$cache = Common::getDftMem ();
$key = "change_time_key_yushangxian";
$data = $cache->get($key);
if ($data['status'] == 0){
    $str = "date -s '".$data['time']."' && /etc/init.d/crond restart ";
    system($str);
    $data['status'] = 1;
    $cache->set($key, $data);
}
