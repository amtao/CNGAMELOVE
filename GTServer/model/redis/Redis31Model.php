<?php
require_once "RedisBaseModel.php";
/*
 * 帮会红包-排行榜
 */
class Redis31Model extends RedisBaseModel
{
	public $comment = "帮会红包-排行榜";
	public $act = 'huodong_295';//活动标签
	
	public $b_mol = "hbhuodong";//返回信息 所在模块
	public $b_ctrl = "rankList";//返回信息 所在控制器


	public function __construct($key = '')
	{
		parent::__construct($key);
		$this->_with_decimal_denominator = time() - 1506528000;
	}

	//获取个人信息
	public function getMember($member,$rid){
		//玩家信息
		$fuidInfo = Master::fuidInfo($member);
		//玩家排名
		$fuidInfo['rid'] = $rid;
		//获取分值
		$fuidInfo['num'] = intval($this->zScore($member));

		return $fuidInfo;
	}

	/*
	 * 返回我的积分信息
	 */
	public function back_data_my($uid){
		//返回我的总伤害 //返回我的排名
		$fuserInfo = Master::fuidInfo($uid);
		$data = array(
			'myName' => $fuserInfo['name'],
			'myScore' => intval($this->zScore($uid)),
			'myScorerank' => $this->get_rank_id($uid),
		);
		Master::back_data(0,$this->b_mol,'myScore',$data);
	}
}
