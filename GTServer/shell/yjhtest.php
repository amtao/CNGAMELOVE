<?php 
/**
 * 跨服帮会战脚本
 * 
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
Common::loadModel("Master");
$serverList = ServerModel::getServList();

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;


$Sev_Cfg = Common::getSevidCfg($serverID);//子服ID

$Sev50Model = Master::getSev50(1);
print_r($Sev50Model->info);

baoming();
//createclub();

Master::click_destroy();

echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();


function createclub(){
	Common::loadModel("ClubModel");
	for($i = 5000001; $i <= 5000499 ; $i += 10){
		$Act40Model = Master::getAct40($i);
		$fcid = $Act40Model->info['cid'];
		if(!empty($fcid)){
			$ClubModel = Master::getClub($fcid);
			$Act40Model->outClub($fcid);
			$ClubModel->goout_club($i);
		}
		$data = array();
		$data['name'] = $i.'公';
		$data['weixin'] = '1321321';
		$data['qq'] = '1321321';
		$data['password'] = '1321321';
		$data['isJoin'] = 1;
		$cid = ClubModel::create_club($data);
		$ClubModel = Master::getClub($cid);
		$ClubModel->join_club($i,1);
		//更新公会个人信息
 		$Act40Model->inClub($cid,0);
		//创建并且返回联盟id
		for($j = $i +1 ; $j < $i +10 ;$j ++){
			$Act40Model = Master::getAct40($j);
			$fcid = $Act40Model->info['cid'];
			if(!empty($fcid)){
				$ClubModel = Master::getClub($fcid);
				$Act40Model->outClub($fcid);
				
			}
			$ClubModel = Master::getClub($cid);
	 		$ClubModel->join_club($j,4);
	 		
			//更新公会个人信息
			$Act40Model->info['cid'] = intval($cid);
			$Act40Model->info['inTime'] = $_SERVER['REQUEST_TIME'];
			$Act40Model->save();
		}
	}
}



/**
 * 匹配
 */
function baoming(){
	
	global $Sev_Cfg;
	$cache 	= Common::getCacheBySevId($Sev_Cfg['sevid']);
	
	//获取所有门客
	$key = 'club_redis';
	$redis = Common::getDftRedis();
	$rdata  = $redis->zRevRange($key, 0, -1,true);  //获取排行数据
	
	foreach($rdata as $ck => $cv){
		echo $ck."\n";
		
//		
//		//删除公会  and  删除redis
//		$ClubModel->del_club($ck);
		
		//获取公会成员
		$club_key = $ck.'_club';
		$all_list = $cache->get($club_key);
//		$all_list['exp'] = 57609;
//		$all_list['level'] = 4;
//		$cache->set($club_key,$all_list);
//		$cache->delete($ck.'_club_base_data');
		
		$ClubModel = Master::getClub($ck);
		
		foreach($all_list['members'] as $mk => $mv){
			
			$Sev54Model = Master::getSev54($ck);
			$Sev54Model->out_data($mk);
			
			//$ClubModel->add_exp($mk,10000);
			/*
			$HeroModel = Master::getHero($mk);
			$HeroModel->add_hero(1);
			$HeroModel->add_hero(2);
			$HeroModel->add_hero(3);
			$HeroModel->add_hero(4);
			$HeroModel->add_hero(5);
		*/
		
			echo $mk."\n";
			//获取门客
			$HeroModel = Master::getHero($mk);
			//获取可报名门客
			$hid = $HeroModel->get_one_hero();
			if(empty($hid)){
				continue;
			}
			//门客出战列表
	        $Act42Model = Master::getAct42($mk);
	        $Act42Model->reset_fight($hid);
	        
	        $Sev51Model = Master::getSev51($ck);
			$Sev51Model->baoming($mk,$hid);
			
			//$jnid = rand(280,285);
			$jnid = 285;
			$jnhid = $HeroModel->get_one_hero();
			if($hid != $jnhid){
				if($jnid != 280){
					$jnhid = 0;
				}else{
					$Act42Model->reset_fight($jnhid);
				}
				
				$Sev51Model->usejinnang($mk,$jnid,$jnhid);
			}
			
			echo '公会:'.$ck.'  uid:'.$mk.'  门客:'.$hid."\n";
		}
		
	}
	echo '公会个数:'.count($rdata)."\n";
	
}







