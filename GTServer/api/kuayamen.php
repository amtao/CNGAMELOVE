<?php
//宫殿模块
class KuayamenMod extends Base
{
    /*
     * 进入活动
     * */
    public function comehd(){
        
        $Act300Model = Master::getAct300($this->uid);
        $Act300Model->comehd();
    }

	/*
	 * 进入宫殿
	 */
	public function yamen(){
		$Act306Model = Master::getAct306($this->uid);
		$Act300Model = Master::getAct300($this->uid);
		if($Act306Model->info['state'] == 1 && $Act300Model->hd_state == 3){//有门票的才发信息 且 正式赛
		    //宫殿信息
		    if ($Act300Model->outf['state'] == 0){
				//尝试开战
		        $Act300Model->rand_qhid();
		    }
		    //发送宫殿初始信息
		    $Act300Model->back_data();
		    //发送战斗信息
		    $Act61Model = Master::getAct61($this->uid);
		    $Act61Model->back_data();
		    
		    $Act301Model = Master::getAct301($this->uid);
		    $Act301Model->back_data();
		    
		    //防守信息
		    $Act304Model = Master::getAct304($this->uid);
		    $Act304Model->back_data();
		    //仇人信息
		    $Act305Model = Master::getAct305($this->uid);
		    $Act305Model->back_data();
			//已出战列表  复仇挑战
			$Act303Model = Master::getAct303($this->uid);
			$Act303Model->back_data();
		}
		//刷新 20名日志表
		$Sev60Model = Master::getSev60($Act300Model->hd_cfg['info']['id']);
		$Sev60Model->list_click($this->uid);
		
		//结算阶段进入宫殿返回当前第一名信息
		if($Act300Model->hd_state == 4){
		    $Redis305Model = Master::getRedis305($Act300Model->hd_cfg['info']['id']);
		    $Redis305Model->back_data_first();
		}
	}
	
	
	/*
	 * 使用出使令
	 */
	public function chushi(){
		//宫殿信息
		$Act300Model = Master::getAct300($this->uid);
		if($Act300Model->hd_state !=3){
		    Master::error(KUAYAMEN_HD_END);
		}
		$Act300Model->is_play($this->uid,1);//是否有参赛资格
		//检查是否处于自动次数用完 使用出师令次数中
		$Act300Model->click_state(3);
		
		//使用出使令出战
		$Act300Model->use_chuzheng();
	}
	/*
	 * 使用挑战卡
	 */
	public function tiaozhan($params){
		//对方uid
		$fuid = Game::intval($params,'fuid');
        $id = Game::intval($params,'id');
        if($id >= 0){
            $Act308Model = Master::getAct308($this->uid);
            if($Act308Model->check($id)){
                Master::error(HANS_TIAOZHAN);
            }
        }
		//用户id是否在正常范围内
        $Act300Model = Master::getAct300($this->uid);
        if($Act300Model->hd_state !=3){
            Master::error(KUAYAMEN_HD_END);
        }
        $Act300Model->is_play($fuid);
        $Act300Model->is_play($this->uid,1);//是否有参赛资格
		//使用门客ID
		$hid = Game::intval($params,'hid');
		
		//扣除挑战卡
		Master::sub_item($this->uid,KIND_ITEM,125,1);
		
		$Act300Model->do_take_fight($fuid,$hid,3,$id);
	}
	
	/*
	 * 复仇
	 */
	public function fuchou($params){
		//对方uid
		$fuid = Game::intval($params,'fuid');
		
		//用户id是否在正常范围内
        $Act300Model = Master::getAct300($this->uid);
        if($Act300Model->hd_state !=3){
            Master::error(KUAYAMEN_HD_END);
        }
        $Act300Model->is_play($fuid);
        $Act300Model->is_play($this->uid,1);//是否有参赛资格
		//是否在我的复仇名单上
		$Act305Model = Master::getAct305($this->uid);
		if ($Act305Model->del($fuid)){
			Master::error(KUAYAMEN_WHEN_WILL_RETRIBUTE);
		}
		
		//使用门客ID
		$hid = Game::intval($params,'hid');
		
		//扣除挑战卡
		Master::sub_item($this->uid,KIND_ITEM,125,1);
		
		$Act300Model->do_take_fight($fuid,$hid,4);
	}
	
