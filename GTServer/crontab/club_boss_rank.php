<?php 
/**
 * club_boss排行奖励下发
 * 半个小时跑一次
 * 
 */
set_time_limit(0);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';

Common::loadModel('ServerModel');
Common::loadModel('UserModel');
Common::loadModel('MailModel');

$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
echo PHP_EOL, 'serverID=', $serverID, PHP_EOL;

$serverList = ServerModel::getServList();

echo PHP_EOL, '----------------begin----------------------', PHP_EOL;

$btime = strtotime('now');
Common::loadModel("Master");
$result = array();

$serverID = intval($_SERVER['argv'][1]);
if ($serverID == 999) {
    $SevidCfg = Common::getSevidCfg($serverID);//子服ID
    $crontabName = $serverID."_club_party_award";
	$db = Common::getDbBySevId($serverID);
    $sql = 'select * from `club`';
    $result = $db->fetchArray($sql);
    if(!empty($result)){
        foreach($result as $info){
            $ClubModel = Master::getClub($info['cid']);
            $ClubModel->sendRankAward();
        }
    }
    Master::click_destroy();
    Game::crontab_debug("-----------999执行成功-------------------", $crontabName);

    exit();
}

if ( is_array($serverList) ) {
	foreach ($serverList as $k => $v) {
		if ( empty($v) ) {
			continue;
		}
        $SevidCfg = Common::getSevidCfg($v['id']);//子服ID
        $serverID = $v['id'];
		echo PHP_EOL, '服务器ID：', $SevidCfg['sevid'], PHP_EOL;
		
		if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg['sevid'] ) {
			echo PHP_EOL, '>>>跳过', PHP_EOL;
			continue;
		}
		if ( 0 < $serverID && $serverID != $SevidCfg['sevid'] ) {
			echo PHP_EOL, '>>>跳过', PHP_EOL;
			continue;
		}
		if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
			&& $SevidCfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
			echo PHP_EOL, '>>>从服跳过', PHP_EOL;
			continue;
		}
        $crontabName = $serverID."_club_party_award";
		$btime1 = strtotime('now');
        $db = Common::getDbBySevId($serverID);
        $sql = 'select * from `club`';
        $result = $db->fetchArray($sql);
        if(!empty($result)){
            foreach($result as $info){
                $ClubModel = Master::getClub($info['cid']);
                $ClubModel->sendRankAward();
            }
        }
    }
}
Master::click_destroy();
Game::crontab_debug("-----------执行成功-------------------", $crontabName);

exit();
