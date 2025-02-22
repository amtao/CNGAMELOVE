<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/19
 * Time: 15:10
 */


set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
$uid = intval($_SERVER['argv'][1]);// 默认是全部区


function get_sevid($uid)
{
    if ($uid == 0) {
        if (defined('IS_TEST_SERVER') && IS_TEST_SERVER) {
            return 999;
        }
        exit;
    }
    if ($uid < 1000000) {
        if (defined('IS_TEST_SERVER') && IS_TEST_SERVER) {
            return 999;
        }
        exit;
    } else {
        return intval($uid / 1000000);
    }
}

$serverid = get_sevid($uid);

$SevidCfg = Common::getSevidCfg($serverid);
Common::loadModel('ServerModel');
$serverid = ServerModel::getDefaultServerId();
$db = Common::getDbBySevId($serverid);

$UserModel = Master::getUser($uid);

//实例化redis
//Common::loadRedisModel('Redis1Model');
//$redis1 = new Redis1Model();
////获取势力值
//$value = $redis->zScore($uid);
////获取排名
//$rid = $redis->get_rank_id($uid);

echo 'uid:'.$uid.'|platform:'.$UserModel->info['platform'].'|rid:'.$rid.'|value:'.$value.PHP_EOL;


Master::click_destroy();
