<?php

/*
 * 1、输入区服
 * 2、遍历需要修改的数据
 *    2.1 redis数据
 *    2.2 sever数据
 *    2.3 数据库数据
 * 3、结束
 * */

set_time_limit(0);
ini_set('memory_limit','1500M');
require_once 'common.php';
//获取当前区服
$serverID = intval($_SERVER['argv'][1]);
$time = isset($_SERVER['argv'][2])?intval($_SERVER['argv'][2]):1;
if(empty($serverID)){
    echo '未输入需要遍历的区服',PHP_EOL;exit();
}
$SevCfg = Common::getSevidCfg($serverID);

$RedisCom = new redisCom();
$SeverCom = new severCom();
$DbCom = new dbCom();

//遍历redis
echo 'redis开始内存使用:'.memory_get_usage(),PHP_EOL;
$RedisCom->bianli($SevCfg,$time);
echo 'redis遍历完成',PHP_EOL;
echo '当前内存使用'.memory_get_usage(),PHP_EOL;
//遍历server
echo 'server开始内存使用:'.memory_get_usage(),PHP_EOL;
$SeverCom->merge($SevCfg);
echo 'server遍历完成',PHP_EOL;
echo '当前内存使用'.memory_get_usage(),PHP_EOL;
//遍历db
echo 'db开始内存使用:'.memory_get_usage(),PHP_EOL;
$DbCom->modifyDb($SevCfg);
echo 'db遍历完成',PHP_EOL;
echo '当前内存使用'.memory_get_usage(),PHP_EOL;
