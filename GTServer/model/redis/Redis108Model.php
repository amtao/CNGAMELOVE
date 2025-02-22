<?php
require_once "RedisBaseModel.php";
/*
 * 狩猎 - 积分排行
 */
class Redis108Model extends RedisBaseModel
{
	public $comment = "狩猎 - 积分排行";
	public $act = 'hunt';//活动标签
	public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
	public $out_num = 100;//常规输出范围 要获取几个
	public $b_mol = "hunt";//返回信息 所在模块
	public $b_ctrl = "scoreRank";//返回信息 所在控制器
	
	
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
	 * 返回第一名信息
	 * */
	public function back_data_first(){
	    $fuid = $this->get_member(1);
	    //返回我的总伤害 //返回我的排名
		$data = array(
		    'user' => Master::fuidInfo($fuid),
			'Score' => $this->zScore($fuid),
			'Scorerank' => 1,
		);
		Master::back_data(0,$this->b_mol,'firstScore',$data);
	}
	
	/*
	 * 返回我的积分信息
	 */
	public function back_data_my($uid){
		//返回我的总伤害 //返回我的排名
		$UserModel = Master::getUser($uid);
		$data = array(
		    'myName' => $UserModel->info['name'],
			'myScore' => $this->zScore($uid),
			'myScorerank' => $this->get_rank_id($uid),
		);
		Master::back_data(0,$this->b_mol,'myScore',$data);
		
	}
	
}