<?php
require_once "ActBaseModel.php";
/*
 * 活动297 情人节活动-获赠记录
 */
class Act151Model extends ActBaseModel
{
    public $atype = 151;//活动编号
    public $comment = "情人节活动-获赠记录";
    public $b_mol = "lovehuodong";//返回信息 所在模块
    public $b_ctrl = "loveLog";//子类配置
    public $hd_id = 'huodong_297';//活动配置文件关键字
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
     * 累计数量
     * 领奖档次
     */
    public $_init =  array(
        /*
         * name 名字
         * num  数量
         * time 时间
         */
    );

    /**
     * 添加日志
     * @param $uid 玩家id
     * @param $num 获赠数量
     */
    public function add_log($uid,$num){

        //玩家个人信息
        $UserModel = Master::getUser($uid);
        $this->info[] = array(
            'uid' => $uid,
            'name' => Game::filter_char($UserModel->info['name']),
            'num' => $num,
            'time' => Game::get_now(),
        );
        $this->save();
    }

    /*
     * 返回活动信息
     */
    public function back_data(){


    }

    /*
     * 返回活动信息
     */
    public function back_data_a(){
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
    }

}
