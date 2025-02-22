<?php

/**
 * 行商-新版日常
 */
class businessMod extends Base{

    public function getInfo(){
        $Act707Model = Master::getAct707($this->uid);
        $Act707Model->randCity();
        $Act708Model = Master::getAct708($this->uid);
        $Act708Model->back_data();
        $Act709Model = Master::getAct709($this->uid);
        $Act709Model->back_data();
        $Act710Model = Master::getAct710($this->uid);
        $Act710Model->back_data();
        $Act711Model = Master::getAct711($this->uid);
        $Act711Model->back_data();
        $Sev707Model = Master::getSev707();
        $Sev707Model->bake_data();
    }

    //开始行商
    public function startBusiness($params){
        $chooseArr = Game::arrayval($params,"chooseInfo");
        $Act707Model = Master::getAct707($this->uid);
        $Act707Model->start($chooseArr);

    }

    //去下一个城市
    public function nextTravel($params){
        $cityId = Game::intval($params,"id");
        $Act707Model = Master::getAct707($this->uid);
        $Act707Model->nextTravel($cityId);
    }

    //购买物品
    public function buyItem($params){
        $index = Game::intval($params,"index");
        $count = Game::intval($params,"count");
        $count = $count== 0?1:$count;
        $Act709Model = Master::getAct709($this->uid);
        $Act709Model->buyBusinessItem($index,$count);
    }

    //出售物品
    public function saleItem($params){
        $index = Game::intval($params,"index");
        $count = Game::intval($params,"count");
        $count = $count== 0?1:$count;
        $Act709Model = Master::getAct709($this->uid);
        $Act709Model->saleBusinessItem($index,$count);
    }

    //行商结束 领取奖励
    public function pickFinalAward($params){
        $Act707Model = Master::getAct707($this->uid);
        $Act707Model->pickAward();

        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(12,1);

        //日常任务
        // $Act35Model = Master::getAct35($this->uid);
        // $Act35Model->do_act(9,1);
    }

    public function buyCount(){
        $Act710Model = Master::getAct710($this->uid);
        $Act710Model->buyCount();
    }
}