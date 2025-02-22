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
$title = '宫殿宫斗冲榜';
$content = '亲爱的小主：

恭喜你所在的宫殿在宫殿宫斗冲榜活动获得第 1 名,请收下活动奖励

《宫廷秘传》官方运营团队';
$mailModel = Master::getMail($uid);
$mailModel->sendMail($uid, $title, $content, 1, array(
array('id'=>1200,'count'=>10),//招募令			
array('id'=>13,'count'=>3),//气势丸			
array('id'=>23,'count'=>3),//智谋丸			
array('id'=>33,'count'=>3),//政略丸			
array('id'=>43,'count'=>3),//魅力丸			
array('id'=>1123,'count'=>5),//幸运丸			
array('id'=>52,'count'=>5),//属性散			
array('id'=>1122,'count'=>10),//幸运散			
array('id'=>71,'count'=>10),//知己精力丹			
array('id'=>73,'count'=>10),//徒弟活力丹			
array('id'=>122,'count'=>5),//办差令			
array('id'=>150,'count'=>5),//车马令			
array('id'=>156,'count'=>5),//精铁腰牌			
array('id'=>92,'count'=>10),//翡翠心			
array('id'=>901,'count'=>5),//寒潭香			
array('id'=>107,'count'=>5),//麝香囊			
array('id'=>51,'count'=>5),//属性药水			
array('id'=>81,'count'=>20),//资质经验书
));
echo "success:[{$uid}];".PHP_EOL;
Master::click_destroy();

