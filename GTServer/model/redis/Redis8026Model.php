<?php
require_once "RedisBaseModel.php";
/*
 * 海滩夺宝 - 积分排行
 */
class Redis8026Model extends RedisBaseModel
{
    public $comment = "海滩夺宝 - 积分排行";
    public $act = 'huodong_8026';//活动标签
    public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
    public $out_num = 100;//常规输出范围 要获取几个
    public $b_mol = "beachloot";//返回信息 所在模块
    public $b_ctrl = "qxRank";//返回信息 所在控制器
    protected $_with_decimal_sort = true;//加小数排序
    public function __construct($key = '')
    {
        parent::__construct($key);
        $this->_with_decimal_denominator = time() - 1506528000;
    }

    //获取个人信息
    public function getMember($member,$rid){

        $UserModel = Master::getUser($member);
        //获取公共基础信息
        $cinfo = array(
            'name' => $UserModel->info['name'],
            'rid' => $rid,
            'score' => intval(parent::zScore($member)),
        );
        return $cinfo;
    }

    /*
     * 返回我的积分信息
     */
    public function back_data_my($uid){
        //返回我的总伤害 //返回我的排名
        $UserModel = Master::getUser($uid);
        $data = array(
            'name' => $UserModel->info['name'],
            'score' => intval($this->zScore($uid)),
            'rid' => $this->get_rank_id($uid),
        );
        Master::back_data(0,$this->b_mol,'myQxRid',$data);

    }

}