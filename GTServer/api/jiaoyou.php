<?php
//郊游
class jiaoyouMod extends Base
{
    //获取基础信息  
    public function getBaseInfo(){
        $Act740Model = Master::getAct740($this->uid);
        $Act740Model->back_data();
    }

    public function getFightInfo($params){
        $heroId = Game::intval($params,"heroId");
        
        $HeroModel = Master::getHero($this->uid);
        $HeroModel->check_info($heroId);

        $Act744Model = Master::getAct744($this->uid);
        $Act744Model->getInfoById($heroId);

    }

    //战斗
    public function fight($params){
        // $epId = Game::intval($params,"epId");
        $cardId = Game::intval($params,"cardId");
        $Act744Model = Master::getAct744($this->uid);
        $Act744Model->fight($cardId);
    }

    //花费元宝购买次数
    public function cashBuyCount(){
        $Act743Model = Master::getAct743($this->uid);
        $Act743Model->cashBuy();
    }

    //开始守护
    public function startGuard($params){
        $heroId = Game::intval($params,'heroId');
        $copyId = Game::intval($params,'copyId');
        $cardEquips = Game::strval($params,'cardEquips');

        $HeroModel = Master::getHero($this->uid);
        $HeroModel->check_info($heroId);

        $Act745Model = Master::getAct745($this->uid);
        $Act745Model->startGuard($heroId,$copyId,$cardEquips);
    }

    //刷新守护列表
    public function refreshGuardList($params){
        $heroId = Game::intval($params,'heroId');

        $HeroModel = Master::getHero($this->uid);
        $HeroModel->check_info($heroId);

        $Act745Model = Master::getAct745($this->uid);
        $Act745Model->refreshList($heroId);
    }

    //领取守护奖励
    public function pickGuardAward($params){
        $heroId = Game::intval($params,'heroId');
        $copyId = Game::intval($params,'copyId');

        $HeroModel = Master::getHero($this->uid);
        $HeroModel->check_info($heroId);

        $Act745Model = Master::getAct745($this->uid);
        $Act745Model->pickAward($heroId,$copyId);
    }

    //领取每周守护次数奖励
    public function pickGuardWeekAward($params){
        $id = Game::intval($params,'id');
        $Act742Model = Master::getAct742($this->uid);
        $Act742Model->pickAward($id);
    }


}