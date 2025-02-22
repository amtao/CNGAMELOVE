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
$title = '【补偿奉上】活动进度重置补偿';
$content = '亲爱的小主们：

4月23日游戏中出现了活动重置问题，这是我们的工作人员为小主发放的二次补偿，此次补偿内容高于活动奖励价值，请小主查收。
对于此次服务器供应商故障导致的问题，我们再次致以诚挚的歉意，望小主海涵~

《宫廷秘传》官方运营团队';
$mailModel = Master::getMail($uid);
$mailModel->sendMail($uid, $title, $content, 1, array(
    array('id'=>1200,'count'=>10),//招募令
    array('id'=>1122,'count'=>10),//幸运散
    array('id'=>903,'count'=>10),//花签
    array('id'=>61,'count'=>10),//气势强化书卷
    array('id'=>62,'count'=>10),//智谋强化书卷
    array('id'=>63,'count'=>10),//政略强化书卷
    array('id'=>64,'count'=>10),//魅力强化书卷
    array('id'=>1247,'count'=>20),//幸运手札
    array('id'=>1249,'count'=>20),//幸运银票
    array('id'=>1248,'count'=>20),//幸运名帖
    array('id'=>21110,'count'=>10),//珍宝线索
    array('id'=>81,'count'=>10),//资质经验书
    array('id'=>72,'count'=>5),//出城体力丹
    array('id'=>73,'count'=>5),//徒弟活力丹
    array('id'=>71,'count'=>5),//知己精力丹
    array('id'=>77,'count'=>5),//书卷礼包
    array('id'=>1121,'count'=>5),//幸运药水
    array('id'=>5004,'count'=>5),//木槿
));
echo "success:[{$uid}];".PHP_EOL;
Master::click_destroy();

