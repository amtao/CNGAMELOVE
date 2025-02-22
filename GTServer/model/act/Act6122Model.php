<?php
require_once "ActHDBaseModel.php";

/*
 * 活动6122
 */
class Act6122Model extends ActHDBaseModel
{
    public $atype = 6122;//活动编号
    public $comment = "兑换商城";
    public $b_mol = "duihuanshop";//返回信息 所在模块
    public $b_ctrl = "shop";//子类配置
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_6122';//活动配置文件关键字


    /*
     * 初始化结构体
     */
    public $_init =  array(
        'duihuan' => array(),
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

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取
        $info = $this->info;
        $ItemModel = Master::getItem($this->uid);
        foreach($this->hd_cfg['rwd'] as $rwd){
            //验证道具是否充足
            $id = $rwd['id'];
            $need = $rwd['items'][0];
            if ($info['duihuan'][$id] < $rwd['count'] && $ItemModel->sub_item($need['id'],$rwd['count'],true)){
                return 1;
            }
        }
        return $news;
    }

    /**
     * 获得奖励
     * $id
     */
    public function get_rwd($id = 0){
        if( parent::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        $buy_count = floor($id / 10000);
        if ($buy_count <= 0)return;
        $id = $id % 10000;
        if ($buy_count == 0)Master::error();
        foreach($this->hd_cfg['rwd'] as $rwd){
            if ($rwd['id'] == $id){
                $c = empty($this->info['duihuan'][$id])?0:$this->info['duihuan'][$id];
                if ($c + $buy_count > $rwd['count'] && $rwd['count'] != 0){
                    Master::error();
                }
                $item = $rwd['items'][0];
                $ItemModel = Master::getItem($this->uid);
                $ItemModel->sub_item($item['id'], $item['count'] * $buy_count);

                $this->info['duihuan'][$id] = $c + $buy_count;
                $item = $rwd['items'][1];
                $item['count'] = $item['count'] * $buy_count;
                Master::add_item2($item);
                $this->save();
                break;
            }
        }
        $this->back_data_hd();
    }

    /*
     * 构造输出结构体
     */
    public function make_out(){
        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);

        $info = $this->info;
        $rwds = array();
        foreach($hd_cfg['rwd'] as $rwd){
            $rwd['buy'] = empty($info['duihuan'][$rwd['id']])?0:$info['duihuan'][$rwd['id']];
            $rwds[] = $rwd;
        }
        $hd_cfg['rwd'] = $rwds;

        $hd_cfg['info']['news'] = $this->get_news();
        $this->outf = $hd_cfg;
    }

}
