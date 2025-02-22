<?php
require_once "RedisBaseModel.php";
/*
 * 葛二蛋伤害排行
 */
class Redis5Model extends RedisBaseModel
{
	public $comment = "葛二蛋伤害排行";
	public $act = 'wordboss';//活动标签
	
	public $b_mol = "wordboss";//返回信息 所在模块
	public $b_ctrl = "hurtRank";//返回信息 所在控制器
	
//	public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
//	public $out_num = 100;//常规输出范围 要获取几个
//	public $out_time = 60;//输出缓存过期时间
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(
	);
	
	//获取个人信息
	public function getMember($member,$rid){
		//玩家信息
		$fuidInfo = Master::fuidInfo($member);
		
		//玩家个人信息
		$this->_init = $fuidInfo;
		//玩家排名
		$this->_init['rid'] = $rid;
		$this->_init['num'] = $this->zScore($member);
		
		return $this->_init;
	}
	
	/*
	 * 返回额外信息
	 * 返回我的排名和 我的总伤害
	 */
	public function back_data_my($uid){
		//返回我的总伤害 //返回我的排名
		$data = array(
			'g2dmydamage' => $this->zScore($uid),
			'g2dmyrank' => $this->get_rank_id($uid),
			'g2dallman' => $this->sSize(),
		);
		Master::back_data(0,$this->b_mol,'ge2danMyDmg',$data);
	}
}


