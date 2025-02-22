<?php

/**
 * 新版办差
 */
class banchaiMod extends Base{
    
    //获取办差信息
    public function getInfo(){
        $Act712Model = Master::getAct712($this->uid);
        $Act712Model->back_data();
        $Act713Model = Master::getAct713($this->uid);
        $Act713Model->back_data();
        $Act714Model = Master::getAct714($this->uid);
        $Act714Model->back_data();
        $Act715Model = Master::getAct715($this->uid);
        $Act715Model->back_data();
        $Act716Model = Master::getAct716($this->uid);
        $Act716Model->calculationRecover();
    }

    //开始办差
    public function startBanchai(){
        $Act712Model = Master::getAct712($this->uid);
        $Act712Model->startBanchai();

        //主线任务
		$Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(1,1);

        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(117,1);

        //日常任务
        // $Act35Model = Master::getAct35($this->uid);
        // $Act35Model->do_act(6,1);

        //日常任务
        // $Act35Model = Master::getAct35($this->uid);
        // $Act35Model->do_act(19,1);
    }

    //选择结果
    public function chooseAnswer($params){
        $isYes = Game::intval($params,"yes");
        $Act712Model = Master::getAct712($this->uid);
        $Act712Model->chooseYN($isYes);
    }

    //复活
    public function revive(){
        $Act712Model = Master::getAct712($this->uid);
        $Act712Model->revive();
    }

    //放弃复活
    public function abandonRevive(){
        $Act712Model = Master::getAct712($this->uid);
        $Act712Model->dontRevive();
    }

    //购买开始次数
    public function buyCount(){
        $Act715Model = Master::getAct715($this->uid);
        $Act715Model->buyCount();
    }

    //使用办差令
    public function useBanchaiLing(){
        $Act715Model = Master::getAct715($this->uid);
        $Act715Model->buyCountByLing();
    }

    //领取结局奖励
    public function pickFinalAward($params){
        $id = Game::intval($params,"id");
        $Act714Model = Master::getAct714($this->uid);
        $Act714Model->pickFinalAward($id);
    }
}