<?php 
require_once "ActBaseModel.php";
/*
 * 购买直购礼包
 * 弹出礼包上限次数
 */

class Act751Model extends ActBaseModel{
    
    public $atype = 751;//活动编号

	public $comment = "弹出礼包上限次数";
	public $b_mol = "giftBag";//返回信息 所在模块
    public $b_ctrl = "maxPop";//返回信息 所在控制器
    
    public $_init = array(
        'popCount' => array(),//弹出信息
    );

    public function isMax($giftId){
        $maxNum = Game::getcfg_param("gift_pack_limit");
        if(empty($this->info['popCount'][$giftId])){
            $this->info['popCount'][$giftId] = 0;
        }
        if($this->info['popCount'][$giftId] >= $maxNum){
            return true;
        }
        $this->info['popCount'][$giftId]++;
        $this->save();
        return false;
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
