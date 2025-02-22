<?php
//七日庆典
class sevendaysMod extends Base
{
    //七日签到
    public function sevenSign($params){
        $signday = Game::intval($params,'signday');
        
        $Act700Model = Master::getAct700($this->uid);
        $Act700Model->getSevenSignAward($signday);
    }

    //七日签到补签
    public function sevenSupplySign($params){
        $signday = Game::intval($params,'signday');
        
        $Act700Model = Master::getAct700($this->uid);
        $Act700Model->supplySign($signday);
    }

    //七日购买超值购礼包
    public function buyValueGift($params){
        $day = Game::intval($params,'day');

        $Act700Model = Master::getAct700($this->uid);
        $Act700Model->buyGift($day);
    }

    //七日连续登录领取最终奖励
    public function pickFinalAward(){

        $Act700Model = Master::getAct700($this->uid);
        $Act700Model->checkIsContinueLogin();

        $Act700Model->pickFinalAward();
    }

    //领取积分奖励
    public function pickScoreAward($params){
        //积分礼包id
        $id = Game::intval($params,'id');

        $Act700Model = Master::getAct700($this->uid);
        $Act700Model->pickScoreGift($id);
    }

    //领取任务奖励
    public function pickTaskAward($params){
        //taskid
        $taskid = Game::intval($params,'taskid');

        $Act700Model = Master::getAct700($this->uid);
        $Act700Model->pickTaskAward($taskid);
    }

}
