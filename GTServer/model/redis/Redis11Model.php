<?php
require_once "RedisBaseModel.php";
/*
 * 公会盟战积分
 */
class Redis11Model extends RedisBaseModel
{
	public $comment = "公会盟战积分";
	public $act = 'clubkuajf';//活动标签
	protected $_server_type = 4;//1：合服，2：跨服，3：全服
	public $_server_kua_key = 'clubpk';//指定跨服配置对应的key
	
	public $b_mol = "ranking";//返回信息 所在模块
	public $b_ctrl = "clubkuajf";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(
	/*
		联盟id => 联盟积分
	*/
	);
	
	
	//获取个人信息
	public function getMember($member,$rid){
		//玩家信息
		$ClubModel = Master::getClub($member);
		$fuidInfo = array();
		$fuidInfo['servid'] = Game::get_sevid_club($member);
		$fuidInfo['cname'] = $ClubModel->info['name'];
		$fuidInfo['score'] = parent::zScore($member);
		$clubBase = $ClubModel->getSimple();
        $fUserModel = Master::getUser($clubBase['mzUID']);
		$fuidInfo['mzname'] = Game::filter_char($fUserModel->info['name']);
		$fuidInfo['rid'] = $rid;
		
		return $fuidInfo;
	}
	/*
	 * 返回排行信息
	 */
	public function back_data_my($member){
		$fuidInfo = array();
		$ClubModel = Master::getClub($member);
		$fuidInfo['servid'] = Game::get_sevid_club($member);
		$fuidInfo['cname'] = $ClubModel->info['name'];
		$fuidInfo['score'] = parent::zScore($member);
		$fuidInfo['rid'] = parent::get_rank_id($member);
		Master::back_data(0,$this->b_mol,"myclubkuaRid",$fuidInfo);
	}
	
}

