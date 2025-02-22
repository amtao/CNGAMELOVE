<?php
require_once "RedisBaseModel.php";
/*
 * 服装排行
 */
class Redis6010Model extends RedisBaseModel
{
	public $comment = "排行";
	public $act = 'actboss_6010';//活动标签
    public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
    public $out_num = 50;//常规输出范围 要获取几个
	public $b_mol = "actboss";//返回信息 所在模块
	public $b_ctrl = "rankList";//返回信息 所在控制器
	protected $_with_decimal_sort = false;//加小数排序
	
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
		uid => 衙门数
	*/
	);
	
	
	/**
	 * 获取宝物积分信息
	 * @param $member  衙门id
	 * @param $rid   排名id
	 */
	public function getMember($uid,$rid){

        //玩家信息
        $fuidInfo = Master::fuidInfo($uid);

        //玩家个人信息
        $this->_init = $fuidInfo;
        //玩家排名
        $this->_init['rid'] = $rid;
        $this->_init['num'] = intval(parent::zScore($uid));

        return $this->_init;
	}
	
	/*
	 * 返回我的排行信息
	 */
	public function back_data_my($uid){
		$rid = 100001;
		$score = 0;
		if(!empty($uid)){
			$rid = parent::get_rank_id($uid);
			$score = intval(parent::zScore($uid));
		}
		
		Master::back_data(0,$this->b_mol,"myDmg",array(
			"g2dmyrank"=>$rid,
			'g2dmydamage' => $score,
			'g2dallman' => $this->sSize(),
		));
	}
	
}

