<?php
require_once "ActBaseModel.php";
/*
 * 衙门-个人信息
 */
class Act60Model extends ActBaseModel
{
	public $atype = 60;//活动编号
	
	public $comment = "衙门-个人信息";
	public $b_mol = "yamen";//返回信息 所在模块
	public $b_ctrl = "info";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		//衙门出战次数 每日重置
		'ftime' => 0,//下次出战时间
		'fitnum' => 0,//正常出战次数
		'chunum' => 0,//出使令出战次数
		'funum' => 0,//复仇次数
		'lkill' => 0,//连续击杀
        'dayCount' => 0,//今日购买次数
		
		'qhid' => 0,//申请出战英雄
		'fuid' => 0,//申请对战玩家
	);
	
	/*
	 * CD到了  正常出战
	 * 随机一个申请英雄开战
	 */
	public function rand_qhid(){
		if ($this->outf['state'] != 0){
			return 0;
		}
		//随机一个敌人
		$Redis6Model = Master::getRedis6();
		$fuid = $Redis6Model->rand_f_uid($this->uid);
		if ($fuid == 0){
			//没有能打的人
			return 0;
		}
		
		//随机一个英雄
		$heroid = $this->rand_hero();
		//如果无人能出战
		if ($heroid == 0){
			//无人能出战
			return 0;
		}
		
		//设置申请出战信息
		$this->info['fitnum'] += 1;//正常出战次数+1
		$this->info['qhid'] = $heroid;//申请出战英雄
		$this->info['fuid'] = $fuid;//申请对战玩家
		$this->save();
	}
	
	/*
	 * 批准一个申请英雄进入战斗
	 */
	public function q2f(){
		//是否请战中
		$this->click_state(2);
		
		//衙门战斗类
		$Act61Model = Master::getAct61($this->uid);
		$Act61Model->start_fight($this->info['qhid'],$this->info['fuid'],1);
		
		//去掉申请信息
		$this->info['qhid'] = 0;
		$this->info['fuid'] = 0;
		$this->save();
	}
	
	/*
	 * 使用出征令
	 */
	public function use_chuzheng(){
		//是否处于 等待使用出征令 状态
		$this->click_state(3);
		//阵法
		$Team = Master::get_team($this->uid);

		// $chumax = floor(count($Team['pkhero'])/4);
		$UserModel = Master::getUser($this->uid);
		$vip = $UserModel->info['vip'];
		$vipCfg = Game::getcfg_info('vip',$vip);
		$chumax = $vipCfg['gongdoutime'];
		if($chumax <= $this->info['chunum']){
			Master::error(OPERATE_TODAY_NO_CASE);
		}
		
		//随机一个敌人
		$Redis6Model = Master::getRedis6();
		$fuid = $Redis6Model->rand_f_uid($this->uid);
		if ($fuid == 0){
			Master::error(YAMUN_UNFUND_ENEMY);
			return;
		}
		
		//随机一个英雄
		$heroid = $this->rand_hero();
		//如果无人能出战
		if ($heroid == 0){
			//无人能出战
			Master::error(YAMUN_NO_PLAY_HERO);
			return;
		}
		
		//扣除道具
		Master::sub_item($this->uid,KIND_ITEM,Game::getcfg_param("gongdou_add_count_id"),1);
		
		//出证次数+1
		$this->info['chunum'] += 1;

		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(27,1);
		
		//设置英雄出征
		//衙门战斗类
		$Act61Model = Master::getAct61($this->uid);
		$Act61Model->start_fight($heroid,$fuid,2);

        //活动293 - 获得骰子-处理政务
        $Act293Model = Master::getAct293($this->uid);
        $Act293Model->get_touzi_task(7,1);

        //活动296 - 挖宝锄头-处理政务
        $Act296Model = Master::getAct296($this->uid);
        $Act296Model->get_chutou_task(7,1);

        $this->save();
	}
	
	/**
	 *  完成一次战斗
	 *  @param int $is_win 是否胜利0 失败 1 胜利(全歼)
	 *  @param int $kill_num 击杀数
	 *  @param int $ftype 战斗类型 战斗类型0 未开战 1自动出战(包括出师令)\2复仇\3追杀
	 * @param $fuid
	 */
	public function battle_complete($is_win,$kill_num,$ftype,$fuid){
		//如果还有自动次数 进入冷却
		if ($this->info['fitnum'] < 4 && $ftype == 1){
			$this->info['ftime'] = Game::get_over(3600);
		}

//		error_log($ftype."  ".$this->info['fitnum']."  ".$this->info['ftime']);
//		if ($ftype == 2 || $ftype == 3 && $this->info['fitnum'] < 4 && $this->info['ftime'] < Game::get_now()){
//            $this->outf['state'] = 0;
//		    $this->rand_qhid();
//        }

		//击杀 连杀次数
		if ($is_win){
			$this->info['lkill'] = $kill_num;
		}else{
			$this->info['lkill'] = 0;
		}

		//记录对这个用户连杀击杀次数
		if($kill_num >= 20){
			if(empty($this->info['dkill'][$fuid])){
				$this->info['dkill'][$fuid] = 0;
			}
			$this->info['dkill'][$fuid] += 1;
		}

		$this->save();
	}
	
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out()
	{
		//几个状态
		/**
		 * 非战斗中
		 * 0 冷却完毕 无人能出战(或者无敌人能打) cd = 0
		 * 1 自动出战冷却中
		 * 2 门客出战申请中 cd > 0
		 * 3 自动出战次数用尽 fitnum >= 4
		 * 4 出师令出战次数用尽  chunum >= chumax
		 * 
		 * 战斗中
		 * 11 自动出战战斗中
		 * 12 出征令战斗中
		 * 13 挑战战斗中
		 * 14 复仇战斗中
		 * 15 追杀战斗中
		 */
		
		//出战类
		$Act61Model = Master::getAct61($this->uid);
		
		//阵法
		$Team = Master::get_team($this->uid);
		//出师令 使用次数
		// $chumax = floor(count($Team['pkhero'])/4);
		$UserModel = Master::getUser($this->uid);
		$vip = $UserModel->info['vip'];
		$vipCfg = Game::getcfg_info('vip',$vip);
		$chumax = $vipCfg['gongdoutime'];
		
		//衙门状态
		$state = 0;
		$ftime = 0;
		$fuser = array();//对战玩家信息

        $hid = 0;

		// 0: 非战斗中  1: 自动出战 2: 出师令 3: 复仇 4: 追杀
		$fight_state = $Act61Model->get_state();
		if ($fight_state > 0){
			switch($fight_state){
				case 1://自动出战
					$state = 11;
					break;
				case 2://出师令
					$state = 12;
					break;
				case 3://挑战
					$state = 13;
					break;
				case 4://复仇
					$state = 14;
					break;
				case 5://追杀
					$state = 15;
					break;
				default://出错 异常
					Master::error("fight_state_err_".$fight_state);
					break;
			}
			$hid = $Act61Model->info['hid'];
	        $fuser = Master::fuidInfo($Act61Model->info['fuid']);
		}else{
			//非战斗中
			if($this->info['ftime'] > 0
			&& !Game::is_over($this->info['ftime'])){
				$ftime = $this->info['ftime'];
			}
			//$ftime
			//非战斗中 CD到了 再发起一波战斗?
			//-----------------
			
			if ($chumax > 0
			&& $this->info['chunum'] >= $chumax){
				//出师令次数用尽
				$state = 4;
			}elseif($this->info['qhid'] > 0){
				//门客出战申请中
				$state = 2;
				//获取申请中 对战玩家信息
				$fuser = Master::fuidInfo($this->info['fuid']);
			}elseif($this->info['fitnum'] >= 4){
				//自动次数用尽 / 显示出师令可使用次数
				$state = 3;
			}elseif($ftime > 0){//自动出战冷却中
				//自动出战冷却中
				$state = 1;
			}else{
				//0 冷却完毕 无人能出战
				$state = 0;
			}
		}
		
		/*
		 * //衙门出战次数 每日重置
		'ftime' => 0,//下次出战时间
		'fitnum' => 0,//正常出战次数
		'chunum' => 0,//出使令出战次数
		'funum' => 0,//复仇次数
		'lkill' => 0,//连续击杀
		
		'qhid' => 0,//申请出战英雄
		'fuid' => 0,//申请对战玩家
		 */
		$this->outf = array(
			'state' => $state,
			'cd' => array(
				'next' => $ftime,//冷却时间
				'label' => "yamen",
			),
			'dayCount' => empty($this->info['dayCount'])?0:$this->info['dayCount'],
			'fitnum' => 4 - $this->info['fitnum'],//剩余 自动出战次数
			'chunum' => $chumax < $this->info['chunum']? 0:$chumax - $this->info['chunum'],//出师令可使用次数
			'chumax' => $chumax,//出师令使用次数上限
			'qhid' => $this->info['qhid'] == 0?$hid:$this->info['qhid'],////申请出战英雄
			'fuser' => $fuser,//申请对战玩家
		);
	}
	
	/*
	 * 判断当前状态 是不是 需要的状态
	 */
	public function click_state($state){
		if ($this->outf['state'] == $state){
			return true;
		}
		switch ($this->outf['state']){
			case 0: $msg = KUAYAMEN_NO_ONE_TO_FIGNt; break;
			case 1: $msg = KUAYAMEN_AUTOMATIC_COMBAT_COOLING; break;
			case 2: $msg = KUAYAMEN_IN_HIS_QINGZHAN; break;
			case 3: $msg = KUAYAMEN_AUTOMATIC_WAR_NOTIME; break;
			case 4: $msg = KUAYAMEN_ENVOY_ORDER_NOTIME; break;
			
			case 11: $msg = KUAYAMEN_HIS_BATTLE; break;
			case 12: $msg = KUAYAMEN_IN_HIS_BATTLE; break;
			case 13: $msg = KUAYAMEN_HIS_CHALLENGE; break;
			case 14: $msg = KUAYAMEN_HIS_REVENGE; break;
			case 15: $msg = KUAYAMEN_HIS_HUNTING; break;
			
			default: $msg = STATUS_ERROR; break;
		}
		Master::error($msg);
	}
	
	/*
	 * 判断当前状态 是不是非战斗中
	 */
	public function isnot_fight(){
		if ($this->outf['state'] > 10){
			Master::error(YAMUN_HAVE_PLAYING_HERO);
		}
		return true;
	}
	
	
	/*
	 * 随机选择一个门客进行出战
	 */
	private function rand_hero(){
		$team = Master::get_team($this->uid);
		
		//衙门正常出战列表
		$Act7Model = Master::getAct7($this->uid);
		//已出战列表
		$dead = array_keys($Act7Model->info); 
		
		//剩余可出战列表
		$f_heros = array_diff($team['pkhero'],$dead);
		//扣除流放的人
		$Act129Model = Master::getAct129($this->uid);
		if(!empty($Act129Model->info['list'])){
			$f_heros = array_diff($f_heros,array_keys($Act129Model->info['list']));
		}

		if (empty($f_heros)){
		    $heroModel = Master::getHero($this->uid);
            $lv = Game::getcfg_param("gongdou_unlock_level");
		    foreach ($heroModel->info as $id => $hero){
		        if ($hero['level'] >= $lv && !in_array($id, $team['pkhero']) && !in_array($id, $dead)){
                    $Act7Model->go_fight($id);
		            return $id;
                }
            }
			return 0;//没有可以出战的英雄了
		}
		
		//随机一个英雄
		$heroid = $f_heros[array_rand($f_heros,1)];
		
		$Act7Model->go_fight($heroid);
		
		return $heroid;
	}

    /*
     * 返回活动信息
     */
    public function back_data_refresh(){
        $this->make_out();
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
    }

	/**
	 * 获取今日击杀该玩家次数
	 * @param $fuid
	 * @return int
	 */
	public function get_day_kill($fuid){
		return empty($this->info['dkill'][$fuid]) ? 0 : $this->info['dkill'][$fuid];
	}

	public function clearCD(){
        if ($this->info['fitnum'] >= 4)return;
        $c = empty($this->info['dayCount'])?0:$this->info['dayCount'];
        $cost = Game::getCfg_formula()->gongdou_cost($c);
        Master::sub_item($this->uid, KIND_ITEM, 1, $cost);
        $this->info['dayCount'] = $c + 1;
        $this->info['ftime'] = 0;
        $this->make_out();
        if ($this->outf['state'] == 0){
            //尝试开战
            $this->rand_qhid();
        }
		$this->save();
		
	}
}
















