<?php
require_once "RedisKuaCfgBaseModel.php";
/*
 * 跨服大理寺 - 各个服务器积分排行
 */
class Redis304Model extends RedisKuaCfgBaseModel
{
	public $comment = "跨服大理寺 - 服务器积分排行";
	public $act = 'huodong_300_all_sever';//活动标签
	public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
	public $out_num = 100;//常规输出范围 要获取几个
	public $b_mol = "kuayamen";//返回信息 所在模块
	public $b_ctrl = "severRank";//返回信息 所在控制器
	protected $_with_decimal_sort = true;//加小数排序
	protected $_server_type = 3;//1：合服，2：跨服，3：全服，4：指定跨服
	
	public function __construct($key){
	    $this->_with_decimal_denominator = time() -1508083200;
	    parent::__construct($key);
	}
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(
		/*
		'sid' => 0,  //服务器id  => 分数
	*/
	);
	
	//获取个人信息
	public function getMember($member,$rid){
		//玩家信息
// 		$fuidInfo = Master::fuidInfo($member);
		$fuidInfo['sid'] = $member;
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
		$UserModel = Master::getUser($uid);
		$data = array(
		    'myName' => $UserModel->info['name'],
			'myScore' => $this->zScore($uid),
			'myScorerank' => $this->get_rank_id($uid),
		);
		Master::back_data(0,$this->b_mol,'severScore',$data);
		
	}
	
}