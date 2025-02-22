<?php 
require_once "ActBaseModel.php";
/*
 * 伙伴郊游--守护次数
 * 每周刷新
 */

class Act742Model extends ActBaseModel{
    
    public $atype = 742;//活动编号

	public $comment = "伙伴郊游";
	public $b_mol = "jiaoyou";//返回信息 所在模块
    public $b_ctrl = "jiaoyou";//返回信息 所在控制器
    
    public $_init = array(
        'weekdefendCount' => 0,//每周守护次数
        'weekAwardPick' => array(),
    );

    //领取每周守护次数奖励
    public function pickAward($id){
        if(in_array($id,$this->info['weekAwardPick'])){
            Master::error(HAS_PICK_AWARD);
        }
        $jiaoyouWeekCfg = Game::getcfg_info("jiaoyou_week",$id);
        if($this->info['weekdefendCount'] < $jiaoyouWeekCfg['cishu']){
            Master::error(JIAOYOU_GUARD_WEEK_NOT_ENPUGH);
        }
        Master::add_item3($jiaoyouWeekCfg['jiangli']);
        array_push($this->info['weekAwardPick'],$id);
        $this->save();
    }



    public function make_out(){
        $this->outf = $this->info;
    }

}
