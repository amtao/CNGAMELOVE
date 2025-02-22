<?php
//弹劾操作
class tanheMod extends Base
{
    /**
     * 获取基础信息 
     * 最大关卡
     */
    public function getBaseInfo(){
        $Act721Model = Master::getAct721($this->uid);
        $Act721Model->back_data();
    }
    /*
	 * 获取弹劾信息
	 */
	public function getTanheInfo($params){
        //战斗内信息 伤害值 技能信息
        $copyId = Game::intval($params,"copyId");
        $Act722Model = Master::getAct722($this->uid);
        $Act722Model->getInfoById($copyId);
    }

    /*
	 * 战斗
	 */
	public function fight($params){
        // $epId = Game::intval($params,"epId");
        $cardId = Game::intval($params,"cardId");
        $Act722Model = Master::getAct722($this->uid);
        $Act722Model->fight($cardId);
        
    }

    /*
	 * 扫荡
	 */
	public function wipeOut($params){
        $copyId = Game::intval($params,"copyId");
        $Act721Model = Master::getAct721($this->uid);
        $Act721Model->wipe($copyId);
    }

    
    /*
	 * 周卡额外领取奖励
	 */
	public function weekWipeOut(){
        $Act721Model = Master::getAct721($this->uid);
        $Act721Model->weekWipe();
    }




}