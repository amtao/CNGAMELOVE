<?php

require_once "ActBaseModel.php";
/**
 * 办差-领取奖励信息
 */
class Act715Model extends ActBaseModel{
    public $atype = 715;

    public $comment = "办差-购买次数";
    public $b_mol = "office";//返回信息 所在模块
    public $b_ctrl = "buy";//返回信息 所在控制器

    public $_init = array(
        'buyCount' => 0,
        'buyCountLing' => 0,
    );

    //购买次数
    public function buyCount(){
        if(empty($this->info['buyCount'])){
            $this->info['buyCount'] = 0;
        }
        $UserModel = Master::getUser($this->uid);
        $vip = $UserModel->info['vip'];
        $vipCfg = Game::getcfg_info('vip',$vip);
        if($this->info['buyCount'] >= $vipCfg['banchai']){
            Master::error(OFFICE_BUY_COUNT_MAX);
        }
        $bcCostCfg = Game::getcfg_info('bc_cost',$this->info['buyCount']+1);
        foreach ($bcCostCfg['cost'] as $v) {
            # code...
            Master::sub_item2($v);
        }
        $this->info['buyCount']++;
        $Act716Model = Master::getAct716($this->uid);
        $Act716Model->info['startCount']++;
        $Act716Model->save();
        $this->save();
    }

    //购买次数
    public function buyCountByLing(){
        if(empty($this->info['buyCountLing'])){
            $this->info['buyCountLing'] = 0;
        }
        $UserModel = Master::getUser($this->uid);
        $vip = $UserModel->info['vip'];
        $vipCfg = Game::getcfg_info('vip',$vip);
        if($this->info['buyCountLing'] >= $vipCfg['banchailing']){
            Master::error(OFFICE_BUY_COUNT_MAX);
        }
        Master::sub_item($this->uid,KIND_ITEM,122,1);
        $this->info['buyCountLing']++;
        $Act716Model = Master::getAct716($this->uid);
        $Act716Model->info['startCount']++;
        $Act716Model->save();
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }
}