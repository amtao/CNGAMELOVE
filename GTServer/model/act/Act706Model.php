<?php

require_once "ActBaseModel.php";

/*
*赴约 
*/
class Act706Model extends ActBaseModel{
    public $atype = 706;//活动编号

    public $comment = "赴约--兑换商城";
    public $b_mol = "fuyue";//返回信息 所在模块
    public $b_ctrl = "exchange";//返回信息 所在控制器
    
    public $_init = array(
        'exchangeShop' => array(),
    );

    //兑换
    public function exchange($id,$num=1){
        $exchangecfg = Game::getcfg_info('dui_huan',$id);

        $exInfo = $this->info['exchangeShop'][$id];
        if($exchangecfg['set'] > 0 && !empty($exInfo) && $exInfo >= $exchangecfg['set']){
            Master::error(EXCHANGE_TIME_MAX);
        }
        $num = $num==0?1:$num;
        for($i = 1; $i <= $num; $i++){
            foreach($exchangecfg['cost'] as $v){
                Master::sub_item2($v);
            }
        }
        for($i = 1; $i <= $num; $i++){
            Master::add_item3($exchangecfg['rwd']);
        }
        if(empty($this->info['exchangeShop'][$id])){
            $this->info['exchangeShop'][$id] = 0;
        }
        $this->info['exchangeShop'][$id]++;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }
}