	/*
	 * 追杀
	 */
	public function zhuisha($params){
		//对方uid
		$fuid = Game::intval($params,'fuid');
		
		//使用门客ID
		$hid = Game::intval($params,'hid');
		
		//用户id是否在正常范围内
        $Act300Model = Master::getAct300($this->uid);

        if($Act300Model->hd_state !=3){
            Master::error(KUAYAMEN_HD_END);
        }
        $Act300Model->is_play($fuid);
        $Act300Model->is_play($this->uid,1);//是否有参赛资格
		//扣除追杀令
		Master::sub_item($this->uid,KIND_ITEM,131,1);
		
		$Act300Model->do_take_fight($fuid,$hid,5);
	}
	
	/*
	 * 宫殿追杀信息查询
	 */
	public function findzhuisha($params){
		//查询的玩家UID
		$fuid = Game::intval($params,'fuid');

		//合服范围内
		Game::CheckServerByUid($fuid);

		Master::click_uid($fuid);

		if ($fuid == $this->uid){
			Master::error(YAMUN_CHALLENGE_YOURSELF);
		}
		
		//用户id是否在正常范围内
		$Act300Model = Master::getAct300($this->uid);
		
		//玩家信息
		$fuser = Master::fuidData($fuid);
		
		//如果玩家未进榜也不可追杀
		$Redis306Model = Master::getRedis306($Act300Model->hd_cfg['info']['id']);
		//排名
		$rank = $Redis306Model->get_rank_id($fuid);
		
		//积分
		$score = intval($Redis306Model->zScore($fuid));
		
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
		Master::back_data($this->uid,'kuayamen','zhuisha',$data);
	}
	
	
	/*
	 * 批准进入战斗
	 */
	public function pizun(){
		
		//宫殿信息
		$Act300Model = Master::getAct300($this->uid);
		if($Act300Model->hd_state !=3){
		    Master::error(KUAYAMEN_HD_END);
		}
		//判断状态 是不是 门客请战中
		$Act300Model->click_state(2);
		
		$Act300Model->q2f();
	}
	
	/*
	 * 领取本服奖励
	 * */
	public function getSevRwd(){
	    $Act300Model = Master::getAct300($this->uid);
	    if($Act300Model->hd_state !=4){
	        Master::error(KUAYAMEN_NOT_TIME_TO_GET_REWARD);
	    }
	    $Act307Model = Master::getAct307($this->uid);
	    if($Act307Model->is_lingqu() === true){
	        Master::error(KUAYAMEN_HAVE_RECEIVED_REWARD);
	    }
	    $Act307Model->add();
	}
	
	/*
	 * 获取排行信息
	 * */
	public function getRank(){
	    $Act300Model = Master::getAct300($this->uid);
	    $redis306Model = Master::getRedis306($Act300Model->hd_cfg['info']['id']);
	    $redis306Model->back_data();
	    $redis306Model->back_data_my($this->uid);
	}
	
	/*
	 * 获取预选赛排名
	 * */
	public function getYxRank(){
	    //返回预选赛的排行信息  当前宫殿冲榜活动排行
	    $hd254Cfg = HoutaiModel::get_huodong_info('huodong_254');
	    $Redis105Model = Master::getRedis105($hd254Cfg['info']['id']);
	    $Redis105Model->back_data();
	    $Redis105Model->back_data_my($this->uid);
	}
	/*
	 * 获取个人排名和本服排名
	 * */
	public function getMyRank(){
	    //是否领取过单服礼包
	    $Act307Model = Master::getAct307($this->uid);
	    $Act307Model->back_data();
	    
	    //总排行
	    $Act300Model = Master::getAct300($this->uid);
        $redis305Model = Master::getRedis305($Act300Model->hd_cfg['info']['id']);
        $SevObj = Common::getSevCfgObj(Game::get_sevid($this->uid));
        $redis305Model->back_data_my($SevObj->getHE());
        //个人排行
        $redis306Model = Master::getRedis306($Act300Model->hd_cfg['info']['id']);
        $redis306Model->back_data_my($this->uid);
	}
	
	//战斗请求 --------------------- 
	
