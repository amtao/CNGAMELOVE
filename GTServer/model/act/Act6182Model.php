<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6182
 */
class Act6182Model extends ActHDBaseModel
{
    public $atype = 6182;//活动编号
    public $comment = "身份大礼";
    public $b_mol = "sfhuodong";//返回信息 所在模块
    public $b_ctrl = "sfGift";//子类配置
    public $hd_id = 'huodong_6182';//活动配置文件关键字

    /*
 * 初始化结构体
 * 累计数量
 * 领奖档次
 */
    public $_init =  array(
        'free'=>array(),
        'charge'=>array(),
    );

    /**
     * 获得奖励
     * @param int $id
     */
    public function get_rwd($id = 0){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        //奖励信息
        $UserModel = Master::getUser($this->uid);
        $lv = $UserModel->info['level'];
        $free = Game::get_key2id($this->hd_cfg['free'],'lv');
        $freeInfo = $free[$id];
        if (empty($freeInfo['lv'])){
            Master::error(ACTHD_NO_REWARD);
        }
        if (!empty($this->info['free'][$id]) || $lv < $freeInfo['lv']){
            Master::error(ACTHD_NO_RECEIVE);
        }
        //领取奖励
        Master::add_item3($freeInfo['items']);
        $this->info['free'][$id] = 1;
        $this->save();
    }

    /**
     * 付费获得奖励
     * @param int $id
     */
    public function get_rwd_charge($id = 0,$num = 1){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //奖励信息
        $UserModel = Master::getUser($this->uid);
        $lv = $UserModel->info['level'];
        $charge = Game::get_key2id($this->hd_cfg['charge'],'lv');
        $chargeInfo = $charge[$id];
        if (empty($this->info['charge'][$id])){
            $this->info['charge'][$id] = 0;
        }
        if (empty($chargeInfo['lv'])){
            Master::error(ACTHD_NO_REWARD);
        }
        if ($lv < $chargeInfo['lv'] || $this->info['charge'][$id]+$num > $chargeInfo['limit']){
            Master::error(ACTHD_NO_RECEIVE);
        }
        //扣除元宝
        Master::sub_item($this->uid,KIND_ITEM,1,$chargeInfo['need'] * $num);
        //领取奖励
        for ($i=0;$i<$num;$i++){
            Master::add_item3($chargeInfo['items']);
        }

        $this->info['charge'][$id] += $num;
        $this->save();
    }

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取
        if( self::get_state() == 0){
            $news = 0;
        }else{

            $free = Game::get_key2id($this->hd_cfg['free'],'lv');
            $UserModel = Master::getUser($this->uid);
            $lv = $UserModel->info['level'];
            foreach ($free as $k => $v){
                if ($lv >= $k && empty($this->info['free'][$k])){
                    $news = 1;
                }
            }
        }
        return $news;
    }

    /*
     * 构造输出结构体
     */
    public function make_out(){
        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
            Master::error(ACTHD_ACTIVITY_UNOPEN.__LINE__);
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        unset($hd_cfg['free']);
        unset($hd_cfg['charge']);
        $hd_cfg['free'] = Game::get_key2id($this->hd_cfg['free'],'lv');
        $hd_cfg['charge'] = Game::get_key2id($this->hd_cfg['charge'],'lv');
        foreach ($hd_cfg['free'] as $k=>$v){
            $hd_cfg['free'][$k]['isget'] = empty($this->info['free'][$v['lv']])?0:1;
        }
        foreach ($hd_cfg['charge'] as $x=>$z){
            $hd_cfg['charge'][$x]['limit'] = empty($this->info['charge'][$z['lv']])?$hd_cfg['charge'][$x]['limit']:$hd_cfg['charge'][$x]['limit']-$this->info['charge'][$z['lv']];
        }

        $this->outf = $hd_cfg;  //活动期间花费多少元宝
    }








}

