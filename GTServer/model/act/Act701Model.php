<?php 
require_once "ActBaseModel.php";
/*
 *0元购礼包
 */

class Act701Model extends ActBaseModel{
    
    public $atype = 701;//活动编号

	public $comment = "0元购礼包";
	public $b_mol = "fuli";//返回信息 所在模块
    public $b_ctrl = "zeroGift";//返回信息 所在控制器
    
    public $_init = array(
        'info' => array(),
    );

    //购买0元购礼包Id
    public function buyGift($id){
        $giftpackCfg = Game::getcfg_info('giftpack',$id);
        if($giftpackCfg['type'] != 1){
            return;
        }
        if(isset($this->info['info'][$id]) && $this->info['info'][$id]['buyTime'] > 0){
            Master::error(ACT701_ONLY_BUY_ONCE);
        }
        Master::sub_item($this->uid,KIND_ITEM,1,$giftpackCfg['set']);
        Master::add_item3($giftpackCfg['rwd']);
        $this->info['info'][$id]['buyTime'] = Game::get_now();
        $this->info['info'][$id]['endTime'] = Game::day_0() + $giftpackCfg['day'] * 86400;
        $this->info['info'][$id]['pickTime'] = 0;
        
        $this->save();
    }

    //领取最终返利奖励
    public function pickRebate($id){
        $giftpackCfg = Game::getcfg_info('giftpack',$id);
        if($giftpackCfg['type'] != 1){
            return;
        }
        $nowTime = Game::get_now();
        if(!isset($this->info['info'][$id]) || $this->info['info'][$id]['buyTime'] <= 0 || $nowTime < $this->info['info'][$id]['endTime']){
            Master::error(ACT701_NOT_REACH_PICK);
        }
        if($this->info['info'][$id]['pickTime'] > 0){
            Master::error(REWARD_IS_GET);
        }
        Master::add_item($this->uid,KIND_ITEM,1,$giftpackCfg['set']);
        $this->info['info'][$id]['pickTime'] = Game::get_now();
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