	/*
	 * 选人打
	 */ 
	public function fight($params){
		//选择敌方门客
		$id = Game::intval($params,'id');
		$Act300Model = Master::getAct300($this->uid);
		if($Act300Model->hd_state !=3){
		    Master::error(KUAYAMEN_HD_END.$Act300Model->hd_state);
		}
		
		//宫殿战斗类
		$Act301Model = Master::getAct301($this->uid);
		$Act301Model->select_fhid($id);
		
		//宫殿信息
// 		$Act300Model = Master::getAct300($this->uid);
		$Act300Model->back_data();
	}
	
	/*
	 * 选择加成
	 */ 
	public function seladd($params){
		//选择敌方门客
		$id = Game::intval($params,'id');
		
		//宫殿战斗类
		$Act301Model = Master::getAct301($this->uid);
		$Act301Model->select_add($id);
	}
	
	/*
	 * 抽奖
	 */ 
	public function getrwd(){
		$Act300Model = Master::getAct300($this->uid);
		if($Act300Model->hd_state !=3){
			Master::error(KUAYAMEN_HD_END);
		}
		//宫殿战斗类
		$Act301Model = Master::getAct301($this->uid);
		$Act301Model->rand_rwd();
	}
	
	
	/*
	 *宫殿日志历史信息
	 */
	public function yamenhistory($params){
		//聊天信息
		$id = Game::intval($params,"id");
		$Act300Model = Master::getAct300($this->uid);
        $Sev60Model = Master::getSev60($Act300Model->hd_cfg['info']['id']);
		$Sev60Model->list_history($this->uid,$id);
	}
	
	// --------------------------跨服聊天 ---------------------------------------------
	/*
	 * 跨服聊天
	 */
	public function kuafu($params){
	    //聊天信息
	    $cache = Common::getMyMem ();
	    if($cache->add($this->uid.'_space_limit_chat_kuafu',1,5) === false){
	        Master::error(CHAT_SPACE_TIMES_LIMIT);
	    }

		$userModel = Master::getUser($this->uid);
		$base_cfg = Game::get_peizhi('jy_level');//发言等级
		$level = 2;
		if(!empty($base_cfg)){
			if(!is_array($base_cfg[0])){
				$level = $base_cfg[0];
			}else {
				$SevCfg = Common::getSevidCfg();
				$level = 2;
				foreach ($base_cfg as $val) {
					if ($val['s'] <= $SevCfg['he'] && $val['e'] >= $SevCfg['he']) {
						$level = $val['l'];
						break;
					}
				}
			}
		}

		if($level > $userModel->info['level']){
			Master::error(CHAT_OPEN_LIMIT);
		}

	    $msg = Game::strval($params,"msg");
	    //禁言
	    $Sev39Model = Master::getSev39();
	    $bool = $Sev39Model->isBanTalk($this->uid);
	    if(empty($bool)){
	        $Sev39Model->autoBanTalk($this->uid,$msg);//自动禁言
	    }

		//广告判定
		$switch = Game::get_peizhi('gq_status');
		if(!empty($switch['advertise'])){
			//判断是否在白名单内
			$chat_white = Game::get_peizhi('chat_white');//聊天白名单
			if(empty($chat_white) || !in_array($this->uid,$chat_white)){
				Common::loadModel("AdCheckModel");
				$AdCheckModel = new AdCheckModel($this->uid);
				if (!$AdCheckModel->click('sev',$msg)){
					Master::error(STATUS_ERROR);
				}
			}
		}

	    //敏感字符判定
	    $msg = Game::str_feifa($msg,1);
	    $msg = Game::str_mingan($msg,1);

		//敏感词汇
	    $Sev28Model = Master::getSev28();
	    if($Sev28Model->isSensitify($msg) === false){
	        if(empty($Sev39Model->info[$this->uid])){//正常
	            $Sev62Model = Master::getSev62();
	            $Sev62Model->add_msg($this->uid,$msg);
	        }else{
	            Master::back_s(2);
	        }
	    }
	}
	
	/*
	 * 跨服频道 : 历史消息
	 */
	public function kuafuhistory($params){
	    //聊天信息
	    $id = Game::intval($params,"id");
	     
	    $Sev62Model = Master::getSev62();
	    $Sev62Model->list_history($this->uid,$id);
	}
}
