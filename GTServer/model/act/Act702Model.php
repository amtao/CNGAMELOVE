<?php

require_once "ActBaseModel.php";

/*
*钱庄--成长基金 
*/

class Act702Model extends ActBaseModel{
    
    public $atype = 702;//活动编号

	public $comment = "钱庄";
	public $b_mol = "bank";//返回信息 所在模块
    public $b_ctrl = "bankInfo";//返回信息 所在控制器
    
    public $_init = array(
    );

    public function buy($rmb){
        $UserModel = Master::getUser($this->uid);
		$channel = $UserModel->info['channel_id'];
		$platform = $UserModel->info['platform'];
		Common::loadModel('OrderModel');
		$list = OrderModel::recharge_list($platform,$channel);
        if($list[$rmb]['type'] != 6){
            return false;
        }
        $this->info['buyTime'] = Game::get_now();
		
		$this->save();
		return $list[$rmb]['type'];
    }

    public function pickAward($id){
        $giftpackCfg = Game::getcfg_info('giftpack',$id);
        if($giftpackCfg['type'] != 3){
            Master::error(ACT700_NOT_SCORE_AWARD);
        }
        $fUserModel = Master::getUser($this->uid);
        if($fUserModel->info['level'] < $giftpackCfg['set']){
            Master::error(ACT702_NOT_REACH_CONDITION);
        }
        if($this->info['buyTime'] <= 0){
            Master::error(ACT702_NOT_BUY);
        }
        if(!empty($this->info['pickInfo']) && $this->info['pickInfo'][$id] == 1){
            Master::error(REWARD_IS_GET);
        }
        Master::add_item3($giftpackCfg['rwd']);
        $this->info['pickInfo'][$id] = 1;
        $this->save();
    }


    public function make_out(){
        $this->outf = $this->info;
    }
}