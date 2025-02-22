<?php
require_once "ActBaseModel.php";
/*
 * 公会宴会-挂机奖励
 */
class Act770Model extends ActBaseModel
{
    public $atype = 770;//活动编号

    public $comment = "公会宴会-挂机奖励";
    public $b_mol = "club";//返回信息 所在模块
    public $b_ctrl = "party";//返回信息 所在控制器

    public $_init = array(
        'hookStart' => 0,//开始挂机时间
        'isHookPick' => 0,//是否领取奖励
    );

    //开始挂机
    public function startHook(){
        if(!empty($this->info['hookStart'])){
            Master::error(CLUB_PARTY_IS_HOOKING);
        }
        if($this->info['isHookPick'] == 1){
            Master::error(HAS_PICK_AWARD);
        }
        $this->info['hookStart'] = Game::get_now();
        $this->save();
    }

    //领取挂机奖励
    public function pickAward($cid){
        if($this->info['isHookPick'] == 1){
            Master::error(HAS_PICK_AWARD);
        }
        $totalTime = Game::getcfg_param('club_partyOnhookTime');
        $intvel = Game::getcfg_param('club_partyOneTime');
        $now = Game::get_now();
        if($this->info['hookStart'] == 0 || $this->info['hookStart']+$totalTime > $now){
            Master::error(CLUB_PARTY_IS_HOOKING);
        }

        $pickCount = ceil($totalTime/$intvel);
        $Sev17Model = Master::getSev17($cid);
        $partyCfg = Game::getcfg_info('party',$Sev17Model->info['partyLv']);
        $Act768Model = Master::getAct768($this->uid);
        $buffRate = 1;
        if($Act768Model->info['buff'] > 0){
            $partyBuffCfg = Game::getcfg_info('party_buff',$Act768Model->info['buff']);
            $buffRate = 1+$partyBuffCfg['buff']/100;
        }
        foreach($partyCfg['food_rwd'] as $k => $items){
            $total = ceil($items['count']*$pickCount*$buffRate);
            Master::add_item($this->uid,$items['kind'],$items['id'],$total);
        }
        $this->info['isHookPick'] = 1;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }
}