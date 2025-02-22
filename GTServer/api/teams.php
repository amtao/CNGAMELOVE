<?php
//编队操作
class TeamsMod extends Base
{
    /**
     * 设置编队
     * heroId = 0 为普通战斗编队
     * heroId != 0 为郊游战斗编队
     */
    public function setTeams($params){
        $teams = Game::strval($params,'teams');
        $heroId = Game::intval($params,'heroId');
        $Act763Model = Master::getAct763($this->uid);
        $Act763Model->setTeams($teams,$heroId);
    }

    /**
     * 获取当前上阵的卡牌数据 
     * heroId != 0 为郊游战斗获取当前上阵卡牌
     */
    public function getCurrentEquipCard($params){
        $heroId = Game::intval($params,'heroId');

        $Act764Model = Master::getAct764($this->uid);
        $Act764Model->randCards($heroId);
    }



}