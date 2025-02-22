<?php
//衙门模块
class YamenMod extends Base
{
	/*
	 * 判断衙门战是否已开放
	 */
	private function is_open(){
		//判断 自己是不是在榜单上
		//加入衙门积分排行
		$Redis6Model = Master::getRedis6();
		$rank_id = $Redis6Model->get_rank_id($this->uid);


		if(empty($rank_id)){
            $TeamModel = Master::getTeam($this->uid);
            if ($TeamModel->info['herocount'] >= Game::getcfg_param("gongdou_unlock_servant")
                && $TeamModel->info['maxlv'] >= Game::getcfg_param("gongdou_unlock_level")){
                //加入衙门积分排行
                $Redis6Model = Master::getRedis6();
                $Redis6Model->join($this->uid);
                //刷新
                $Redis6Model->back_data();
            }
            else {
                Master::error(YAMUN_NOT_OPEN);
            }
		}
	}
	
	/*
	 * 进入衙门
	 */
	public function yamen($params){
		//判断开放
		$this->is_open();
		
		//衙门信息
		$Act60Model = Master::getAct60($this->uid);
		//如果CD到了
		if ($Act60Model->outf['state'] == 0){
			//尝试开战
			$Act60Model->rand_qhid();
		}

//		if ($this->uid== 11621){
//		    error_log(json_encode($Act60Model));
//        }
		//发送衙门初始信息
		$Act60Model->back_data();
		
		//发送战斗信息
		$Act61Model = Master::getAct61($this->uid);
		$Act61Model->back_data();
        //衙门战数据
        $Act7Model = Master::getAct7($this->uid);
        $Act7Model->back_data();
        //挑战出战表
        $Act8Model = Master::getAct8($this->uid);
        $Act8Model->back_data();
		
		//刷新 20名日志表
		$Sev6Model = Master::getSev6();
		$Sev6Model->list_click($this->uid);
	}

	public function clearCD(){
        $Act60Model = Master::getAct60($this->uid);
        $Act60Model->clearCD();
    }
	
	/*
	 * 刷新排行榜
	 */
	public function getrank($params){
		//我的衙门排名(衙门战开放标记)
        $Redis6Model = Master::getRedis6();
        $Redis6Model->back_data();
        $Redis6Model->back_data_my($this->uid);//我的排名
	}
	
	/*
	 * 使用出使令
	 */
	public function chushi($params){
		//判断开放
		$this->is_open();
		
		//衙门信息
		$Act60Model = Master::getAct60($this->uid);
		
		//检查是否处于自动次数用完 使用出师令次数中
		$Act60Model->click_state(3);
		
		//使用出使令出战
		$Act60Model->use_chuzheng();
	}
	
	/*
	 * 使用挑战卡
	 */
	public function tiaozhan($params){
		//对方uid
		$fuid = Game::intval($params,'fuid');
		$id = Game::intval($params,'id');
        if($id > 0){
            $Act64Model = Master::getAct64($this->uid);
            if($Act64Model->check($id)){
                Master::error(HANS_TIAOZHAN);
            }
        }

		//是否合服范围内
		Game::isHeServerUid($fuid);

		//使用门客ID
		$hid = Game::intval($params,'hid');

		$this->check_banish($hid);

		//扣除挑战卡
		Master::sub_item($this->uid,KIND_ITEM,Game::getcfg_param("gongdou_attack_id"),1);

		$this->_do_take_fight($fuid,$hid,3,$id);
	}

	/*
	 * 复仇
	 */
	public function fuchou($params){
		//对方uid
		$fuid = Game::intval($params,'fuid');
        //时间
        $time = Game::intval($params,'time');
		
		//是否合服范围内
		Game::isHeServerUid($fuid);
		
		//是否在我的复仇名单上
		$Act63Model = Master::getAct63($this->uid);
		if ($Act63Model->del($fuid,$time)){
			Master::error(YUAN_YUAN_XIAN_GBAO);
		}
		
		//使用门客ID
		$hid = Game::intval($params,'hid');
		
		//扣除挑战卡
		Master::sub_item($this->uid,KIND_ITEM,Game::getcfg_param("gongdou_attack_id"),1);
		
		$this->_do_take_fight($fuid,$hid,4);
	}
	
	/*
	 * 追杀
	 */
	public function zhuisha($params){
		//对方uid
		$fuid = Game::intval($params,'fuid');
		
		//是否合服范围内
		Game::isHeServerUid($fuid);
		
		//使用门客ID
		$hid = Game::intval($params,'hid');
		
		//扣除追杀令
		Master::sub_item($this->uid,KIND_ITEM,Game::getcfg_param("gongdou_zhuisha_id"),1);
		
		$this->_do_take_fight($fuid,$hid,5);

        //主线任务 ---  乘奔逐北	使用X次追杀令
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(41, 1);

	}
	
	/*
	 * 衙门追杀信息查询
	 */
	public function findzhuisha($params){
		//查询的玩家UID
		$fuid = Game::intval($params,'fuid');
		
		if ($fuid == $this->uid){
			Master::error(YAMUN_CHALLENGE_YOURSELF);
		}
		
		
		//是否合服范围内
		Game::isHeServerUid($fuid);
		
		
		//UID合法
		Master::click_uid($fuid);
		
		
		//玩家信息
		$fuser = Master::fuidData($fuid);
		
		//如果玩家未进榜也不可追杀
		$Redis6Model = Master::getRedis6();
		//排名
		$rank = $Redis6Model->get_rank_id($fuid);
		if(empty($rank)){
			Master::error(YAMUN_PLAYER_UNCOMMIT);
		}
		
		//积分
		$score = $Redis6Model->zScore($fuid);
		
		//对方门客
		$fHeroModel = Master::getHero($fuid);
		//门客数量
		$hnum = count($fHeroModel->info);
		
		$data = array(
			'fuser' => $fuser,
			'rank' => $rank,
			'score' => $score,
			'hnum' => $hnum,
		);
		
		//返回信息
		Master::back_data($this->uid,'yamen','zhuisha',$data);
	}
	
