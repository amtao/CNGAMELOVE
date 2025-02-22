<?php 
/**
 * 宫斗排行奖励下发
 * 调用方式：00 22 * * *
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
	$db = Common::getDbBySevId($serverID);
	$key = 'yamen_redis';
	$redis = Common::getDftRedis();
	$rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
	$rid = 0;
	if(!empty($rdata)){
		foreach($rdata as $uid => $score){
			if($score <= 0){
				break;
			}
			$rid++;
			$newRid = $rid;
			$mailModel = new MailModel($uid);
			$title = GONGDOU_TITLE;
			$tips = GONGDOU_CONTENT_1.'|'.$newRid.'|'.GONGDOU_CONTENT_2;
			$exchangeCfg = Game::getcfg('gongdou_rank');
			foreach($exchangeCfg as $v){
				if($newRid >= $v['max'] && $newRid <= $v['min']){
					//获取配置
					$mailModel->sendMail($uid,$title,$tips,1,$v['rwd']);
					$mailModel->destroy();
					Game::crontab_debug('-999-宫斗积分排行奖励: 玩家id: '.$uid."--已发\n",$serverID."gongdouAaward");
					break;
				}
			}
		}
		echo PHP_EOL, '----------------end----------------------', PHP_EOL;
	}else {
		Game::crontab_debug($key.CRONTAB_NO_RANK."\n");
		return false;
	}
    Master::click_destroy();
    echo "999执行成功";

    exit();
}

if ( is_array($serverList) ) {
	foreach ($serverList as $k => $v) {
		if ( empty($v) ) {
			continue;
		}
		$SevidCfg = Common::getSevidCfg($v['id']);//子服ID
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

		$btime1 = strtotime('now');
        $db = Common::getDbBySevId($SevidCfg['sevid']);
        $key = 'yamen_redis';
        $redis = Common::getDftRedis();
		$rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
		$rid = 0;
		if(!empty($rdata)){
            foreach($rdata as $uid => $score){
				if($score <= 0){
					break;
				}
				$rid++;
				$newRid = $rid;
				$mailModel = new MailModel($uid);
				$title = GONGDOU_TITLE;
				$tips = GONGDOU_CONTENT_1.'|'.$newRid.'|'.GONGDOU_CONTENT_2;
				$exchangeCfg = Game::getcfg('gongdou_rank');
				foreach($exchangeCfg as $v){
					if($newRid >= $v['max'] && $newRid <= $v['min']){
						//获取配置
						$mailModel->sendMail($uid,$title,$tips,1,$v['rwd']);
						$mailModel->destroy();
						Game::crontab_debug('宫斗积分排行奖励: 玩家id: '.$uid."--已发\n",$SevidCfg['sevid']."gongdouAaward");
						break;
					}
				}
            }
            echo PHP_EOL, '----------------end----------------------', PHP_EOL;
        }else {
            Game::crontab_debug($key.CRONTAB_NO_RANK."\n");
            return false;
        }
	}
}
Master::click_destroy();

exit();
