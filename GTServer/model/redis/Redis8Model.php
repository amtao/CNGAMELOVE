<?php
require_once "RedisBaseModel.php";
/*
 * 点赞排行
 */
class Redis8Model extends RedisBaseModel
{
	public $comment = "点赞排行";
	public $act = 'admire';//活动标签
	
	public $b_mol = "ranking";//返回信息 所在模块
	public $b_ctrl = "admire";//返回信息 所在控制器
	
//	public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
//	public $out_num = 100;//常规输出范围 要获取几个
//	public $out_time = 60;//输出缓存过期时间
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(
		//uid =>admire
	);

    /**
     * 获取玩家点赞信息
     * @param $member 玩家uid
     * @param $rid
     * @return mixed
     */
	public function getMember($member,$rid){
		//玩家信息
		$fuidInfo = Master::fuidInfo($member);
		
		//玩家排名
		$fuidInfo['rid'] = $rid;
		
		//玩家点赞信息
		$fuidModel = Master::getUser($member);
		$fuidInfo['num'] = $fuidModel->info['admire'];
		return $fuidInfo;
	}

    /**
     * 返回排行信息
     * @param $uid
     */
	public function back_data_my($uid){
		$rid = parent::get_rank_id($uid);
		Master::back_data(0,$this->b_mol,"selfRid",array("admire"=>$rid));
	}

}
