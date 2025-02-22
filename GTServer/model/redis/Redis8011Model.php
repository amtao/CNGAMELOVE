<?php
require_once "RedisBaseModel.php";
/*
 * 贵人令 - 等级排行
 */
class Redis8011Model extends RedisBaseModel
{
    public $comment = "贵人令 - 等级排行";
    public $act = 'huodong_8011';//活动标签
    public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
    public $out_num = 100;//常规输出范围 要获取几个
    public $b_mol = "guirenling";//返回信息 所在模块
    public $b_ctrl = "qxRank";//返回信息 所在控制器
    protected $_with_decimal_sort = true;//加小数排序
    public function __construct($key = '')
    {
        parent::__construct($key);
        $this->_with_decimal_denominator = time() - 1506528000;
        $this->_with_decimal_number = 1000000000;
    }

    //获取个人信息
    public function getMember($member,$rid){

        //玩家信息
        $fuidInfo = Master::fuidInfo($member);
        
        //玩家排名
        $fuidInfo['rid'] = $rid;
        //势力
        $fuidInfo['score'] = intval(parent::zScore($member));
        
        return $fuidInfo;
    }

    /*
     * 返回我的积分信息
     */
    public function back_data_my($uid){

        //玩家信息
        $fuidInfo = Master::fuidInfo($uid);
        //玩家排名
        $fuidInfo['rid'] = $this->get_rank_id($uid);
        //势力
        $fuidInfo['score'] = intval(parent::zScore($uid));

        Master::back_data(0,$this->b_mol,'myQxRid',$fuidInfo);

    }

}