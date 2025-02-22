<?php
//世界BOSS
class wordbossMod extends Base
{
	/*
	 * 打开世界BOSS
	 */
	public function wordboss($params){
		
		//割二蛋来袭 信息类
		$Act22Model = Master::getAct22($this->uid);
		
		//检查并发送排行奖励
		$Act22Model->click_kill();
		
		//击杀榜单
		$Sev4Model = Master::getSev4();
		$Sev4Model->bake_data();
		return true;
	}
	
	/*
	 * 点击积分击杀榜
	 */
	public function scoreRank($params){
		//积分榜
		$Redis4Model = Master::getRedis4();
		$Redis4Model->back_data();
		$Redis4Model->back_data_my($this->uid);
		
		//击杀榜单
		$Sev4Model = Master::getSev4();
		$Sev4Model->bake_data();
	}
	
	/*
	 * 点击伤害排行
	 */
	public function g2dHitRank($params){
		//葛二蛋伤害排行
		$Redis5Model = Master::getRedis5();
		$Redis5Model->back_data();
		$Redis5Model->back_data_my($this->uid);
	}
	
	/*
	 * 进入战场 蒙古来袭(刷新战场信息)
	 */
	public function goFightmg($params){
		//刷新击杀奖励日志
		$Sev3Model = Master::getSev3();
		$Sev3Model->list_click($this->uid);
	}
	
	/*
	 * 进入战场 葛二蛋来袭(刷新战场信息)
	 */
	public function goFightg2d($params){
		//BOSS信息
		$Act22Model = Master::getAct22($this->uid);
		
		//检查并发送排行奖励
		$Act22Model->click_kill();
		$Act22Model->back_data();
		
		//击杀榜单
		$Sev4Model = Master::getSev4();
		$Sev4Model->bake_data();
		
		//葛二蛋伤害排行
		$Redis5Model = Master::getRedis5();
		$Redis5Model->back_data();
		$Redis5Model->back_data_my($this->uid);

		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(139,1);
	}
	
	/*
	 * 打蒙古军
	 * id:门客ID
	 */
	public function hitmenggu($params){
		$hid= Game::intval($params,'id');
		$Act21Model = Master::getAct21($this->uid);
		$Act21Model->hit($hid);
		
		//刷新获得
		$Sev3Model = Master::getSev3();
		$Sev3Model->list_click($this->uid);
	}
	
	/*
	 * 蒙古军
	 * 使用出战令 复活门客
	 */
	public function comebackmg($params){
		//需要复活的门客ID
		$hero_id = Game::intval($params,'id');
		//门客出战列表
		$Act4Model = Master::getAct4($this->uid);
		//这个门客 是不是可以出战(活的)
		$Act4Model->cone_back($hero_id);
	}
	
	/*
	 * 打葛二蛋
	 * id:门客ID
	 */
	public function hitgeerdan($params){
		$hid= Game::intval($params,'id');
		$type = Game::intval($params, 'type');
		$Act22Model = Master::getAct22($this->uid);
		$Act22Model->hit($hid, $type);
		//限时-郊祀献礼次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6175',1);

        //舞狮大会 - 郊祀献礼次数
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(18,1);
	}
	
	/*
	 * 葛二蛋
	 * 使用出战令 复活门客
	 */
	public function comebackg2d($params){
		//需要复活的门客ID
		$hero_id = Game::intval($params,'id');
		//门客出战列表
		$Act5Model = Master::getAct5($this->uid);
		//这个门客 是不是可以出战(活的)
		$Act5Model->cone_back($hero_id);
	}
	
	/*
	 * 积分兑换
	 */
	public function shopBuy($params){
		$id = Game::intval($params,"id");
		$Act23Model = Master::getAct23($this->uid);
		$Act23Model->goumai($id);
	}
	
}
