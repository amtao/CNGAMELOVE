<?php
//聊天
class ChatMod extends Base
{
	/*
	 * 公共频道聊天
	 */
	public function sev($params){
	    if ($this->isBan()) {
	        Master::error(IS_BAN);
        }
		$Act9000Model = Master::getAct9000($this->uid);
		$now = Game::get_now();
		if($Act9000Model->info['lastchatTime'] >0 && ($Act9000Model->info['lastchatTime'] + 5) >= $now){
			Master::error(CHAT_SPACE_TIMES_LIMIT);
		}
		$Act9000Model->setChatTime();
	    // $cache = Common::getMyMem();
	    // if($cache->add($this->uid.'_space_limit_chat_sev',1,5) === false){
	    //     Master::error(CHAT_SPACE_TIMES_LIMIT);
	    // }
	    $userModel = Master::getUser($this->uid);
	    $base_cfg = Game::get_peizhi('jy_level');//群号
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
		//聊天信息
		$msg = Game::strval($params,"msg");
		//禁言
		$Sev23Model = Master::getSev23();
		$bool = $Sev23Model->isBanTalk($this->uid);
		if(empty($bool)){
		    $Sev23Model->autoBanTalk($this->uid,$msg);//自动禁言
		}

		//聊天上传
		$this->_chatload($userModel->info,$msg,1);

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
        if(empty($switch['disable_filter'])){
            $msg = Game::str_mingan($msg,1);
        }

		//敏感词汇
		$Sev28Model = Master::getSev28();
		if($Sev28Model->isSensitify($msg) === false){//不存在敏感字符
		    if(empty($Sev23Model->info[$this->uid])){//正常
    		    $Sev22Model = Master::getSev22();
    		    $Sev22Model->add_msg($this->uid,$msg);
    		}else{
    		    Master::back_s(2);
    		}
		}

        $type = Game::strval($params,"type");
        if ($type == 1){
            $id = Game::getcfg_param("speaker_itemid");
            Master::sub_item($this->uid, KIND_ITEM, $id, 1);
            $Sev6013Model = Master::getSev6013();
            $Sev6013Model->add_msg($this->uid, $msg);
        }

        $this->jilihua($msg);
	}

    private function jilihua($msg){
        Common::loadModel('HoutaiModel');
        $hd_cfg = HoutaiModel::get_huodong_info('huodong_6014');
        if (!empty($hd_cfg)){
            $Act6014Model = Master::getAct6014($this->uid);
            $Act6014Model -> get_rwd($msg);
        }
    }
	
	/*
	 * 公共频道聊天 : 历史消息
	 */
	public function sevhistory($params){
		//聊天信息
		$id = Game::intval($params,"id");
		
	    $Sev22Model = Master::getSev22();
	    $Sev22Model->list_history($this->uid,$id);
	}
	
