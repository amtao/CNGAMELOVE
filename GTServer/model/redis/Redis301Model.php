<?php
require_once "RedisBaseModel.php";
/**
 * 跨服势力排行榜
 */
class Redis301Model extends RedisBaseModel
{
	public $comment = "跨服势力排行榜";
	public $act = 'huodong_301';//活动标签
    protected $_server_type = 3;//1：合服，2：跨服，3：全服
    protected $_with_decimal_sort = true;//加小数排序

    public $b_mol = "ranking";//返回信息 所在模块
    public $b_ctrl = "shiliKua";//返回信息 所在控制器
    protected $_b_my_column = "shiliKua";//列名
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
            'id' => 0,  //玩家UID
            'name' => 0,  //名字
            'level' => 0,  //官阶
            'vip' => 0,  //VIP
            'chenghao' => 0,  //称号
            'rid'	=> 0, //排名
            'num'	=> 0, //附加字段  -- 分值
        */
    );
    //获取个人信息
    public function getMember($member,$rid)
    {
        //玩家信息
        $fuidInfo = Master::fuidInfo($member);

        //玩家排名
        $fuidInfo['rid'] = $rid;

        //分值
        $fuidInfo['num'] = intval(parent::zScore($member));
        return $fuidInfo;
    }
    /*
     * 返回排行信息
     */
    public function back_data_my($uid){
        $rid = parent::get_rank_id($uid);
        Master::back_data(0,$this->b_mol,"selfRid",array($this->_b_my_column=>$rid));
    }
}