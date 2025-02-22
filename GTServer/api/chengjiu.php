<?php
//成就
class ChengjiuMod extends Base
{
	/**
	 * 领取成就奖励
	 */
	public function rwd($params){
		//成就ID
		$id = Game::intval($params,'id');
		
		//成就
		$Act36Model = Master::getAct36($this->uid);
		$Act36Model->rwd($id);
	}
}









