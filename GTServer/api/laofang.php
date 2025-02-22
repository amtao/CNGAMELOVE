<?php
//排行榜
class LaofangMod extends Base
{
	
	/**
	 *  $params打或者一键打-0普通打,1死里打 鞭打
	 */
	public function bianDa($params){
		$type = Game::intval($params,'type');
        $id = Game::intval($params,'id');
		$Act19Model = Master::getAct19($this->uid);
		$Act19Model->bianDa($type,$id);
	}
	
}
