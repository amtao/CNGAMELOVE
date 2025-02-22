<?php
require_once "ActBaseModel.php";
/*
 * 感恩节活动 - 积分
 */
class Act6163Model extends ActBaseModel
{
    public $atype = 6163;//活动编号

    public $comment = "皇子应援 - 积分";
    public $b_mol = "yyhuodong";//返回信息 所在模块
    public $b_ctrl = "score";//返回信息 所在控制器
    public $hd_id = 'huodong_6136';//活动配置文件关键字
    public $hd_cfg;

    /**
     * @param unknown_type $uid   玩家id
     * @param unknown_type $id    活动id
     */
    public function __construct($uid)
    {
        $this->uid = intval($uid);
        //获取活动配置
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        if(!empty($this->hd_cfg['info']['id'])){
            parent::__construct($uid,$this->hd_cfg['info']['id']);//执行基类的构造函数
        }
    }

    /*
     * 初始化结构体
     */
    public $_init =  array(
        /*
         * 'score' =>0,//活动积分
         *
         * */
    );

    /*
     * 添加积分
     *
     * */
    public function add_score($score)

    {
        if (empty($this->info['score'])){
            $this->info['score']=0;
        }
        $this->info['score'] += $score;
        $this->save();
    }

    /*
     * 消耗积分
     *
     * */
    public function sub_score($score,$num)

    {
        $score *= $num;
        if (empty($this->info['score']) || $score > $this->info['score']){
            Master::error(POINT_NOT_ENOUGH);
        }
        $this->info['score'] -= $score;
        $this->save();
    }


    /*
     * 构造输出结构体
     */
    public function make_out(){
        $outof = array();
        if(empty($this->info['score'])){

            $this->info['score'] = 0;
        }

        //默认输出直接等于内部存储数据
        $this->outf = $this->info;
    }
}
