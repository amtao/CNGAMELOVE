<?php
require_once "ActBaseModel.php";
/*
 * 国子监-打工
 */
class Act76Model extends ActBaseModel
{
	public $atype = 76;//活动编号
	
	public $comment = "国子监-打工";
	public $b_mol = "gzj";//返回信息 所在模块
	public $b_ctrl = "list";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(//学院学习信息
		//空  / 没有人在打工
	);
	
	/*
	//每个打工位的初始化信息
	public $_init_info = array(
		'sid' => 1,	//子嗣ID 0 没人
		'start' => Game::get_now(),//开始时间
		'over' => 0,//罢工时间
	);
	*/
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out()
	{
		$outf = array();
		$Act77Model = Master::getAct77($this->uid);
		foreach ($this->info as $id => $dmsg){
			if(empty($dmsg['sid'])) continue;
			if(!empty($Act77Model->info[$dmsg['sid']])){
				$giftId = $Act77Model->info[$dmsg['sid']]['giftId'];
				$level = $Act77Model->info[$dmsg['sid']]['level'];
				$popular = $Act77Model->info[$dmsg['sid']]['popular'];
				$lq_over = $Act77Model->info[$dmsg['sid']]['lq']+24*3600;
				if(Game::is_over($dmsg['over']) && $lq_over>$dmsg['over']){
					$giftId = 0;
				}
			}
			$outf[] = array(
				'id' => $id,
				'sid' => $dmsg['sid'],
				'giftId' => empty($giftId) ? 0: $giftId,
				'level' => empty($level) ? 1 : $level,
				'popular' => empty($popular) ? 0 : $popular,
				'cd' => array(
					'next' => Game::dis_over($dmsg['over']),
					'label' => 'gzj',
				),
				'lqcd' => array(
					'next' => Game::dis_over($lq_over),
					'label' => 'gzj_lq',
				),
			);
		}
		$this->outf = $outf;
	}
	
	/*
	 * 开始学习
	 */
	public function start_work($id,$sid){
		//这个座位 有没有人
		if (isset($this->info[$id]) && $this->info[$id]['sid'] > 0){
			Master::error(COLLEGE_SEATE_IS_TAKEN);
		}

		//这个子嗣 已结婚
		$SonModel = Master::getSon($this->uid);
		if(empty($SonModel->info[$sid]) || $SonModel->info[$sid]['state'] != 9){//其他状态都是不能打工
			Master::error(GZJ_CHILD_NO_MARRY);
		}

		//判断这个孩子是否读过书
		$Act78Model = Master::getAct78($this->uid);
		$Act78Model->isRead($sid);

		//工位是否 超上限
		$Act75Model = Master::getAct75($this->uid);
		$Act75Model->click_id($id);

		//开始学习
		$this->info[$id] = array(
			'sid' => $sid,	//子嗣ID 0 没人
			'start' => Game::get_now(),//开始时间
			'over' => Game::get_over(15*24*3600),//下课时间
		);
		//添加子嗣个人信息
		$Act77Model = Master::getAct77($this->uid);
		$Act77Model->addSonInfo($SonModel->info[$sid]);

		$this->save();
	}
	/*
	 * 完成学业 毕业啦
	 * $params $id 座位号
	 */
	public function over_work($id){
		//这个打工位 有没有人
		if (empty($this->info[$id]['sid'])){
			Master::error(COLLEGE_SEATE_UN_TAKEN);
		}
		//完成学习的子嗣ID
		$sid = $this->info[$id]['sid'];

		//结算奖励
		$items = $this->clearReward($id);
		$back_items[] = array(//返回前端
			'sid' => $sid,
			'items' => $items
		);

		Master::add_item3($items);
		Master::back_data($this->uid,'gzj','reward',$back_items);

		//毕业了
		$this->info[$id] = array(
			'sid' => 0,	//子嗣ID 0 没人
			'over' => 0,//罢工时间
		);
		$this->save();
	}

	/**
	 * 一起毕业
	 */
	public function allover_work(){
		//5个位置
		$Act75Model = Master::getAct75($this->uid);
		if($Act75Model->info['desk'] < 5) {
			Master::error(GZJ_ONE_KEY_COMPLETION);
		}
		$now = Game::get_now();
		$back_items = array();
		foreach ($this->info as $id => $data){
			$sid = $data['sid'];

			if($data['over'] > $now || empty($sid)){//未毕业
				continue;
			}

			//奖励结算
			$items = $this->clearReward($id,true);

			if(empty($items)) continue;
			$back_items[] = array(//返回前端
				'sid' => $sid,
				'items' => $items
			);

			Master::add_item3($items);

			//罢工
			$this->info[$id] = array(
				'sid' => 0,	//子嗣ID 0 没人
				'over' => 0,//罢工时间
			);
		}
		$this->save();
		Master::back_data($this->uid,'gzj','reward',$back_items);
	}

	/**
	 * 结算奖励
	 * @param $id
	 * @param $is_check
	 */
	public function clearReward($id,$is_check = false){
		$sid = $this->info[$id]['sid'];
		$Act77Model = Master::getAct77($this->uid);//子嗣信息

		//判断是否还有奖励未领取
		if(!empty($Act77Model->info[$sid]['giftId']) && ($Act77Model->info[$sid]['lq']+24*3600 <= $this->info[$id]['over'])){
			if($is_check == true){
				return 0;
			}else{
				Master::error(GZJ_DAILY_REWARD_NO_RECEIVE);
			}
		}

		$e2 = $Act77Model->info[$sid]['e2'];//智力
		$popular = $Act77Model->info[$sid]['popular'];//声望
		if(Game::dis_over($this->info[$id]['over'])){//未毕业的话
			Master::error(GZJ_CHILD_NOT_GRADUATE);
		}
		$money = 15*24*($e2+$popular);//正常结束总银两

		//声望奖励
		$level = empty($Act77Model->info[$sid]['level']) ? 0 : $Act77Model->info[$sid]['level'];
		//移除孩子
		$Act77Model->del($this->info[$id]);
		$design_cfg = Game::getcfg('gzj_design');

		if(empty($design_cfg[$level]['rwd'])){
			$items = array(
				array('id'=> 2,'count'=> $money,'kind'=>1),//基础奖励
			);
		}else{
			$js_rwd = $design_cfg[$level]['rwd'];
			$items = $js_rwd;
			$items[] = array('id'=> 2,'count'=> $money,'kind'=>1);//基础奖励
		}
		return $items;
	}
}
