<?php 
/**
 * 跨服帮会战脚本
 * 
 */
set_time_limit(0);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
Common::loadModel("Master");
$serverList = ServerModel::getServList();

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;


if ( is_array($serverList) ) {
	foreach ($serverList as $k => $v) {
		if ( empty($v) ) {
			continue;
		}
		$Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID
		
		echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;
		
		//如果不是主服务器  跳过
		if($Sev_Cfg['sevid'] != Game::club_pk_serv($Sev_Cfg['sevid'])){
			echo PHP_EOL, '>>>不是主服务器-跳过',$Sev_Cfg['sevid'],Game::club_pk_serv($Sev_Cfg['sevid']), PHP_EOL;
			continue;
		}
		
		if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {
			echo PHP_EOL, '>>>跳过', PHP_EOL;
			continue;
		}
		
		if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
			&& $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
			echo PHP_EOL, '>>>从服跳过', PHP_EOL;
			continue;
		}
		
		
		//匹配
		do_club_match();
		$Sev50Model = Master::getSev50();
        print_r($Sev50Model->info);
		
	}
}

Master::click_destroy();

echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();



/**
 * 匹配
 */
function do_club_match(){
	
	global $Sev_Cfg;
	
	$clubpk = Game::get_peizhi('clubpk');
	$upnum = empty($clubpk['upnum'])?10:$clubpk['upnum'];
		
	$Sev52Model = Master::getSev52();
	if(empty($Sev52Model->info)){
		echo PHP_EOL, '>>>无参赛数据', PHP_EOL;
		return 0;
	}
	
	$save_clubs = array();
	$k = 10000;
	foreach( $Sev52Model->info  as $cid => $fcinfo ){
		//过滤未获取参赛资格的
		$Sev51Model = Master::getSev51($cid,Game::get_sevid_club($cid));
		if(  count($Sev51Model->info['list']) < $upnum){
			echo $cid."未获取参赛资格".$upnum.'人数:'.count($Sev51Model->info['list'])."\n";
			continue;
		}
		$k ++; //防止重复
		$save_clubs[$fcinfo.$k] = $cid;
	}
	
	if(empty($save_clubs)){
		echo PHP_EOL, '>>>无参赛公会', PHP_EOL;
		return 0;
	}
	//排序
	krsort($save_clubs);
	$save_clubs = array_values($save_clubs);
	
	//加入匹配列表
	$Sev50Model = Master::getSev50();
	for($i = 0; $i < count($save_clubs) ; $i +=2 ){
		$cid = $save_clubs[$i];
		$fcid = empty($save_clubs[$i+1])?0:$save_clubs[$i+1];
		$Sev50Model->add($cid,$fcid);
		if(!empty($fcid)){
			$Sev50Model->add($fcid,$cid);
		}
		echo $cid.' VS '.$fcid ." 匹配\n";
	}
	return 1;
}







