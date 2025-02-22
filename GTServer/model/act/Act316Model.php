<?php
require_once "ActBaseModel.php";
/*
 * 连续首充福利
 */
class Act316Model extends ActBaseModel
{
	public $atype = 316;//活动编号
	
	public $comment = "连续首充福利";
	public $b_mol = "fuli";//返回信息 所在模块
	public $b_ctrl = "fexchofuli";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		'num' => 0,//充值最高金额
		'rwd' => array(
		),
		'money' => 0,
	);
	
	/*
	 * 首充
	 */
	public function do_save($num,$money,$type = 0,$giftNum){
		// if(empty($this->info['money'])|| $this->info['money'] < $num){
		// 	$UserModel = Master::getUser($this->uid);
		// 	$channel = $UserModel->info['channel_id'];
		// 	$platform = $UserModel->info['platform'];
		// 	Common::loadModel('OrderModel');
		// 	$list = OrderModel::recharge_list($platform,$channel);
		// 	$dollar = $list[$money]['dollar'];
		// 	$this->info['money'] = $dollar;
		// 	$this->save();
		// }
		// if(empty($this->info['num'])|| $this->info['num'] < $num){
		// 	$this->info['num'] = $num;
		// 	$this->save();
		// }
		//if(empty($this->info['money'])){
			if($type == 4){
				$num = $giftNum;
			}
			$UserModel = Master::getUser($this->uid);
			$channel = $UserModel->info['channel_id'];
			$platform = $UserModel->info['platform'];
			Common::loadModel('OrderModel');
			$list = OrderModel::recharge_list($platform,$channel);
			$dollar = $list[$money]['dollar'];
			$this->info['money'] += $dollar;
		//	$this->save();
		//}
		///if(empty($this->info['num'])){
			$this->info['num'] += $num;
			$this->save();
		//}
	}
	
	/*
	 * 领取首充奖励
	 */
	public function rwd($lv){
		//发奖励 配置
		$rwd_cfg = Game::getcfg_info('fuli_fc_ex',$lv);
		if(empty($rwd_cfg)){
			Master::error(ACT_HD_GIVE_ATTRIBUTE_ERROR);
		}

		//领取首冲奖励
		if ($rwd_cfg['num']>$this->info['num']){
			Master::error(ACT66_UNRECHARGE);
		}elseif ($this->info['rwd']['$lv'] > 0){
			Master::error(ACT66_HAVE_RECEIVE);
		}
		
		//领奖记录
		$this->info['rwd'][$lv] = 1;
		$this->save();
		
		
		Master::add_item3($rwd_cfg['firstRwd']);
	}

	public function check66rwd(){
		if(
			(!isset($this->info['rwd'][1]))  || empty($this->info['rwd'][1])){
			$Act66Model = Master::getAct66($this->uid);
			if($Act66Model->info['rwd'] >0){
				$this->info['rwd'][1] = 1;
				$this->save();
				//Master::other_debug("316 save");
			}else{
				if($Act66Model->info['ctime']>0){
					if($this->info['num']<50){
						$this->info['num'] = 50;
						$this->save();
					}
				}
			}
		}
	}
}