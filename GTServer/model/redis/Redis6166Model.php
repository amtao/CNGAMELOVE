<?php
require_once "RedisBaseModel.php";
/*
 * 活动亲密冲榜
 */
class Redis6166Model extends RedisBaseModel
{
    public $comment = "伙伴羁绊冲榜排行";
    public $act = 'huodong_6166';//活动标签

    public $b_mol = "cbhuodong";//返回信息 所在模块
    public $b_ctrl = "herojblist";//返回信息 所在控制器
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
            uid => 亲密数
        */
    );


    /**
     * 获取单个亲密的信息
     * @param $member  亲密id
     * @param $rid   排名id
     */
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
     * 返回我的排行信息
     */
    public function back_data_my($member){
        $name = '无';
        $rid = 100001;
        $score = 0;
        if(!empty($member)){
            $rid = parent::get_rank_id($member);
            $score = intval(parent::zScore($member));
            $UserModel = Master::getUser($member);
            $name = $UserModel->info['name'];
        }

        Master::back_data(0,$this->b_mol,"myHerojbRid",array(
            "rid"=>$rid,
            'score' => $score,
            'name' => $name,
        ));
    }

}