	/*
	 * 启动一场指定战斗
	 * 挑战,复仇,追杀
	 * 对方UID , 我使用的门客ID , 战斗类型
	 */
	private function _do_take_fight($fuid,$hid,$ftype,$node_id = 0){
		//判断开放
		$this->is_open();
		
		if ($fuid == $this->uid){
			Master::error(YAMUN_CHALLENGE_YOURSELF);
		}
		$this->check_banish($hid);
		//UID合法
		Master::click_uid($fuid);
		
		//门客是否可出战
		$Act8Model = Master::getAct8($this->uid);
		$Act8Model->go_fight($hid);
		
		//如果玩家未进榜也不可追杀
		$Redis6Model = Master::getRedis6();
		//排名
		$rank = $Redis6Model->get_rank_id($fuid);
		if(empty($rank)){
			Master::error(YAMUN_PLAYER_UNCOMMIT);
		}

        //衙门信息
        $Act60Model = Master::getAct60($this->uid);
        //判断状态 是不是 战斗中
        $Act60Model->isnot_fight();

		//进入指定战斗
		$Act61Model = Master::getAct61($this->uid);
		$Act61Model->start_fight($hid,$fuid,$ftype);
		if($ftype == 3){
		    $Act64Model = Master::getAct64($this->uid);
            $Act64Model->add($node_id);
		}
        $Act61Model->back_data();
        $Act60Model->back_data_refresh();

		//主线任务 挑战书使用次数
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(31,1);
		//主线任务 衙门出使次数
		$Act39Model->task_add(27,1);
		$Act39Model->task_refresh(31);

        //活动293 - 获得骰子-处理政务
        $Act293Model = Master::getAct293($this->uid);
        $Act293Model->get_touzi_task(7,1);

        //活动296 - 挖宝锄头-处理政务
        $Act296Model = Master::getAct296($this->uid);
        $Act296Model->get_chutou_task(7,1);

		
	}
	
	/*
	 * 批准进入战斗
	 */
	public function pizun($params){
		
		//衙门信息
		$Act60Model = Master::getAct60($this->uid);
		//判断状态 是不是 门客请战中
		$Act60Model->click_state(2);
		
		$Act60Model->q2f();
		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(27,1);

        //活动293 - 获得骰子-处理政务
        $Act293Model = Master::getAct293($this->uid);
        $Act293Model->get_touzi_task(7,1);

        //活动296 - 挖宝锄头-处理政务
        $Act296Model = Master::getAct296($this->uid);
        $Act296Model->get_chutou_task(7,1);

	}
	
	//战斗请求 --------------------- 
	
	/*
	 * 选人打
	 */ 
	public function fight($params){
		//选择敌方门客
		$id = Game::intval($params,'id');
		
		//衙门战斗类
		$Act61Model = Master::getAct61($this->uid);
		$Act61Model->select_fhid($id);
		
		//衙门信息
		$Act60Model = Master::getAct60($this->uid);
		$Act60Model->back_data();

        //舞狮大会 - 参与宫斗次数
        $Act6224Model = Master::getAct6224($this->uid);
		$Act6224Model->task_add(13,1);
		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(122,1);
		
	}
	
	/*
	 * 选择加成
	 */ 
	public function seladd($params){
		//选择敌方门客
		$id = Game::intval($params,'id');
		
		//衙门战斗类
		$Act61Model = Master::getAct61($this->uid);
		$Act61Model->select_add($id);
	}
	
	/*
	 * 抽奖
	 */ 
	public function getrwd($params){
		//衙门战斗类
		$Act61Model = Master::getAct61($this->uid);
		$Act61Model->rand_rwd();
	}
	
	
	/*
	 *衙门日志历史信息
	 */
	public function yamenhistory($params){
		//聊天信息
		$id = Game::intval($params,"id");
       
        $Sev6Model = Master::getSev6();
		$Sev6Model->list_history($this->uid,$id);
	}

	/**
	 * 检测门客是否被发配
	 * @param $hid
	 */
	public function check_banish($hid){
		$Act129Model = Master::getAct129($this->uid);
		$isbanish = $Act129Model->isBanish($hid);
		if($isbanish){
			Master::error(BANISH_010);
		}
	}

	public function getHistory(){
        //防守信息
        $Act62Model = Master::getAct62($this->uid);
        $Act62Model->back_data();
        //仇人信息
        $Act63Model = Master::getAct63($this->uid);
        $Act63Model->back_data();
    }

	/**
	 * 一键衙门
	 */
	public function oneKeyPlay(){
		//衙门冲榜
		$Act254Model = Master::getAct254($this->uid);
		$Act254Model->check_onkey();
		//帮会衙门冲榜
		$Act315Model = Master::getAct315($this->uid);
		$Act315Model->check_onkey();

		$Act61Model = Master::getAct61($this->uid);
		$Act61Model->oneKeyPlay();		
	}
	
	//兑换商城
	public function exchange($params){
		$id = Game::intval($params,"id");
		$exchangecfg = Game::getcfg_info('gongdou_exchange',$id);
		foreach($exchangecfg['cost'] as $v){
			Master::sub_item2($v);
        }
		Master::add_item3($exchangecfg['rwd']);
	}
	
}
