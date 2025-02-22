<?php
require_once "RedisBaseModel.php";
/*
 * 子嗣势力涨幅冲榜排行
 */
class Redis311Model extends RedisBaseModel
{
	public $comment = "子嗣势力涨幅冲榜排行";
	public $act = 'huodong_311';//活动标签
	
	public $b_mol = "cbhuodong";//返回信息 所在模块
	public $b_ctrl = "zsshililist";//返回信息 所在控制器
	protected $_with_decimal_sort = true;//加小数排序
	
    public function __construct($key = '')
    {
        parent::__construct($key);
        $this->_with_decimal_denominator = time() - 1505232000;
    }
	/*
	 * 初始化结构体
	 */
	public $_init = array(
	/*
		uid => 子嗣总势力
	*/
	);
	
	
	/**
	 * 获取单个联盟的信息
	 * @param $member  联盟id
	 * @param $rid   排名id
	 */
	public function getMember($member,$rid){
		
		$UserInfo = Master::fuidInfo($member);
		//获取公共基础信息
		$cinfo = array(
			'name' => $UserInfo['name'],
			'rid' => $rid,
			'score' => intval(parent::zScore($member)),
		);
		return $cinfo;
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
			$UserInfo = Master::fuidInfo($member);
			$name = $UserInfo['name'];
		}
		
		
		Master::back_data(0,$this->b_mol,"myzsShiliRid",array(
			"rid"=>$rid,
			'score' => $score,
			'name' => $name,
		));
	}
	
}

