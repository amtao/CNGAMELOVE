<?php
//排行榜
class ChenghaoMod extends Base
{
	
	/*
	 * 设置称号
	 */
	public function setChengHao($params){
        Master::vip_limit($this->uid,5,'LOOK_FOR_VIP_LEVEL_SHORT');
		//称号id
		$chid = Game::intval($params,'chid');
		//称号
		$Act25Model = Master::getAct25($this->uid);
		$Act25Model->set_chenghao($chid);
		
		//插入称号返回
		$data = array();
		$data['chenghao'] = $Act25Model->outf['setid'];
		Master::back_data($this->uid,'user','user',$data,true);
		
	}
	
	
	/*
	 * 取消称号
	 */
	public function offChengHao($params){
		//称号
		$Act25Model = Master::getAct25($this->uid);
		$Act25Model->off_chenghao();

        //插入称号返回
        $data = array();
        $data['chenghao'] = 0;
        Master::back_data($this->uid,'user','user',$data,true);
		
	}
	
	/*
	 * 王爷领取奖励
	 */
	public function wyrwd($params){
		
//		$Act30Model = Master::getAct30($this->uid);
//		$Act30Model->get_rwd();
		
	}

}
