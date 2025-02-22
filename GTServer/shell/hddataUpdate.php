<?php 
/**
 * 活动280数据错误
 * 调用方式：手动跑
 * 
 */
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('HoutaiModel');
Common::loadModel('lock/MyLockModel');
Common::loadModel('MailModel');

$serverID = intval($_SERVER['argv'][1]);// 默认是全部区

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

//服务器过滤

$SevidCfg = Common::getSevidCfg($serverID);//子服ID
echo PHP_EOL, '服务器ID：', $SevidCfg['sevid'], PHP_EOL;
if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg['sevid'] ) {
	echo PHP_EOL, '>>>跳过', PHP_EOL;
	exit();
}
if ( 0 < $serverID && $serverID != $SevidCfg['sevid'] ) {
	echo PHP_EOL, '>>>跳过', PHP_EOL;
	exit();
}
if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
	&& $SevidCfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
	echo PHP_EOL, '>>>从服跳过', PHP_EOL;
	exit();
}

$key = 'huodong_254_20171208_redis';
$redis = Common::getDftRedis();
$rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
//if($hd_info['info']['switch'] == 1){//状态未1时必须有对应的跨服衙门活动开启
	$hd_info_300 = HoutaiModel::get_huodong_info('huodong_300');
	if(!empty($hd_info_300)){
		//分组
		$Sev61Model = Master::getSev61();
		$Sev61Model->grouping($hd_info_300['info']['id'],$hd_info_300['info']['max_rank'],$hd_info_300['limit'],$hd_info_300['server'],$hd_info_300['recover']);
		$hid = $hd_info_300['info']['id'].'_'.$SevidCfg['he'];
		$redis307Model = Master::getRedis307($hid);
	}
//}

if(empty($rdata)){
	echo $key."无排行信息\n";
	return false;
}

$rid = 0; //排名
foreach($rdata as $uid => $score) {
	$rid++;
	if (!empty($hd_info_300) && $rid <= 100) {
		echo $uid,PHP_EOL;
		$redis307Model->zAdd($uid, 0);
		//加用户锁 堵塞3秒
		$LockModel = new MyLockModel("user_" . $uid);
		$uid_Lock = $LockModel->getLock(3);

		$Act306Model = Master::getAct306($uid);
		$Act306Model->add();
		$Act306Model->ht_destroy();
		//解用户锁
		if (null != $uid_Lock) {
			$LockModel->releaseLock();
		}
		$mailModel = new MailModel($uid);
		$mailModel->sendMail($uid, '跨服衙门预选赛结果', '恭喜你获得跨服衙门正式赛资格',0,0);
		$mailModel->destroy();
		unset($mailModel);
	}
}

Master::click_destroy();
echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();