<?php
/**
 * Created by PhpStorm.
 * User: 'Mr.Chen'
 * Date: 2019/4/25
 * Time: 10:04
 */

set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
$uid = intval($_SERVER['argv'][1]);// 默认是全部区
$count = intval($_SERVER['argv'][2]);// 默认是全部区


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
$title = '國力慶典徒弟聯姻問題補償';
$content = '親愛的小主：

請查收您的5月1日國力慶典徒弟聯姻問題補償。
謝謝小主對我們的支持，祝您寵冠六宮！';
$mailModel = Master::getMail($uid);
$mailModel->sendMail($uid, $title, $content, 1, array(
    array('id'=>1,'count'=>intval($count) * 20),//招募令
));
echo "success:[{$uid}];".PHP_EOL;
Master::click_destroy();

