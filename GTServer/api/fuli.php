<?php
//签到
class FuliMod extends Base
{
	/*
	 * 签到领奖
	 */
	public function qiandao($params){
		$Act37Model = Master::getAct37($this->uid);
		$Act37Model->rwd();
		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(20,1);
	}
	
	/*
	 * 首充领奖
	 */
	public function fcho($params){
		$Act66Model = Master::getAct66($this->uid);
		$Act66Model->rwd();
	}

	/*
	 * 连续首充领奖
	 */
	public function fcho_ex($params){
		$Act316Model = Master::getAct316($this->uid);
		$rwdid = Game::intval($params,'id');
		$Act316Model->rwd($rwdid);
	}
	
	
	/*
	 * VIP领奖
	 */
	public function vip($params){
		//领奖的档次ID
		$vipid = Game::intval($params,'id');
		
		//获取我的vip
		$UserModel = Master::getUser($this->uid);
		//验证vip等级
		if($UserModel->info['vip'] < $vipid){
			Master::error(PARAMS_ERROR); //参数错误
		}
		
		$Act67Model = Master::getAct67($this->uid);
		$Act67Model->rwd($vipid);
	}

    /*
     * VIP购买特惠礼包
     */
    public function buy($params){
        //领奖的档次ID
        $vipid = Game::intval($params,'id');

        //获取我的vip
        $UserModel = Master::getUser($this->uid);
        //验证vip等级
        if($UserModel->info['vip'] < $vipid){
            Master::error(PARAMS_ERROR); //参数错误
        }

        $Act67Model = Master::getAct67($this->uid);
        $Act67Model->buyRwd($vipid);
    }
	
	/*
	 * 月卡/年卡  领取
	 */
	public function mooncard($params){
		//月卡类型id
		$id = Game::intval($params,'id');
		
		$Act68Model = Master::getAct68($this->uid);
		$Act68Model->rwd($id);
	}

	public function share($params){
        $id = Game::intval($params,'id');
        $Act6153Model = Master::getAct6153($this->uid);
//        $Act6153Model->share($id);
    }

    public function monday($params){
        $Act37Model = Master::getAct37($this->uid);
        $Act37Model->monday();
	}
	
	//购买0元购礼包
	public function buyZeroGift($params){
		$id = Game::intval($params,'id');

		$Act701Model = Master::getAct701($this->uid);
		$Act701Model -> buyGift($id);
	}

	//领取结束的返利奖励
	public function pickZeroRebate($params){
		$id = Game::intval($params,'id');

		$Act701Model = Master::getAct701($this->uid);
		$Act701Model -> pickRebate($id);
	}

	//领取钱庄的奖励
	public function pickBankAward($params){
		$id = Game::intval($params,'id');
		
		$Act702Model = Master::getAct702($this->uid);
		$Act702Model -> pickAward($id);
	}

}









