<?php
require_once "ActBaseModel.php";
/*
 * 活动122 感恩节活动-累天充值
 */
class Act127Model extends ActBaseModel
{
    public $atype = 127;//活动编号
    public $comment = "感恩节活动-累天充值";
    public $b_mol = "ThanksDay";//返回信息 所在模块
    public $b_ctrl = "leiji";//子类配置
    public $hd_id = 'huodong_284';//活动配置文件关键字
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
            parent::__construct($uid,$this->hd_cfg['info']['id'].Game::get_today_id());//执行基类的构造函数
        }
    }
    /*
     * 初始化结构体
     * 累计数量
     * 领奖档次
     */
    public $_init =  array(
        'total' => 0,//充值总金额
        'rwd' => 0,  //已领取的档次
    );


    /**
     * 充值记录
     * @param $num
     */
    public function add_recode($money){
        //判断活动是否开启
        $state = self::get_state();
        if( $state == 1 || $state == 2){
            $this->info['total'] += $money;
            $this->save();
        }
    }
    /*
     * 领取
     * */
    public function getRwd($id) {
        //判断领取的档次是否正确
        if($id - $this->info['rwd'] != 1){
            Master::error(ACT_HD_GIVE_ATTRIBUTE_ERROR);
        }
        //获取充值奖励信息
        if(empty($this->hd_cfg['recharge'][$id-1])){
            Master::error(ACT_HD_GIVE_MAX);
        }
        $this->info['rwd'] +=1;

        if(!empty($this->hd_cfg['recharge'][$id-1]['items'])){
            foreach ($this->hd_cfg['recharge'][$id-1]['items'] as $ite){
                Master::add_item($this->uid, empty($ite['kind']) ? 1 : $ite['kind'], $ite['id'],$ite['count']);
            }
        }
        $this->save();
    }

    //构造输出函数
    public function make_out(){
        $outf = array(
            'total' => empty($this->info['total']) ? 0 : $this->info['total'],
            'rwd' => empty($this->info['rwd']) ? 0 : $this->info['rwd']
        );
        $this->outf = $outf;
    }

    /**
     * 活动活动状态
     * 返回:
     * 0: 活动未开启
     * 1: 活动中
     * 2: 活动结束,展示中
     */
    public function get_state(){
        $state = 0;  //活动未开启
        if(!empty($this->hd_cfg) ){
            if(Game::dis_over($this->hd_cfg['info']['showTime'])){
                $state = 2;  //活动结束,展示中
            }
            if(Game::dis_over($this->hd_cfg['info']['eTime'])){
                $state = 1;  //活动中
            }
        }
        return $state;
    }
}