	/*
	 * 跨服聊天
	 */
	public function kuafu($params){
        if ($this->isBan()) {
            Master::error(IS_BAN);
        }
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

		$this->_chatload($userModel->info,$msg,2);

		//广告判定
		$switch = Game::get_peizhi('gq_status');
		if(!empty($switch['advertise'])){
			//判断是否在白名单内
			$chat_white = Game::get_peizhi('chat_white');//聊天白名单
			if(empty($chat_white) || !in_array($this->uid,$chat_white)) {
				Common::loadModel("AdCheckModel");
				$AdCheckModel = new AdCheckModel($this->uid);
				if (!$AdCheckModel->click('sev', $msg)) {
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
		    //扣除喇叭
		    Master::sub_item($this->uid,1,270,1);
		    if(empty($Sev39Model->info[$this->uid])){//正常
		        $Sev25Model = Master::getSev25();
    		    $Sev25Model->add_msg($this->uid,$msg);
		    }else{
		        Master::back_s(2);
		    }

            //主线任务 ---  跨服发言	跨服聊天发X条消息
            $Act39Model = Master::getAct39($this->uid);
            $Act39Model->task_add(39, 1);

		}
	}
	
	/*
	 * 跨服频道 : 历史消息
	 */
	public function kuafuhistory($params){
		//聊天信息
		$id = Game::intval($params,"id");
       
        $Sev25Model = Master::getSev25();
		$Sev25Model->list_history($this->uid,$id);
	}

	/*
	 * 工会频道聊天
	 */
	public function club($params){
        if ($this->isBan()) {
            Master::error(IS_BAN);
        }
	    $cache = Common::getMyMem ();
	    if($cache->add($this->uid.'_space_limit_chat_club',1,5) === false){
	        Master::error(CHAT_SPACE_TIMES_LIMIT);
	    }
		//聊天信息
		$msg = Game::strval($params,"msg");
		$userModel = Master::getUser($this->uid);
		$this->_chatload($userModel->info,$msg,3);

		//广告判定
		$switch = Game::get_peizhi('gq_status');
		if(!empty($switch['advertise'])){
			//判断是否在白名单内
			$chat_white = Game::get_peizhi('chat_white');//聊天白名单
			if(empty($chat_white) || !in_array($this->uid,$chat_white)) {
				Common::loadModel("AdCheckModel");
				$AdCheckModel = new AdCheckModel($this->uid);
				if (!$AdCheckModel->click('sev', $msg)) {
					Master::error(STATUS_ERROR);
				}
			}
		}
		
		//是否禁言
		$Sev23Model = Master::getSev23();
		$Sev23Model->isBanTalk($this->uid);
		
		//敏感词汇
		$Sev28Model = Master::getSev28();
		if($Sev28Model->isSensitify($msg) === false){
		    //敏感字符判定
		    $msg = Game::str_feifa($msg,1);
		    $msg = Game::str_mingan($msg,1);
		    //联盟
		    $Act40Model = Master::getAct40($this->uid);
		    if($Act40Model->info['cid'] > 0){
		        $Sev24Model = Master::getSev24($Act40Model->info['cid']);
		        $Sev24Model->add_msg($this->uid,$msg);
		    }else{
		        Master::error(CLUB_NO_HAVE_JOIN);
		    }
		}

		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(161,1);
	}
	
	/*
	 * 工会频道聊天 : 历史消息
	 */
	public function clubhistory($params){
		//聊天信息
		$id = Game::intval($params,"id");
       
		//联盟
		$Act40Model = Master::getAct40($this->uid);
		if($Act40Model->info['cid'] > 0){
			$Sev24Model = Master::getSev24($Act40Model->info['cid']);
			$Sev24Model->list_history($this->uid,$id);
		}else{
			Master::error(CLUB_NO_HAVE_JOIN);
		}
	}
	/*
	 * 加入黑名单
	 * params buid 要拉黑名单的buid
	 * */
	public function addblacklist($params) {
	    $buid = Game::intval($params,"buid");
	    
	    $Act97Model = Master::getAct97($this->uid);
	    $Act97Model->add($buid);
	    
	    //好友两边都删除
	    $Act130Model = Master::getAct130($this->uid);
		$Act130Model->sub($buid);
		$Act130Model = Master::getAct130($buid);
		$Act130Model->sub($this->uid);
	}
	
	/*
	 * 移除黑名单
	 * params buid 移除黑名单的buid
	 * */
	public function subblacklist($params) {
	    $buid = Game::intval($params,"buid");
	     
	    $Act97Model = Master::getAct97($this->uid);
	    $Act97Model->sub($buid);
	}

	/**
	 * @param $userinfo
	 * @param $msg
	 * @param int $type 1:本服 2:跨服 3:工会聊天
	 */
	private function _chatload($userinfo,$msg,$type=1){
		if(!is_file(ROOT_DIR . '/public/pay_cfg/'.$userinfo['platform'].'.php') || (GAME_MARK != 'gjypaf')){
			return;
		}

		/*
		 * 去掉调试
		if(!Common::istestuser()){
			return;
		}

		if (!defined("MSDK_DEBUG")) {
		    //调试日志
		    define("MSDK_DEBUG", true);
        }
		*/

		Common::loadModel('OrderModel');
		$Api = OrderModel::sdk_login($userinfo['platform']);
		if(method_exists($Api,'putChat')){
			switch ($type){
				case 1:
					$channel = '本服聊天';
					break;
				case 2:
					$channel = '跨服聊天';
					break;
				case 3:
					$channel = '工会聊天';
					break;
				default:
					return;
					break;
			}
			$chat_params = array(
				'open_id' => Common::getOpenid($this->uid),
				'zone_id' => Game::get_sevid($this->uid),
				'zone_name' => Game::get_sevid($this->uid).'服',
				'role_id' => $this->uid,
				'role_level' => $userinfo['level'],
				'role_name' => $userinfo['name'],
				'to_role_id' => '',
				'to_role_name' => '',
				'channel' => $channel,
				'content' => $msg,
				'time' => Game::get_now(),
			);
			$Api->putChat($chat_params);
		}
	}


	public function isBan() {
	    return false;
        $banKey = 'AWY_BAN_USER_LIST';
        $uids = Common::getSharding($this->uid);
        $accname = $uids['ustr'];
        $redis = Common::getComRedis();
        $expireTime = $redis->hGet($banKey,$accname);
        if ($expireTime === '0') {
            return true;
        }

        $now = Game::get_now();

        if ($now > $expireTime) {
            return true;
        }

        return false;
	}
	
	// 客服聊天
	public function serviceChat($params) {

	    if ($this->isBan()) {
	        Master::error(IS_BAN);
        }

        //聊天信息
		$msg = Game::strval($params,"msg");
		$chatData = array(
            "info" => array(
				'uid' => $this->uid,
				'is_service' => 0,
				'content' => $msg,
				'send_time' => $_SERVER['REQUEST_TIME'],
				'is_read' => 0
			),
            "news" => 1
        );

		$sevid = Game::get_sevid($this->uid);
		$SevidCfg1 = Common::getSevidCfg($sevid);
	    $db = Common::getDbBySevId($sevid);

	    $sql = "insert into `service_chat_log` set `uid`='{$this->uid}', `content`='{$msg}', `send_time`='{$_SERVER['REQUEST_TIME']}'";
		$db->query($sql);

		//数据返回
        Master::back_data($this->uid, 'chat', "serviceChat", $chatData);
    }

    // 获取聊天历史信息
	public function serviceChatHistory() {

		$sevid = Game::get_sevid($this->uid);
		$SevidCfg1 = Common::getSevidCfg($sevid);
	    $db = Common::getDbBySevId($sevid);

	    $sql = "select * from `service_chat_log` where `uid`='{$this->uid}' order by id desc limit 50";
	    $data = $db->fetchArray($sql);
		if($data == false) $data = array();

		$news = 0;
		foreach ($data as $key => $value) {
			if ($value["is_service"] == 1 && $value["is_read"] == 0) {
				$news = 1;
			}
		}

		$returnData = array(
			'list' => $data,
			'news' => $news
		);

		//数据返回
        Master::back_data($this->uid, 'chat', "serviceChatHistory", $returnData);
    }

    // 读取聊天信息
	public function serviceChatRead() {

		$sevid = Game::get_sevid($this->uid);
		$SevidCfg1 = Common::getSevidCfg($sevid);
	    $db = Common::getDbBySevId($sevid);

	    $sql = "update `service_chat_log` set `is_read` = 1 where `uid`='{$this->uid}' AND `is_service` = 1";
	    $db->query($sql);

	    $returnData = array(
			'news' => 0
		);

		//数据返回
        Master::back_data($this->uid, 'chat', "serviceChatHistory", $returnData);
    }

    // 读取聊天信息
	public function clickAutomatic($params) {

		$cId = Game::intval($params,"cId");

		$sevid = Game::get_sevid($this->uid);
		$SevidCfg1 = Common::getSevidCfg($sevid);
	    $db = Common::getDbBySevId($sevid);

	    $sql = "insert into `service_chat_automatic` set `uid`='{$this->uid}', `cId`='{$cId}', `click_time`='{$_SERVER['REQUEST_TIME']}'";
	    $db->query($sql);
    }
}




