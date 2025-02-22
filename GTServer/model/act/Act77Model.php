<?php
require_once "ActBaseModel.php";
/*
 * 国子监-子嗣信息
 */
class Act77Model extends ActBaseModel
{
	public $atype = 77;//活动编号
	
	public $comment = "国子监-子嗣信息";
	public $b_mol = "gzj";//返回信息 所在模块
	public $b_ctrl = "son";//返回信息 所在控制器

	public $is_up;
//
//	public $init = array(
//		sid => array(
//          'popular' => 0,
//          'max' => 1,
//          'level' => 0,
//			'lq' => Game::get_now(),//上一次领取奖励的时间
//      )
//	);

	/**
	 * 开始打工
	 */
	public function addSonInfo($sonData){
		$sid = $sonData['sonuid'];
		if(isset($this->info[$sid])) $this->info[$sid] = array();
		$cfg = Game::getcfg('gzj_design');
		if(!isset($cfg[2]['popular'])){
			Master::error(GZJ_CONFIG_LOSS);
		}
		$this->info[$sid] = array(
			'e2' => $sonData['e2'],
			'lq' => Game::get_now(),
			'popular' => 0,
			'max' => $cfg[2]['popular'],
			'level' => 1,
			'giftId' => 1,
		);
		$this->save();
	}

	/**
	 * 添加人气
	 * @param $sid
	 * @param $num
	 */
	public function add($sid,$num=0){
		if(empty($this->info[$sid])){
			Master::error(GZJ_CHILD_NOT_IN_SCHOOL);
		}
		$info = $this->info[$sid];
		$info['popular'] +=$num;
		if(!empty($info['max']) && ($info['popular'] >= $info['max'])){
			$info['level'] +=1;
			$cfg = Game::getcfg('gzj_design');

			$info['max'] = empty($cfg[$info['level']+1]['popular']) ? 0 : $cfg[$info['level']+1]['popular'];
			//重置礼包
			if(empty($cfg[$info['level']])){
				Master::error(GZJ_NOT_FUND_GIFT.$info['level']);
			}
			$giftId = 0;
			$rand_num = rand(1,100);
			foreach ($cfg[$info['level']]['drwd'] as $itId => $rand) {
				if($rand['min']<=$rand_num && $rand['max']>=$rand_num){
					$giftId = $itId;
					break;
				}
			}
			$info['lq'] = Game::get_now();
			$info['giftId'] = $giftId;
			$this->is_up[$sid] = true;
		}
		$this->info[$sid] = $info;
		$this->save();
	}

	/**
	 * 删除该子嗣信息
	 * @param $workData
	 */
	public function del($workData){

		if(empty($this->info[$workData['sid']])) {//判断是否存有子嗣信息
			Master::error(GZJ_NOT_FUND_CHILD_SCHOOL_INFORMATION);
		}
		unset($this->info[$workData['sid']]);
		$this->save();
	}


	/**
	 * 行贿
	 * @param $sid
	 * @param $id
	 */
	public function bribery($sid,$id){

		//判断该子嗣是否正在监学
		if(empty($this->info[$sid])){
			Master::error(GZJ_CHILD_NOT_IN_SCHOOL);
		}
		$Act79Model = Master::getAct79($this->uid);
		$pop = $Act79Model->sendGift($id);

		$this->add($sid,$pop);
		Master::add_item($this->uid,2,40,$pop);
	}

