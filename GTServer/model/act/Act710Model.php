<?php

require_once "ActBaseModel.php";
/**
 * 行商--购买信息
 */
class Act710Model extends ActBaseModel{
    public $atype = 710;

    public $comment = "行商-进入次数";
    public $b_mol = "business";//返回信息 所在模块
    public $b_ctrl = "startinfo";//返回信息 所在控制器

    public $_init = array(
        // 'freeBusinessCount' => 0,   //免费消耗次数
        // 'payBusinessCount' => 0,    //使用日常令次数
        'consumeBusinessCount' => 0,   //消耗次数
        'buyBusinessCount' => 0,    //购买次数
    );

    public function buyCount(){
        $UserModel = Master::getUser($this->uid);
        $vipCfg = Game::getcfg_info('vip',$UserModel->info['vip']);
        //购买次数达到最大
        if($this->info['buyBusinessCount'] >= $vipCfg['xingshangtime']){
            Master::error(BUY_COUNT_MAX);
        }
        Master::sub_item($this->uid,KIND_ITEM,121,1);
        $this->info['buyBusinessCount'] ++;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }
}
