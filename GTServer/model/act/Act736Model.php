<?php 
require_once "ActBaseModel.php";
/*
 * 伙伴邀约
 */

class Act736Model extends ActBaseModel{
    
    public $atype = 736;//活动编号

	public $comment = "伙伴邀约-钓鱼游戏";
	public $b_mol = "hero";//返回信息 所在模块
    public $b_ctrl = "buy";//返回信息 所在控制器
    
    public $_init = array(
        'useItem' => 0,//使用道具消耗
        'useCash' => 0,//使用元宝消耗
    );

    /**
     * 购买次数
     * 优先消耗道具
     * 道具次数消耗最大之后消耗元宝购买
     */
    public function buyCountByItem($isItem){
        $UserModel = Master::getUser($this->uid);
        $viplv = $UserModel->info['vip'];
        $vipCfg = Game::getcfg_info("vip",$viplv);
              
        if($isItem == 1){
            $useItemCount = $vipCfg['invite_item'];
            $this->buy($useItemCount,$isItem);
        }else{
            $useCashCount = $vipCfg['invite_cash'];
            $this->buy($useCashCount,$isItem);
        }

        $Act731Model = Master::getAct731($this->uid);
        $Act731Model->info['inviteCount']++;
        if($Act731Model->info['inviteCount'] >= 3){
            $Act731Model->info['lastRefreshTime'] = 0;
        }
        $Act731Model->save();
        $this->save();
    }

    public function buy($count,$isItem){
        if($isItem == 1){
            if($this->info['useItem'] >= $count){
                Master::error(INVITE_BUY_MAX);
            }
            $itemId = Game::getcfg_param("game_item");
            Master::sub_item($this->uid,KIND_ITEM,$itemId,1);
            $this->info['useItem']++;
        }else{
            if($this->info['useCash'] >= $count){
                Master::error(INVITE_BUY_MAX);
            }
            $cost = ($this->info['useCash']+1)*100;
            Master::sub_item($this->uid,KIND_ITEM,1,$cost);
            $this->info['useCash']++;
        }
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
