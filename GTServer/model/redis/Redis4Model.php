<?php
require_once "RedisBaseModel.php";
/*
 * 副本积分排行
 */
class Redis4Model extends RedisBaseModel
{
	public $comment = "副本积分排行";
	public $act = 'fbscore';//活动标签
	
	public $b_mol = "wordboss";//返回信息 所在模块
	public $b_ctrl = "scoreRank";//返回信息 所在控制器
	
//	public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
//	public $out_num = 100;//常规输出范围 要获取几个
//	public $out_time = 60;//输出缓存过期时间
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(
		/*
		'id' => 0,  //玩家UID
		'name' => 0,  //名字
		'level' => 0,  //官阶
		'vip' => 0,  //VIP
		'chenghao' => 0,  //称号
		'rid'	=> 0, //排名
		'num'	=> 0, //附加字段  -- 关卡
	*/
	);
	
	//获取个人信息
	public function getMember($member,$rid){
		//玩家信息
		$fuidInfo = Master::fuidInfo($member);
		//玩家排名
		$fuidInfo['rid'] = $rid;
		//获取分值
		$fuidInfo['num'] = $this->zScore($member);
		
		return $fuidInfo;
	}
	
	/*
	 * 返回我的积分信息
	 */
	public function back_data_my($uid){
		//返回我的总伤害 //返回我的排名
		$data = array(
			'myScore' => $this->zScore($uid),
			'myScorerank' => $this->get_rank_id($uid),
		);
		Master::back_data(0,$this->b_mol,'myScore',$data);
		
	}
	
}

/*
 * 通用联盟 势力 排行榜
 */
