<?php
require_once "ActBaseModel.php";
/*
 * 公会宴会-领取游戏奖励
 */
class Act772Model extends ActBaseModel
{
    public $atype = 772;//活动编号

    public $comment = "公会宴会-领取游戏奖励";
    public $b_mol = "club";//返回信息 所在模块
    public $b_ctrl = "party";//返回信息 所在控制器

    public $_init = array(
        'pickCount' => 0,//领取游戏奖励
    );
    
    //领取游戏奖励
    public function pickGameAward(){
        $maxPickCount = Game::getcfg_param("club_gameRwdMax");
        if(empty($this->info['pickCount'])){
            $this->info['pickCount'] = 0;
        }
        if($this->info['pickCount'] >= $maxPickCount){
            return;
        }
        $items = Game::getcfg_param("club_gameRwd");
        $itemArr = explode('|',$items);
        Master::add_item($this->uid,$itemArr[1],$itemArr[0],$itemArr[2]);
        $this->info['pickCount']++;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}