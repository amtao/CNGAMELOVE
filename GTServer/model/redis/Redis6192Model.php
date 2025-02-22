<?php
require_once "RedisBaseModel.php";
/*
 * 盛装出行排行榜
 */
class Redis6192Model extends RedisBaseModel
{
    public $comment = "御花园";
    public $act = 'flower_6192';//活动标签
    public $out_start = 1;//常规输出范围 从第几个开始 下标从1开始
    public $out_num = 50;//常规输出范围 要获取几个
    public $b_mol = "flower";//返回信息 所在模块
    public $b_ctrl = "rank";//返回信息 所在控制器
    protected $_with_decimal_sort = true;//加小数排序
    public function __construct($key = '')
    {
        parent::__construct($key);
        $this->_with_decimal_denominator = time() - 1506528000;
    }

    /*
     * 初始化结构体
     */
    public $_init = array(
        /*
        'id' => 0,  //玩家UID
        'name' => 0,  //名字
        'pkID' => 0,  //伙伴ID
        'rid'	=> 0, //排名
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

        Master::back_data(0,$this->b_mol,"myRank",array(
            "rid"=>$rid,
            'score' => $score,
            'name' => $name,
        ));
    }

    /*
	 * 随机一个对战玩家
	 */
    public function rand_f_uid($myuid){
        //获取我的名次
        $my_rank = $this->get_rank_id($myuid);

        $size = $this->sSize();

        if ($size == 1){
            return 0;
        }

        if ($size < 100){
            //获取下50名次
            $r_start = max(1,$my_rank - 50);

            //获取我的上下50名次
            //获取 uid => 分值列表
            $uis_scores = $this->azRange($r_start,$r_start+101);

            // 查找和删掉自己
            unset($uis_scores[array_search($myuid,$uis_scores)]);

            if(empty($uis_scores)){
                return 0;
            }
            return $uis_scores[array_rand($uis_scores,1)];
        }

        $rid = rand(1, $size);
        if ($rid == $my_rank){
            $rid = rand(1,$size);
        }

        return parent::get_member($rid);
    }

}