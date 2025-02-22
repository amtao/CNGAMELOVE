<?php

/**
 * 商城
 * Class ShopMod
 */
class ShopMod extends Base
{
	
	/**
	 * 商城列表
	 */
	public function shoplist(){
		//单品限购列表
		$Act81Model = Master::getAct81($this->uid);
		$Act81Model->back_data();
		//特惠礼包 列表
		$Act82Model = Master::getAct82($this->uid);
		$state82 = $Act82Model->getState();
		if(!empty($state82)){
		   $Act82Model->back_data();
		}
	}
	
	
	/**
	 * 单品限购 - 购买
	 * @param $params
	 */

	public function shopLimit($params){
		$id = Game::intval($params,'id');
		$count = Game::intval($params,'count');
		$Act81Model = Master::getAct81($this->uid);
		$Act81Model->shopLimit($id, $count);
	}
	
	/*
	 * 特惠礼包 -购买
	 * @param $param['id']  档次id
	 * */
	public function shopGift($params){
		$id = Game::intval($params,'id');
		$Act82Model = Master::getAct82($this->uid);
		$Act82Model->shopGift($id);
	}

	
}









