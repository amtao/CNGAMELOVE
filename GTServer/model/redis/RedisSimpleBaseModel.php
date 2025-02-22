<?php
require_once "RedisBaseModel.php";
class RedisSimpleBaseModel extends RedisBaseModel
{
    public $b_mol = "cbhuodong";//返回信息 所在模块
    public $b_ctrl = "list";//返回信息 所在控制器
    protected $_with_decimal_sort = true;//加小数排序
    public function __construct($key = '')
    {
        parent::__construct($key);
        $this->_with_decimal_denominator = time() - 1505232000;
    }
    /**
     * 获取单个的信息
     * @param mixed $member uid
     * @param mixed $rid   id
     */
    public function getMember($member,$rid){
        $UserModel = Master::getUser($member);
        //获取公共基础信息
        return array(
            'name' => $UserModel->info['name'],
            'rid' => $rid,
            'score' => intval(parent::zScore($member)),
        );
    }
    public function back_xs_rank($type, $member)
    {
        $name = '无';
        $rid = 100001;
        $score = 0;
        if(!empty($member)){
            $rid = parent::get_rank_id($member);
            $score = intval(parent::zScore($member));
            $UserModel = Master::getUser($member);
            $name = $UserModel->info['name'];
        }
        return array(
            'type'=>$type,
            'myName'=>$name,
            'myRid'=>$rid,
            'myNum'=>$score,
            'xsRank'=>$this->out_redis(),
        );
    }
}




