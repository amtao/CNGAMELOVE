<?php
require_once "RedisBaseModel.php";
/*
 * 势力排行
 */
class Redis1Model extends RedisBaseModel
{
	public $comment = "势力排行";
	public $act = 'shili';//活动标签
	
	public $b_mol = "ranking";//返回信息 所在模块
	public $b_ctrl = "shili";//返回信息 所在控制器
	protected $_with_decimal_sort = true;//加小数排序
	
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
		'num'	=> 0, //附加字段  -- 势力值
	*/
	);
	
	//获取个人信息
	public function getMember($member,$rid){
		//玩家信息
		$fuidInfo = Master::fuidInfo($member);
		
		//玩家排名
		$fuidInfo['rid'] = $rid;
		//势力
		$fuidInfo['num'] = intval(parent::zScore($member));
		
		return $fuidInfo;
	}
	
	/*
	 * 返回排行信息
	 */
	public function back_data_my($uid){
		$rid = parent::get_rank_id($uid);
		Master::back_data(0,$this->b_mol,"selfRid",array("shili"=>$rid));
	}


	
}

/*
 * 通用联盟 势力 排行榜
 */
