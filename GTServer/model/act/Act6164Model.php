<?php
require_once "ActBaseModel.php";
/*
 * 感恩节活动 - 积分
 */
class Act6164Model extends ActBaseModel
{
    public $atype = 6164;//活动编号

    public $comment = "皇子应援活动 - 贡献";
    public $b_mol = "yyhuodong";//返回信息 所在模块
    public $b_ctrl = "contribution";//返回信息 所在控制器
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
         * 'id'=>0,活动id
         * 'contribution' =>0,//贡献分数

         * */
    );

    /*
     * 添加积分
     *
     * */
    public function add_score($contribution,$pkID)
    {
        $hd_cfg = $this->hd_cfg;
        $hid = $hd_cfg['info']['id'];

        if(empty($this->info['id']) || $this->info['id'] != $hid){
            $this->info['id'] = $hid;
            $this->info['contribution'] = array();
        }
        if ( empty($this->info['contribution'][$pkID] ) ){
            $this->info['contribution'][$pkID] = 0;
        }
        $this->info['contribution'][$pkID] += $contribution;
        $this->save();
    }


    /*
     * 构造输出结构体
     */
    public function make_out(){
        $outof=array();

        $hd_cfg = $this->hd_cfg;
        $hid = $hd_cfg['info']['id'];

        if(empty($this->info['id']) || $this->info['id'] != $hid || empty($this->info['contribution'])){
            $outof = array('contribution'=>0);
        }else{
            $contribution = array_sum($this->info['contribution']);
            $outof = array('contribution'=>$contribution);
        }


        //默认输出直接等于内部存储数据
        $this->outf = $outof;
    }
}
