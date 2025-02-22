<?php 
require_once "ActBaseModel.php";
/*
 * 弹劾次数
 */

class Act720Model extends ActBaseModel{
    
    public $atype = 720;//活动编号

	public $comment = "弹劾次数";
	public $b_mol = "tanhe";//返回信息 所在模块
    public $b_ctrl = "free";//返回信息 所在控制器
    
    public $_init = array(
        'count' => 0,         //每天免费弹劾次数
        'wipeCount' => 0,     //扫荡次数
        'weekCount' => 0,     //使用周卡领取奖励次数
        'pickCopy' => array(), //领过奖励的copy
    );
    
    //免费弹劾次数
    public function setTanheCount(){
        $this->info['count']++;
        $this->save();
    }

    //免费弹劾次数
    public function checkTanheCount(){
        $times = Game::getcfg_param("tanhe_times");
        if($this->info['count'] >= $times){
            Master::error(TANHE_NO_WIPE_COUNT);
        }
    }
    
    //免费扫荡次数
    public function setFreeWipeCount(){
        $this->info['wipeCount']++;
        $this->save();
    }

    //免费扫荡次数
    public function checkFreeWipeCount(){
        $times = Game::getcfg_param("tanhe_times");
        if($this->info['wipeCount'] >= $times){
            Master::error(TANHE_NO_WIPE_COUNT);
        }
    }

    //使用周卡领取奖励次数
    public function setWeekCount(){
        $this->info['weekCount']++;
        $this->save();
    }

    //使用周卡领取奖励次数
    public function checkWeekCount(){
        $times = Game::getcfg_param("tanhe_times");
        if($this->info['weekCount'] >= $times){
            Master::error(TANHE_NO_WIPE_COUNT);
        }
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
