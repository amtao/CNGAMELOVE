<?php
require_once "RedisBaseModel.php";
/*
 * 新年活动 - 总排行
 */
class Redis6187Model extends RedisBaseModel
{
    public $comment = "国力庆典 - 总排行";
    public $act = 'huodong_6187';//活动标签
    public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
    public $out_num = 100;//常规输出范围 要获取几个
    public $b_mol = "glqdhuodong";//返回信息 所在模块
    public $b_ctrl = "totalRankList";//返回信息 所在控制器
    protected $_with_decimal_sort = true;//加小数排序
    /*
     * 初始化结构体
     */
    public $_init = array(
        /*
            联盟id => 联盟总经验
        */
    );
    public function __construct($key = '')
    {
        parent::__construct($key);
        $this->_with_decimal_denominator = time() - 1506528000;
    }


    /**
     * 获取单个用户
     * @param $member
     * @param $rid
     * @return array
     */
    public function getMember($member,$rid){
        $fuserInfo = Master::fuidInfo($member);
        //获取公共基础信息
        $cinfo = array(
            'name' => $fuserInfo['name'],
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
            $fuserInfo = Master::fuidInfo($member);
            $name = $fuserInfo['name'];
        }
        Master::back_data(0,$this->b_mol,"mytotalRankRid",array(
            "myScorerank"=>$rid,
            'myScore' => $score,
            'myName' => $name,
        ));
    }

}

