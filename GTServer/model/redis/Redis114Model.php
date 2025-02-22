<?php
require_once "RedisBaseModel.php";
/*
 * 丝绸之路分数排行
 */
class Redis114Model extends RedisBaseModel
{
	public $comment = "丝绸之路分数排行";
	public $act = 'trade';//活动标签
	
	public $b_mol = "trade";//返回信息 所在模块
	public $b_ctrl = "scoreRank";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(
	/*
		uid => 讨伐积分
	*/
	);
	
	
	/**
	 * 获取单个酒楼的信息
	 * @param $member  uid
	 * @param $rid   排名id
	 */
	public function getMember($member,$rid){
	    
	    //玩家信息
	    $fuidInfo = Master::fuidInfo($member);
	    //玩家排名
	    $fuidInfo['rid'] = $rid;
	    //获取分值
	    $fuidInfo['num'] = intval($this->zScore($member));
		
// 		$UserModel = Master::getUser($member);
// 		//获取公共基础信息
// 		$cinfo = array(
// 			'name' => $UserModel->info['name'],
// 			'rid' => $rid,
// 			'score' => parent::zScore($member),
// 		);
		return $fuidInfo;
	}
	
	/*
	 * 返回我的排行信息
	 */
	public function back_data_my($member){
		$name = '无';
		$rid = 100001;
		$score = 0;
		if(!empty($member)){
			$rid = parent::get_rank_id($member);
			$score = intval(parent::zScore($member));
			$UserModel = Master::getUser($member);
			$name = $UserModel->info['name'];
		}
		
		Master::back_data(0,$this->b_mol,"myRand",array(
			"rid"=>$rid,
			'score' => $score,
			'name' => $name,
		));
	}
	
}

