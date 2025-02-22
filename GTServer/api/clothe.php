<?php
//郊游
class clotheMod extends Base
{
    //领取华服等级奖励
    public function pickHuaFuAward(){
        $Act755Model = Master::getAct755($this->uid);
        $Act755Model->getAward();
    }

    //锦衣华服升级
    public function jyUpLv($params){
        $suitId = Game::intval($params,'suitId');
        $Act756Model = Master::getAct756($this->uid);
        $Act756Model->brocadeUpLv($suitId);
    }

    public function putCard($params){
        $suitId = Game::intval($params,'suitId');
        $bSlot = Game::intval($params,'bSlot');
        $cardId = Game::intval($params,'cardId');

        $Act757Model = Master::getAct757($this->uid);
        $Act757Model->putCard($suitId,$cardId,$bSlot);
    }

    //获取槽位解锁信息
    public function getUnlockInfo($params){
        $suitId = Game::intval($params,'suitId');

        $Act757Model = Master::getAct757($this->uid);
        $Act757Model->checkSlotUnlock($suitId);
    }

    //刷新
    public function refresh($params){
        $suitId = Game::intval($params,'suitId');
        $bSlot = Game::intval($params,'bSlot');
        $sSlot = Game::intval($params,'sSlot');

        $Act758Model = Master::getAct758($this->uid);
        $Act758Model->refreshConsume($suitId,$bSlot);

        $Act757Model = Master::getAct757($this->uid);
        $Act757Model->refreshProp($suitId,$bSlot,$sSlot);
    }

    public function equipSpecial($params){
        $clotheId = Game::intval($params,'clotheId');
        $isEquip = Game::intval($params,'isEquip');

        $Act759Model = Master::getAct759($this->uid);
        $Act759Model->equipSpecial($clotheId, $isEquip);
    }
}