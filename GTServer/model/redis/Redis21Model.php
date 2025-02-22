<?php
require_once "RedisBaseModel.php";
/*
 * 酒楼-消息-来宾统计
 */
class Redis21Model extends RedisBaseModel
{
	public $comment = "酒楼-消息-来宾统计";
	public $act = 'yhLaiBin';//活动标签
	
	public $b_mol = "boite";//返回信息 所在模块
	public $b_ctrl = "lbList";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(
	/*
		uid => 历史赴宴分数
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
	
}

/*
 * 通用联盟 势力 排行榜
 */
