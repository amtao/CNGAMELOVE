<?php
require_once "ActHDBaseModel.php";

/*
 * 活动295 发红包
 */
class Act295Model extends ActHDBaseModel
{
	public $atype = 295;//活动编号
	public $comment = "发红包";
	public $b_mol = "hbhuodong";//返回信息 所在模块
	public $b_ctrl = "info";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_295';//活动配置文件关键字

	/*
	 * 初始化结构体
	 */
	public $_init =  array(

	);

	/**
	 * 获取是否有红点  (可领取)
	 * $news 0:不可以领取   1:可以领取
	 */
	public function get_news(){
		$news = 0; //不可领取
		$cid = $this->getClubid($this->uid);
		if($cid){
			$Sev16Model = Master::getSev16($cid);
			$news = $Sev16Model->isGetHb($this->uid);
			if($news == 0){
				$Act43Model = Master::getAct43($this->uid);
				$news = $Act43Model->get_news();
			}
		}
		return $news;
	}

	/**
	 * 领红包
	 * @param $fuid
	 * @param $id
	 */
	public function getHb($fuid,$id){
		if(parent::get_state() == 0){
			Master::error(ACTHD_OVERDUE);
		}
		$cid = $this->getClubid($this->uid);
		if(empty($cid)){
			Master::error(CLUB_NO_HAVE_JOIN);
		}
		$Sev16Model = Master::getSev16($cid);
		$Sev16Model->getHb($this->uid,$fuid,$id);
		$Sev16Model->bake_data();
		$this->back_data();
	}

	/**
	 * 发红包
	 * @param $id
	 * @param $type 0:不炫耀 1:炫耀
	 */
	public function sendHb($id,$type = 0){
		$state = self::get_state();
		if($state == 0){
			Master::error(ACTHD_OVERDUE);
		}
		$cid = $this->getClubid($this->uid);
		if(empty($cid)){
			Master::error(CLUB_NO_HAVE_JOIN);
		}
		//判断是否可以使用 返回金额
		$Act43Model = Master::getAct43($this->uid);
		$money = $Act43Model->isuse($id);

		$ClubModel = Master::getClub($cid);
		$cfg_club_id = Game::getcfg_info('club',$ClubModel->info['level']);
		$maxMember = empty($cfg_club_id['maxMember']) ? 10 : ceil($cfg_club_id['maxMember']*0.8);

		$Act44Model = Master::getAct44($this->uid);
		$Act44Model->add($id,$maxMember,$money);

		$this->getLastHb();
		//添加跑马灯信息
		if($type){
			$UserInfo = Master::fuidInfo($this->uid);
			$Sev91Model = Master::getSev91();
			$Sev91Model->add_msg(array(102,Game::filter_char($ClubModel->info['name']),Game::filter_char($UserInfo['name'])));
		}
		$this->back_data();
		Game::cmd_other_flow($cid , 'hbhuodong', 'sendHb', array($this->uid => $this->uid.'_'.$id), 50, 1, $money,$money);
	}

	/**
	 * 添加红包券
	 * @param $money
	 */
	public function addRedTicket($money){
		if(parent::get_state() == 0 || parent::get_state() == 2){
			return;
		}

		$recharge = $this->hd_cfg['recharge'] ? $this->hd_cfg['recharge'] : array(328,648);

		if(!in_array($money,$recharge)){
			return;
		}

		$cid = $this->getClubid($this->uid);
		if(empty($cid)){
			return;
		}
		$Act43Model = Master::getAct43($this->uid);
		$Act43Model->add($money);
	}

	/**
	 * 获取联盟id
	 * @param $uid
	 * @return int
	 */
	public function getClubid($uid){
		$Act40Model = Master::getAct40($uid);
		return empty($Act40Model->info['cid']) ? 0 : $Act40Model->info['cid'];
	}

	/**
	 * 退盟移除联盟生效的红包信息
	 * @param $cid
	 */
	public function removeHb($cid){
		if(parent::get_state() == 0){
			return;
		}
		$Sev16Model = Master::getSev16($cid);
		$Sev16Model->delHb($this->uid);
	}

	/**
	 * 返回红包详细信息
	 */
	public function back_data_hd(){
		if(parent::get_state() == 0){
			Master::error(ACTHD_OVERDUE);
		}

		$cid = $this->getClubid($this->uid);
		if(empty($cid)){
			Master::error(CLUB_NO_HAVE_JOIN);
		}
		//红包列表
		$Sev16Model = Master::getSev16($cid);
		$Sev16Model->bake_data();
		//我的红包券列表
		$Act43Model = Master::getAct43($this->uid);
		$Act43Model->back_data();
		//我的红包列表
		$Act44Model = Master::getAct44($this->uid);
		$Act44Model->back_data();
		//已领红包列表
		$Act45Model = Master::getAct45($this->uid);
		$Act45Model->back_data();


		$Act49Model = Master::getAct49($this->uid);
		$Act49Model->back_data();
	}

	/**
	 * 获取红包详情
	 * @param $fuid
	 * @param $id
	 */
	public function getHbInfo($fuid,$id){
		if(parent::get_state() == 0){
			Master::error(ACTHD_OVERDUE);
		}
		$cid = $this->getClubid($fuid);
		if(empty($cid)){
			Master::error(CLUB_NO_HAVE_JOIN);
		}

		$Act44Model = Master::getAct44($fuid);
		$Act44Model->getHbInfo($id);
	}

	/**
	 * 获取当前联盟最后一个红包信息
	 */
	public function getLastHb(){
		if(parent::get_state() == 0){
			return;
		}
		$cid = $this->getClubid($this->uid);
		if(empty($cid)){
			return;
		}
		$Sev16Model = Master::getSev16($cid);
		$data = $Sev16Model->getLastHb();
		if(!empty($data)){
			$Act44Model = Master::getAct44($data['uid']);
			if($Act44Model->info[$data['id']]){
				$UserModel = Master::getUser($data['uid']);
				$info = array(
					'name' => $UserModel->info['name'],
					'stime' => $Act44Model->info[$data['id']]['stime']
				);
				Master::back_data($this->uid,$this->b_mol,'lastHb',$info);
			}
		}
	}

	public function resetNews(){
		if(parent::get_state() == 0){
			return;
		}
		$this->back_data();
	}

	/**
	 * 排行榜
	 */
	public function getPaihang(){
		if(parent::get_state() == 0){
			Master::error(ACTHD_OVERDUE);
		}
		$key = $this->hd_id.'_'.$this->uid.'_paihang';
		$mcache = Common::getDftMem();
		$old_time = $mcache->get($key);
		if(empty($old_time) || Game::get_now() >= $old_time){
			$Redis31Model = Master::getRedis31($this->hd_cfg['info']['id']);
			$Redis31Model->back_data();
			$Redis31Model->back_data_my($this->uid);
			$mcache->set($key,Game::get_now()+10);
		}else{
			Master::back_s(2);
		}
	}
}




