<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/29
 * Time: 10:42
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

// 邮件发放奖励
$title = '【补偿奉上】国力庆典数据异常补偿';
$content = '亲爱的小主：

请查收您在本次国力庆典中，因数据异常而发放的补偿。
谢谢小主对我们的支持，祝您宠冠六宫！';
$mailModel = Master::getMail($uid);
$mailModel->sendMail($uid, $title, $content, 1, array(
    array('id'=>909,'count'=>100),
));
echo "success:[{$uid}];".PHP_EOL;
Master::click_destroy();

