<?php 
/**
 * 跨服帮会战脚本
 * 
 */
set_time_limit(0);
ini_set('memory_limit','3000M');
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
Common::loadModel("Master");
Common::loadModel("ClubModel");
$serverList = ServerModel::getServList();

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;


if ( is_array($serverList) ) {
	foreach ($serverList as $k => $v) {
		if ( empty($v) ) {
			continue;
		}
		
		if(!empty($serverID) && $serverID != $v['id']){
			continue;
		}
		
		$Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID
		
		echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;
		
		if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {
			echo PHP_EOL, '>>>跳过', PHP_EOL;
			continue;
		}
		
		if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
			&& $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
			echo PHP_EOL, '>>>从服跳过', PHP_EOL;
			continue;
		}
		
		//PK
		do_club_post();
		
	}
}


Master::click_destroy();


echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();




function do_club_post(){
	
	global $Sev_Cfg;
	$cache 	= Common::getCacheBySevId($Sev_Cfg['sevid']);
	$db = Common::getDbBySevId($Sev_Cfg['sevid']);
	//获取所有门客
	$key = 'club_redis';
	$redis = Common::getDftRedis();
	$rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
	
	if(empty($rdata)){
		return false;
	}
	
	foreach($rdata as $ck => $cv){
		
		$ClubModel = new ClubModel($ck);
		if(empty($ClubModel->info['members'])){
			$ClubModel->del_club($ck);
			echo $ck."  已删除\n";
		}
		unset($ClubModel,$ck,$cv);
	}
	
	return true;
	
}










