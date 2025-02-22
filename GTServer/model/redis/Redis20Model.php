<?php
require_once "RedisBaseModel.php";
/*
 * 酒楼-宴会排行榜
 */
class Redis20Model extends RedisBaseModel
{
	public $comment = "宴会排行榜";
	public $act = 'jiulou';//活动标签
	
	public $b_mol = "boite";//返回信息 所在模块
	public $b_ctrl = "yhList";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(
	/*
		uid => 宴会分数
	*/
	);
	
	
	/**
	 * 获取单个玩家信息
	 * @param $member
	 * @param $rid
	 */
	public function getMember($member,$rid){
		
		//玩家信息
		$fuidInfo = Master::fuidInfo($member);
		//玩家排名
		$fuidInfo['rid'] = $rid;
		//分数
		$fuidInfo['num'] = parent::zScore($member);
		
		return $fuidInfo;
		
	}
	
	/*
	 * 返回我的排行信息
	 */
	public function back_data_my($uid){
		$rid = parent::get_rank_id($uid);
		$score = parent::zScore($uid);
		Master::back_data(0,$this->b_mol,"myYhRid",array("rid"=>$rid,'score' => $score));
	}
	
}

/*
 * 通用联盟 势力 排行榜
 */
