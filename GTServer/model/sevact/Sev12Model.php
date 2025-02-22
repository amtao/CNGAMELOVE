<?php
/*
 * 联盟-boss血量
 */
require_once "SevBaseModel.php";
class Sev12Model extends SevBaseModel
{
	public $comment = "联盟-boss血量";
	public $act = 12;//活动标签

	public $stime = 0;//当天开始时间戳
	public $etime = 0; //当天结束时间戳

	public $_init = array(//初始化数据
		/*
		    bossid => hp,
		 */
		'currentCbid' => 0,//当前所打bossid
		'bosshp' => 0,//当前boss剩余血量
		'startBossTime' => 0,//开始的时间戳
	);

	public function getTodayTime(){
		$startTime = Game::getcfg_param('club_bossOpenTime');
		$startArr = explode('|',$startTime);
		$endTime = Game::getcfg_param('club_bossEndTime');
		$endArr = explode('|',$endTime);
		$this->stime = mktime($startArr[0],$startArr[1],00,date("m"),date("d"),date("y"));//当天开始时间戳
		$this->etime = mktime($endArr[0],$endArr[1],00,date("m"),date("d"),date("y"));//当天结束时间戳
	}


	public function checkNewBoss($uid){
		if($this->checkBossCanOpen($uid)){
			$this->open_club_boss($uid,$this->info['currentCbid']+1,false,1);
		}
	}

	public function checkBossCanOpen($uid){
		if($this->info['currentCbid'] <= 0 || $this->info['bosshp'] > 0){
			return false;
		}

		if(!empty($this->info['startBossTime']) && !$this->bossFightTime()){
			return false;
		}

		$cbid = $this->info['currentCbid']+1;
		$cfg_club_boss_cfg = Game::getcfg('club_boss');
		$cfg_club_boss = $cfg_club_boss_cfg[$cbid];
		if(empty($cfg_club_boss) || empty($cfg_club_boss_cfg)){
			return false;
		}
		//判断当前联盟等级是否可以攻打该副本
		$Act40Model = Master::getAct40($uid);
		$cid = $Act40Model->info['cid'];
		$ClubModel = Master::getClub($cid);
		$buildInfo = $ClubModel->getBuildInfo(3);
		if($buildInfo['lv'] < $cfg_club_boss['level'] ){
			return false;
		}
		return true;
	}

	//开启boss默认开启第一个
	public function open_club_boss($uid,$cbid = 1,$isPay = false,$type){
		if($isPay){
			self::bossTime();
		}else{
			if(!$this->bossFightTime()){
				Master::error(CLUB_COPY_RESTING_BOSS,2);
			}
		}
		if(!empty($this->info['currentCbid']) && $isPay && $this->bossFightTime()){
			Master::error(CLUB_COPY_OPEN);
		}

		$cfg_club_boss_cfg = Game::getcfg('club_boss');
		$cfg_club_boss = $cfg_club_boss_cfg[$cbid];
		if(empty($cfg_club_boss) || empty($cfg_club_boss_cfg)){
			Master::error('cfg_club_boss_err'.$cbid);
		}
		//判断当前联盟等级是否可以攻打该副本
		$Act40Model = Master::getAct40($uid);
		$cid = $Act40Model->info['cid'];
		$ClubModel = Master::getClub($cid);
		$buildInfo = $ClubModel->getBuildInfo(3);
		if($buildInfo['lv'] < $cfg_club_boss['level'] ){
			Master::error(CLUB_COPY_NEED_LEVEL_SHORT);
		}
		
		if($this->info['currentCbid'] > 0 && $this->info['bosshp'] > 0 && $this->bossFightTime()){
			Master::error(CLUB_COPY_NOT_DEAD);
		}

		if($isPay){	
			if($type == 1){
				$cost = $cfg_club_boss['payDia'];
				Master::sub_item($uid,KIND_ITEM,1,$cost);
			}else{
				$ClubModel->sub_fund($uid,$cfg_club_boss['payFund']);
			}
			$this->info['startBossTime'] = Game::get_now();
			$Sev13Model = Master::getSev13($cid);
			$Sev13Model->delLogs($cbid);
		}

		$this->info['currentCbid'] = $cbid;
		$this->info['bosshp'] = $cfg_club_boss['hp'];
		$this->save();

		//记录公会日志
		// $Sev15Model = Master::getSev15($cid);
		// if($kill_num > 0){
		// 	$Sev15Model->add_log(4,$uid,0,$money,$cbid);
		// }else{
		// 	$Sev15Model->add_log(4,$uid,0,$type,$cbid);
		// }

		//开启流水
		Game::cmd_other_flow($cid , 'ClubModel', 'openboss', array($cbid => $this->uid), 40, 1, 1, $this->info['bosshp']);

		//删除记录的缓存
		$this->delMsgCache();
	}

	/**
	 * 联盟boss  扣除血量
	 * @param unknown_type $cbid   bossID
	 * @param unknown_type $hit  要被扣除的伤害
	 */
	public function sub_club_boss_hp($cbid,$hit,$uid){
		if($this->info['currentCbid'] != $cbid){
			Master::error(CLUB_COPY_KILLED_BOSS,2);
		}

		if($this->info['bosshp'] <= 0){
			Master::error(CLUB_COPY_KILLED_BOSS,2);
		}
		if(!$this->bossFightTime()){
			Master::error(CLUB_COPY_ESCAPE_BOSS,2);
		}
		//扣除血量
		$ylhp = min($this->info['bosshp'],$hit);
		$this->info['bosshp'] -= $hit;
		if($this->info['bosshp'] <= 0){
			// if($this->checkBossCanOpen($uid)){
			// 	$this->open_club_boss($uid,$this->info['currentCbid']+1,false,1);
			// }
		}
		$this->save();
		
		return $hit;
	}

	/**
	 * 判断boss是否可以开启
	 */
	public function bossTime(){
		$this->getTodayTime();

		if($_SERVER['REQUEST_TIME'] < $this->stime){
			Master::error(CLUB_COPY_RESTING_BOSS,2);
		}
		if($_SERVER['REQUEST_TIME'] > $this->etime){
			Master::error(CLUB_COPY_ESCAPE_BOSS,2);
		}
	}

	//是否在boss战斗时间
	public function bossFightTime(){
		$intvel = 43200;
		if($this->info['startBossTime']+$intvel > Game::get_now()){
			return true;
		}
		return false;
	}

	public function bossIsEnd(){

		if($this->info['startBossTime'] > 0 && $this->info['startBossTime']+43200 < Game::get_now()){
			return true;
		}
		return false;
	}

	/*
	 * 返回协议信息
	 */
	public function bake_data(){
		$data = $this->info;
		Master::back_data(0,'club','bossInfo',$data);
	}
}





