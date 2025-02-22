<?php
require_once "ActBaseModel.php";
/*
 * 公会宴会-红包
 */
class Act769Model extends ActBaseModel
{
    public $atype = 769;//活动编号

    public $comment = "公会宴会-抢红包";
    public $b_mol = "club";//返回信息 所在模块
    public $b_ctrl = "party";//返回信息 所在控制器

    public $_init = array(
        'robTimes' => 0,//抢红包次数
    );

    //根据今日抢红包次数判断是否可以抢
    public function checkIsRob(){
        $maxRobCount = Game::getcfg_param('club_giftTimes');
        if(!empty($this->info['robTimes']) && $this->info['robTimes'] >= $maxRobCount){
            Master::error(CLUB_PARTY_RED_BAG_ROB_MAX);
        }
    }

    //抢完红包之后设置数量
    public function setRobCount(){
        if(empty($this->info['robTimes'])){
            $this->info['robTimes'] = 0;
        }
        $this->info['robTimes']++;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}