<?php
require_once "ActHDBaseModel.php";

/*
 * 活动6169
 */
class Act6169Model extends ActHDBaseModel
{
    public $atype = 6169;//活动编号
    public $comment = "命盘活动";
    public $b_mol = "dzphuodong";//返回信息 所在模块
    public $b_ctrl = "cfg";//子类配置
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_6169';//活动配置文件关键字

    /*
     * 初始化结构体
     */
    public $_init =  array(


    );


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
     * 构造输出结构体
     */
    public function make_out(){

        //构造输出
        $this->outf = array();
        if( parent::get_state() == 0 ){
            Master::error(ACTHD_ACTIVITY_UNOPEN.__LINE__);
        }

        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        unset($hd_cfg['needTen']);
        $rwd = array();
        foreach($hd_cfg['list'] as $k=>$v){
            $rwd[$k] = array('dc'=>$v['dc'],'id'=>$v['items']['id'],'count'=>$v['items']['count'],'kind'=>$v['items']['kind']) ;
        }
        $hd_cfg['list'] = $rwd;
        $this->outf = $hd_cfg;  //活动期间花费多少元宝

    }

    /**
     * 摇奖
     * $num 次数 1 或 10次
     */
    public function yao($num){

        if( parent::get_state() != 1 ){
            Master::error(ACTHD_OVERDUE);
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);


        //扣道具
        $need_itemd= $num == 1? $hd_cfg['need']:$hd_cfg['needTen'];
        Master::sub_item($this->uid,KIND_ITEM,$need_itemd['id'],$need_itemd['count']);
        //奖励
        $list = array();
        $rwd = array();
        foreach($hd_cfg['list'] as $k=>$v){
            $list[$v['dc']] = $v;
            $rwd[$k] = array('dc'=>$v['dc'],'id'=>$v['items']['id'],'count'=>$v['items']['count'],'kind'=>$v['items']['kind']) ;
        }
        $hd_cfg['list'] = $rwd;
        //摇奖循环
        for($i = 1 ; $i<= $num ; $i ++){
            //砸蛋随机奖励
            $rid =  Game::get_rand_key(10000,$list,'prob_10000');
            $hd_cfg['prize'][] = array('dc'=>$rid);
            $zpItem = $list[$rid]['items'];
            Master::add_item2($zpItem);
        }
        unset($hd_cfg['needTen']);
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$hd_cfg);


    }


    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取
        //活动消耗道具
        $hd_need = $this->hd_cfg['need']['id'];
        $ItemModel = Master::getItem($this->uid);
        if (isset($ItemModel->info[$hd_need]) && $ItemModel->info[$hd_need]['count'] > 0){
            $news = 1;
        }
        return $news;
    }


}




