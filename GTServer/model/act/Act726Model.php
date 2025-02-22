<?php 
require_once "ActBaseModel.php";
/*
 * 伙伴拜访
 */

class Act726Model extends ActBaseModel{
    
    public $atype = 726;//活动编号

	public $comment = "伙伴答题-答对次数";
	public $b_mol = "hero";//返回信息 所在模块
    public $b_ctrl = "right";//返回信息 所在控制器
    
    public $_init = array(
        'qaType' => 0, //答对类型
        'rightCount' => 0, //答对次数
        'anCount' => 0,//回答次数
        'isOrient' => 0,//是否为定向问候
        'heroId' => 0, //定向问候伙伴
    );

    /**
     * id 题库里id
     * answerId 选择答案的下标
     */
    public function chooseAnswer($id,$answerId){
        if($this->info['anCount'] >= 3){
            Master::error(VISIT_END);
        }
        if($this->info['qaType'] != 3){
            $quseTionCfg = Game::getcfg_info("game_question",$id);
            if($quseTionCfg['type'] != $this->info['qaType']){
                Master::error(VISIT_TYPE_ERROR);
            }
            if($answerId == $quseTionCfg['idright']){
                $this->info['rightCount']++;
            }
        }else{
            if($index >= 1){
                $this->info['rightCount']++;
            }
        }
        $this->info['anCount'] ++;
        $this->save();
    }
    
    //结束这一局游戏 获取奖励
    public function endGame(){
        if($this->info['qaType'] == 0){
            Master::error(VISIT_NO_START);
        }
        $Act725Model = Master::getAct725($this->uid);
        if($this->info['qaType'] != 3){
            $rwdCfg = Game::getcfg_info("visit_rwd",$this->info['rightCount']);
            if($this->info['isOrient'] == 0){
                if($Act725Model->info['getAwardCount'] < 3){
                   $Act6001Model = Master::getAct6001($this->uid);
                   $Act6001Model->addHeroJB($this->info['heroId'],$rwdCfg['jiban']);
                }
                $Act725Model->info['getAwardCount']++;
                $Act725Model->save();
            }else{
                $Act6001Model = Master::getAct6001($this->uid);
                $Act6001Model->addHeroJB($this->info['heroId'],$rwdCfg['jiban']);
            }
        }else{
            //服装升级增加的属性
            $Act756Model = Master::getAct756($this->uid);
            $extraCount = $Act756Model->getPropCount(7);
            
            if($this->info['isOrient'] == 1 || ($this->info['isOrient'] == 0 && $Act725Model->info['getAwardCount'] < (3+$extraCount))){
                $rwdCfg = Game::getcfg_info("visit_rwd",999);
                $addNum = $rwdCfg['jiban'];
                if($this->info['anCount'] >= 2){
                    $addNum = rand(15,20);
                }
                $Act6001Model = Master::getAct6001($this->uid);
                $Act6001Model->addHeroJB($this->info['heroId'],$addNum);
            }
            if($this->info['isOrient'] == 0){
                $Act725Model->info['getAwardCount']++;
                $Act725Model->save();
            }
        }
        $this->info = $this->_init;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
