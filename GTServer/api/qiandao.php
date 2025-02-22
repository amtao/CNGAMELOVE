<?php
//签到
class QiandaoMod extends Base
{
	/**
	 签到领奖
	 */
	public function rwd($params){
        $Act37Model = Master::getAct37($this->uid);
        $Act37Model->rwd();
	}
}









