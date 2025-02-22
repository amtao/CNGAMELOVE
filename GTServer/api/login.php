<?php

class loginMod
{
	/*
	 * 登陆
	 * //["uc","uc","sst1mobia7b765b8f7fe46109d99a0a86e36c2cb128728"]
	 * ["iToolsapk","2405160","2405160_kqfpaijbl1db15du92qif4hr11"]
	 */
	public function loginAccount($params)
	{
	    $num1 = '';
		$open_id = $params['openid'];
		$platform = $params['platform'];
		$openkey = $params['openkey'];
		
		if (empty($open_id)){
			Master::error('openid_null');
		}
		if (empty($platform)){
			Master::error('platform_null');
		}

		if($platform == 'anfenggjyp'){
            if(preg_match("/^49\d{6}$/", $open_id)){
                $paramslogin = file_get_contents('php://input');
                $logfile = LOG_PATH . 'anfenggjyp_newlogin.log';
                $log = sprintf('%s : %s', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), var_export($paramslogin, 1) . PHP_EOL);
                file_put_contents($logfile, $log, FILE_APPEND);
            }
        }

		//登陆验证
		Common::loadModel('OrderModel');
		$Api = OrderModel::sdk_login($platform);
		if ( !$Api->verifyToken($params) ) {
			Master::error(NEED_RELOGIN);
		}
		//是否获取第三方平台返回的用户id标识
        if(defined("IS_GET_PLATFORM_RETURN_ID") && IS_GET_PLATFORM_RETURN_ID){
            $num1 = $Api->getPlatformId();
            $open_id = $num1;
        }
		$shard = Common::getUidByOpenid($open_id);
		$white = Common::istestuser();
		if (empty($shard) && !$white) {
			$iplimit = Game::checkIPUser();
			if(!$iplimit){
				Master::error(SYSTEM_CHECK_USER);
			}
		}

		$uids = Common::getUid($open_id);//获取UID
		$uid = $uids['uid'];
		if (empty($shard) && !$white) {
			Game::addIPUser($uid);
		}


        //设备号
        if (!empty($params['parm4'])){
            Common::loadModel('DeviceModel');
            $is_exist = DeviceModel::is_exist($uid, $params['platform'], $params['parm4']);
            if (!$is_exist){
                $param3 = $params['parm3']?$params['parm3']:'0';
                DeviceModel::add($uid, $params['platform'], $params['parm4'], $param3);
            }
        }

		// 记录登录日志,GM登陆的不计算在内
		Common::loadModel('DataAnalyzeModel');
		DataAnalyzeModel::loginRecord($uid, $platform);
		
		//生成token
		$token = Common::setToken($uid);
		if (empty($token)){
			Master::error(NOTE_LOGIN_TIME_MIN);
		}
		$bak_data = array(
			'uid' => $uid,
			'token' => $token,
			'backurl' => defined("SNS_BACK_URL")?SNS_BACK_URL:'',
            'num1' => $num1,
            'gamename' => defined("SNS_BACK_GAME_NAME") ? SNS_BACK_GAME_NAME : '',
		);
		Master::back_data(0,__CLASS__,__FUNCTION__,$bak_data);
	}

	public function fastLoginAccount($params) {
		$open_id = '';
		$platform = strtolower(trim($_REQUEST['platform']));
		try {
			Common::loadModel('PlatFormModel');
			PlatFormModel::loadPlatFormCfg($platform);
			$Api = PlatFormModel::getPlatFormApiInstance($platform);
			if ( !$Api->fastLogin($params) ) {
				return array(
				0 => 0,// 失败：0，成功：1
				1 => sprintf('%s(%s)', NOTE_SNS_VERIFY_TOKEN_FAIL, __LINE__),
				);
			}
			if (defined('EXSNS')){
				$open_id = EXSNS . '_' . $Api->_snsid;
			} else {
				$open_id = $platform . '_' . $Api->_snsid;
			}
		} catch (Exception $e) {
			return array(
			0 => 0,// 失败：0，成功：1
			1 => sprintf('%s(%s_%s)', NOTE_LOGIN_FAIL, $e->getMessage(), __LINE__),
			);
		}

		$uids = Common::getUid($open_id);//获取UID
		$uid = $uids['uid'];

		//返回用户ID信息
		return array(
		0 => 1,//登陆成功?
		1 => $uid,//uid
		2 => md5(time()),//token
		3 => $open_id,// 游戏账户标识，原厂取游戏平台标识，这里
		);
	}

	public function getNotice() {
		Common::loadModel('HoutaiModel');
        $guanq = Game::get_peizhi('gq_status');

        if(empty($guanq['isNotice'])){
            //旧版公告
            $Act33Model = Master::getAct33($this->uid);
            $Act33Model->getGG();
        }else{
            //新版公告
            $Sev90Model = Master::getSev90();
            $Sev90Model->out_back($this->uid,$guanq['isNotice']);
		}
		  //活动公告
		  $activity_note = Game::get_peizhi('activity_note');
		  Master::$bak_data['a']['notice']['activity'] = $activity_note;
	}
}