	/**
	 * 获取每日奖励 单子嗣
	 * @param $sid
	 */
	public function get_day_reward($sid){
		if(empty($this->info[$sid])){
			Master::error(GZJ_NOT_FUND_CHILD);
		}
		if(Game::get_now() - $this->info[$sid]['lq'] < 24*3600){
			Master::error(GZJ_NOT_TIME_TO_RECEICE);
		}
		//获取每日礼包配置信息
		$cfg = Game::getcfg_info('gzj_daygift',$this->info[$sid]['giftId'],'不存在该礼包');
		$rk = Game::get_rand_key(100,$cfg['rwd'],'prob');
		if(!empty($cfg['rwd'][$rk])){
			//奖励
			$itemid = $cfg['rwd'][$rk]['id'];
			$count = $cfg['rwd'][$rk]['count'];
			$kind = empty($cfg['rwd'][$rk]['kind']) ? 1 : $cfg['rwd'][$rk]['kind'];
			Master::add_item($this->uid,$kind,$itemid,$count);
			//加声望
			$SonModel = Master::getSon($this->uid);
			$honor = $SonModel->info[$sid]['honor'];
			$jc_cfg = Game::getcfg('gzj_jc');
			if(empty($jc_cfg[$honor])){
				$back_items[] = array(
					'sid'=>$sid,
					'items'=>array(
						array(
							'id'=>$itemid,
							'count'=>$count,
							'kind'=>$kind
						),
					)
				);
			}else{
				$back_items[] = array(
					'sid'=>$sid,
					'items'=>array(
						array(
							'id'=>$itemid,
							'count'=>$count,
							'kind'=>$kind
						),
						array(
							'id'=>$jc_cfg[$honor]['rwd']['id'],
							'count'=>$jc_cfg[$honor]['rwd']['count'],
							'kind'=>$jc_cfg[$honor]['rwd']['kind']
						)
					)
				);
				$this->add($sid,$jc_cfg[$honor]['rwd']['count']);
				Master::add_item($this->uid,$jc_cfg[$honor]['rwd']['kind'],$jc_cfg[$honor]['rwd']['id'],$jc_cfg[$honor]['rwd']['count']);
			}

			Master::back_data($this->uid,'gzj','dgift',$back_items);//返回道具列表信息

			//重置礼包
			if(empty($this->is_up[$sid]) && $this->info[$sid]['level'] > 0){
				$design_cfg = Game::getcfg('gzj_design');
				if(empty($design_cfg[$this->info[$sid]['level']])){
					Master::error(GZJ_NOT_FUND_GIFT.$this->info[$sid]['level']);
				}
				$giftId = 0;
				$rand_num = rand(1,100);

				foreach ($design_cfg[$this->info[$sid]['level']]['drwd'] as $itId => $rand) {
					if($rand['min']<=$rand_num && $rand['max']>=$rand_num){
						$giftId = $itId;
					}
				}
				$this->info[$sid]['lq'] = Game::get_now();
				$this->info[$sid]['giftId'] = $giftId;
				$this->save();
			}
		}
	}

	/**
	 * 一键领取每日奖励
	 */
	public function all_get_day_reward(){
		if(empty($this->info)){
			Master::error(GZJ_NO_CHILD_IN_SCHOOL);
		}
		$back_items = array();
		$SonModel = Master::getSon($this->uid);
		$jc_cfg = Game::getcfg('gzj_jc');
		foreach ($this->info as $sid => $data){
			if(Game::get_now() - $data['lq'] < 24*3600){
				continue;
			}
			$cfg = Game::getcfg_info('gzj_daygift',$data['giftId'],GZJ_GIFT_NOT_EXIST);
			$rk = Game::get_rand_key(100,$cfg['rwd'],'prob');
			if(!empty($cfg['rwd'][$rk])) {
				$itemid = $cfg['rwd'][$rk]['id'];
				$count = $cfg['rwd'][$rk]['count'];
				$kind = empty($cfg['rwd'][$rk]['kind']) ? 1 : $cfg['rwd'][$rk]['kind'];
				$items = array(array('id' => $itemid, 'count' => $count, 'kind' => $kind));
				$honor = $SonModel->info[$sid]['honor'];
				if(empty($jc_cfg[$honor])) {
					$back_items[] = array(
						'sid' => $sid,
						'items' => $items
					);
				}else{
					$back_items[] = array(
						'sid' => $sid,
						'items' => array(
							array('id' => $itemid, 'count' => $count, 'kind' => $kind),
							array(
								'id'=>$jc_cfg[$honor]['rwd']['id'],
								'count'=>$jc_cfg[$honor]['rwd']['count'],
								'kind'=>$jc_cfg[$honor]['rwd']['kind']
							)
						)
					);
					$this->add($sid,$jc_cfg[$honor]['rwd']['count']);
					$items[] = array(
						'id'=>$jc_cfg[$honor]['rwd']['id'],
						'count'=>$jc_cfg[$honor]['rwd']['count'],
						'kind'=>$jc_cfg[$honor]['rwd']['kind']
					);
				}
				Master::add_item3($items);
				//重置礼包
				if(empty($this->is_up[$sid]) && $this->info[$sid]['level'] > 0){
					$design_cfg = Game::getcfg('gzj_design');
					if(empty($design_cfg[$this->info[$sid]['level']])){
						Master::error(GZJ_NOT_FUND_GIFT.$this->info[$sid]['level']);
					}
					$giftId = 0;
					$rand_num = rand(1,100);
					foreach ($design_cfg[$this->info[$sid]['level']]['drwd'] as $itId => $rand) {
						if($rand['min']<=$rand_num && $rand['max']>=$rand_num){
							$giftId = $itId;
						}
					}
					$this->info[$sid]['lq'] = Game::get_now();
					$this->info[$sid]['giftId'] = $giftId;
					$this->save();
				}
			}

		}
		if(!empty($back_items)){
			Master::back_data($this->uid,'gzj','dgift',$back_items);//返回道具列表信息
		}else{
			Master::error(GZJ_NO_GIFT);
		}
	}
	/**
	 * 不返回数据
	 */
	public function back_data()
	{
	}
}
