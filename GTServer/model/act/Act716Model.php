<?php

require_once "ActBaseModel.php";
/**
 * 办差
 */
class Act716Model extends ActBaseModel{
    public $atype = 716;

    public $comment = "办差-次数及恢复时间";
    public $b_mol = "office";//返回信息 所在模块
    public $b_ctrl = "recover";//返回信息 所在控制器

    public $_init = array(
        'startCount' => 5,//初始次数 共五次 
        'recoverTime' => 0,//五个小时恢复一次
    );

    

    //计算恢复次数
    public function calculationRecover(){
        //服装升级增加的属性
        $Act756Model = Master::getAct756($this->uid);
        $extraCount = $Act756Model->getPropCount(6);
        $maxCount = Game::getcfg_param('banchai_times');
        if($this->info['startCount'] >= ($maxCount+$extraCount)){
    
        }else{
            $interval = Game::getcfg_param('banchai_addtime');
            $timeSlot = Game::get_now()-($this->info['recoverTime']);
            if($timeSlot > 0){
                $count = intval($timeSlot/$interval);
                if(($this->info['startCount'] + $count) >= ($maxCount+$extraCount)){
                    $this->info['startCount'] = $maxCount + $extraCount;
                }else{
                    $this->info['startCount'] += $count;
                }
                $this->info['recoverTime'] += $count*$interval;
            }
        }
        $this->save(); 
    }

    public function make_out(){
        $this->outf = $this->info;
    }
}

