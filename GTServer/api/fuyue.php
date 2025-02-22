<?php

//赴约（万花阁）
class FuYueMod extends Base{

	//获取赴约信息
	public function getFuyueInfo(){
		//故事
		$Act703Model = Master::getAct703($this->uid);
		$Act703Model->back_data();
		//回忆录
		$Act704Model = Master::getAct704($this->uid);
		$Act704Model->back_data();
		//fight
		$Act705Model = Master::getAct705($this->uid);
		$Act705Model->back_data();
		//兑换商城
		$Act706Model = Master::getAct706($this->uid);
		$Act706Model->back_data();
	}

	//开始故事
	//chooseInfo 选择信息
	//useItem 是否使用道具
	public function startStory($params){
		$storyChoose = Game::arrayval($params,"chooseInfo");
		
		$Act703Model = Master::getAct703($this->uid);
		$Act703Model->randZongGuShi($storyChoose);

		
		//每日任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(148,1);

		//日常任务
		// $Act35Model = Master::getAct35($this->uid);
		// $Act35Model->do_act(18,1);
	}

	//开始战斗
	public function startFight(){
		$Act705Model = Master::getAct705($this->uid);
		$Act705Model->fight();
	}

	//保存故事进度
	public function saveStory(){
		$Act704Model = Master::getAct704($this->uid);
		$Act704Model->saveStory();
	}

	public function delStory($params){
		$id = Game::intval($params,"id");

		$Act704Model = Master::getAct704($this->uid);
		$Act704Model->delStory($id);
	}

	//领取通关奖励
	public function pickClearanceAward(){
		$Act705Model = Master::getAct705($this->uid);
		$Act705Model->pickAward();
	}

	//不保存故事进度
	//清空故事进度 战斗进度
	public function noSaveStory(){
		$Act703Model = Master::getAct703($this->uid);
		$Act703Model->removeData();

		$Act705Model = Master::getAct705($this->uid);
		$Act705Model->removeData();
	}

	//兑换物品
	public function exchange($params){
		$id = Game::intval($params,"id");
		$num = Game::intval($params,"num");
		$Act706Model = Master::getAct706($this->uid);
		$Act706Model->exchange($id,$num);
	}

	//消耗道具购买次数
	public function buyCount(){
		$Act703Model = Master::getAct703($this->uid);
		$Act703Model->buyCount();
	}

}