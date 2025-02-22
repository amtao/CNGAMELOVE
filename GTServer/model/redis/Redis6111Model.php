<?php
require_once "RedisBaseModel.php";
/*
 * 活动亲密冲榜
 */
class Redis6111Model extends RedisBaseModel
{
	public $comment = "亲密冲榜排行";
	public $act = 'trea_6111';//活动标签
	
	public $b_mol = "treasure";//返回信息 所在模块
	public $b_ctrl = "tidyList";//返回信息 所在控制器
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
        $name = '无';
        $rid = 100001;
        $score = 0;
        if(!empty($uid)){
            $rid = parent::get_rank_id($uid);
            $score = intval(parent::zScore($uid));
            $UserModel = Master::getUser($uid);
            $name = $UserModel->info['name'];
        }

        Master::back_data(0,$this->b_mol,"myTidyRank",array(
            "rid"=>$rid,
            'score' => $score,
            'name' => $name,
        ));
    }
}

