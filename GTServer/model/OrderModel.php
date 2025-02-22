<?php
require_once ROOT_DIR . '/config.php';
// 订单表
class OrderModel
{

	/**
	 * 获取订单id
	 * @param $data
	 */
	static public function order_id($data){
		
		$db = Common::getMyDb();
		
		$sql = "insert into `t_order` 
			(`openid`, `roleid`, `status`, `ctime`, `platform`)
			values (
			'{$data['openid']}', 
			'{$data['roleid']}', 
			'{$data['status']}',
			'{$data['ctime']}',
			'{$data['platform']}'
			 )";
		$db->query($sql);
		return $db->insertId();
	}


	/**
	 * 订单成功回调处理
	 * @param unknown_type $data
	 * $data['servid']:  服务器id
	 * $data['orderid']:  游戏唯一订单号
	 * $data['money']:   RMB
	 * $data['tradeno']:   订单流水号
	 */
	static public function order_success($data){
		
		Common::loadModel("Master");
		Game::order_debug('order_success:'.var_export($data, 1));
		$SevidCfg = Common::getSevidCfg($data['servid']);
		
		$orderInfo = array(); //订单返回信息
		$diamond = 0;   //充值钻石
		//获取平台配置
		$platform_cfg = Game::getcfg_info('order_platform',AGENT_CHANNEL_ALIAS);
		if(empty($platform_cfg)){
			Game::order_debug('获取平台配置失败');
			
			return false;
		}
		
		//如果没有配置平台
		if(empty($data['platform'])){
			$data['platform'] = 'android';  //安卓手机默认android
			if($data['paytype'] == 'appstore'){
				$data['platform'] = 'appstore';//苹果手机默认appstore
			}
		}
		
		//倍率  默认为10
		if(empty($platform_cfg[$data['platform']]['rate'])){
			$platform_cfg[$data['platform']]['rate'] = 10;
		}
		
	
		$shop_cfg = Game::getcfg_info('order_shop',1);
		if(GAME_MARK == "xianyu")
		{
			// $shop_cfg = Game::getcfg_info('order_shop_k',1);
			$shop_cfg = Master::getOrderShopCfg();
		}
		
		$packtype= 1;
		if(empty($shop_cfg)){
			
			$diamond = $data['money'] * $platform_cfg[$data['platform']]['rate'];
			 if($data['actcoin'] > 6480){
				$packtype =4;
			 }
		}else{
			foreach($shop_cfg as $k => $v){
				if($v['dc']== $data['payid'])
				{
					$diamond = $v['diamond'];
					$packtype = $v['type'];
					$data['money'] = $v['rmb'];//韩版计数改为rmb计数，保持任务统一
					break;
				}
			}
			if($diamond==0)
			{
				
				$diamond = $data['money'] * $platform_cfg[$data['platform']]['rate'];
				if($data['actcoin'] > 6480){
					$packtype =4;
				}
			}
		}
		
		if($packtype!=4)
		{
			if($diamond <= 0){
				Game::order_debug('钻石报错:'.$diamond.'packtype:'.$packtype);
				return false;
			}
		}
		Game::order_debug('更新数据验证服务器:'.$SevidCfg['sevid']);
		//更新数据库
		if($data['orderid'] <= 0){
			$orderData = array();//构造新订单数据
			switch($data['paytype']){
				case 'appstore':  //苹果充值
					$uids = Common::getUid($data['openid']);
					$uid = intval($uids['uid']);
					$orderData = array(
						'openid' => $data['openid'],   //手机标识
						'servid' => $data['servid'],   //服务器id
						'roleid' => $uid,   //玩家id
						'platform' => $data['platform'],   //平台
						'money' => $data['money'],    //充值金额
						'diamond' => $diamond,   //钻石
						'paytype' => $data['paytype'],  //充值类型
						'tradeno' => $data['tradeno'],  //sdk订单流水号
					);
					//验证相同订单
					if(!self::check_tradeno($data['tradeno'])){
						return false;
					}
					if(!self::check_oid($data['paytype'],$data['servid'],$data['tradeno'])){
						return false;
					}
					
					break;
				case 'houtai':  //后台直充
					$sevid = Game::get_sevid($data['roleid']);
					$SevidCfg = Common::getSevidCfg($sevid);
					$orderData = array(
						'openid' => Common::getOpenid($data['roleid']),   //手机标识
						'servid' => $sevid,   //服务器id
						'roleid' => $data['roleid'],   //玩家id
						'platform' => $data['platform'],   //平台
						'money' => $data['money'],    //充值金额
						'diamond' => $diamond,   //钻石
						'paytype' => 'houtai',  //充值类型
						'tradeno' => 'houtai_123456789',  //sdk订单流水号
					);
					break;
				case 'addOrder':  //后台直充
					$sevid = Game::get_sevid($data['roleid']);
					$SevidCfg = Common::getSevidCfg($sevid);
					$orderData = array(
						'openid' => Common::getOpenid($data['roleid']),   //手机标识
						'servid' => $sevid,   //服务器id
						'roleid' => $data['roleid'],   //玩家id
						'platform' => $data['platform'],   //平台
						'money' => $data['money'],    //充值金额
						'diamond' => $diamond,   //钻石
						'paytype' => 'addOrder',  //充值类型
						'tradeno' => 'addOrder_'.date('YmdHis'),  //sdk订单流水号
					);
					break;
				case 'guangwang':  //官网直充
				    $sevid = Game::get_sevid($data['roleid']);
				    $SevidCfg = Common::getSevidCfg($sevid);
				    $orderData = array(
					'openid' => Common::getOpenid($data['roleid']),   //手机标识
					'servid' => $sevid,   //服务器id
					'roleid' => $data['roleid'],   //玩家id
					'platform' => $data['platform'],   //平台
					'money' => $data['money'],    //充值金额
					'diamond' => $diamond,   //钻石
					'paytype' => $data['paytype'],  //充值类型
					'tradeno' => $data['tradeno'],  //sdk订单流水号
				    );
					//验证相同订单
				    if(!self::check_tradeno($data['tradeno'])){
					    return false;
				    }
				    break;
				default:
				    // 部分平台客户端支持充值界面多次付款的情况，每次需要新添订单避免掉单
				    if (isset($data['newOrder']) && !empty($data['newOrder'])) {
					// 没有设定角色id的时候
					if ( empty($data['roleid']) && !empty($data['openid']) ) {
					    $uids = Common::getUid($data['openid']);
					    $data['roleid'] = intval($uids['uid']);
					}
					// 没有设定平台帐号id的时候
					if ( empty($data['openid']) && !empty($data['roleid']) ) {
					    $uids = Common::getSharding($data['roleid']);
					    $data['openid'] = intval($uids['ustr']);
					}
					$fUserModel = Master::getUser($data['roleid']);
					$orderData = array(
						'openid' => $data['openid'],   //手机标识
						'servid' => $data['servid'],   //服务器id
						'roleid' => $data['roleid'],   //玩家id
						'platform' => $fUserModel->info['platform'],   //平台
						'money' => $data['money'],    //充值金额
						'diamond' => $diamond,   //钻石
						'paytype' => $data['paytype'],  //充值类型
						'tradeno' => $data['tradeno'],  //sdk订单流水号
					);
					//验证相同订单
					if(!self::check_tradeno($data['tradeno'])){
						return true;
					}
					break;
					}
				    return false;
			}
            Game::order_debug('order_insert');
            //if($data['actcoin'] > 6480){
			if($packtype==4){
                // 直购礼包传参报送
                $orderData['diamond'] = $data['actcoin'];
            }
            $orderInfo = self::order_insert($orderData);
            //咸鱼日志
            if($orderInfo != false){
                Common::loadModel('XianYuLogModel');
                XianYuLogModel::charge($orderData['platform'], $orderInfo['roleid'], $orderData['tradeno'], $orderData['money'], $orderData['tradeno'], $orderData['diamond'], '元宝');
            }
		}else{
			Game::order_debug('验证相同订单');
			//验证相同订单
			if(!self::check_tradeno($data['tradeno'])){
				return true;
			}
			Game::order_debug('order_update');
			$data['diamond'] = $diamond;


			$orderInfo = self::order_update($data);
            //咸鱼日志
            if($orderInfo != false){
                Common::loadModel('XianYuLogModel');
                XianYuLogModel::charge($data['platform'], $orderInfo['roleid'], $data['orderid'], $data['money'], $data['tradeno'], $data['diamond'], '元宝');
            }
		}
		Game::order_debug('更新数据验证服务器:'.$SevidCfg['sevid']);
		//更新玩家数据
		Game::order_debug('更新玩家数据:'.var_export($orderInfo, 1));
		
		if(false == $orderInfo){
		    Game::order_debug('订单信息更新失败');
		    return false;
		}


        //if($data['actcoin'] > 6480){
		if($packtype==4){
            // 直购礼包传参
            $orderInfo['diamond'] = $data['actcoin'];
        }

		//加用户锁
		Common::loadLockModel("MyLockModel");
		$LockModel = new MyLockModel("user_".$data['roleid']);
		$uid_Lock = $LockModel->getLock(3);
		
		//加钱
		$fUserModel = Master::getUser($orderInfo['roleid']);
		$rtdata = $fUserModel->add_cash_buy($orderInfo['diamond'],$orderInfo['money'],$packtype, $data['tradeno']);
		Master::click_destroy();
		
		//解用户锁
		if( null != $uid_Lock ){
			$LockModel->releaseLock();
		}
		
		Game::order_debug('更新玩家数据1:成功 0:失败:'.$rtdata);
		
		//Master::click_destroy();
		return true;
	}
	
	
/**
	 * 订单成功回调处理
	 * @param unknown_type $data
	 * $data['servid']:  服务器id
	 * $data['orderid']:  游戏唯一订单号
	 * $data['money']:   RMB
	 * $data['tradeno']:   订单流水号
	 */
	static public function order_success_fuli($data){
		Common::loadModel("Master");
		$sevid = Game::get_sevid($data['roleid']);
		$SevidCfg = Common::getSevidCfg($sevid);
		
		$orderInfo = array(); //订单返回信息
		$diamond = 0;   //充值钻石
		//获取平台配置
		$platform_cfg = Game::getcfg_info('order_platform',AGENT_CHANNEL_ALIAS);
		if(empty($platform_cfg)){
			Game::order_debug('获取平台配置失败');
			return false;
		}
		
		//如果没有配置平台
		if(empty($data['platform'])){
			$data['platform'] = 'android';  //安卓手机默认android
			if($data['paytype'] == 'appstore'){
				$data['platform'] = 'appstore';//苹果手机默认appstore
			}
		}
		
		//倍率  默认为10
		if(empty($platform_cfg[$data['platform']]['rate'])){
			$platform_cfg[$data['platform']]['rate'] = 10;
		}
		$diamond = $data['money'] * $platform_cfg[$data['platform']]['rate'];
		if($diamond <= 0){
			Game::order_debug('钻石报错:'.$diamond);
			return false;
		}
		
		//流水
		//Common::loadModel("FlowModel");
		//$cmd_FlowModel = new FlowModel($data['roleid'],'fuli',$data['orderid'],$data);
	    
		//加用户锁
		Common::loadLockModel("MyLockModel");
		$LockModel = new MyLockModel("user_".$data['roleid']);
		$uid_Lock = $LockModel->getLock(3);
		
		$fUserModel = Master::getUser($data['roleid']);
		$fUserModel->add_cash_buy_fuli($diamond,$data['money'], $data['tradeno']);
		
		Master::click_destroy();
		//$fUserModel->destroy();
		
		//解用户锁
		if( null != $uid_Lock ){
			$LockModel->releaseLock();
		}
		
		//流水写入
		//$cmd_FlowModel->destroy_now();
		
		
		//插入信息
		$orderInfo = array(
			'openid' => Common::getOpenid($data['roleid']),   //手机标识
			'roleid' => $data['roleid'],   //玩家id
			'status' => 0, 	 //标记交易已经完成
			'ctime' => $_SERVER['REQUEST_TIME'], //订单创建时间
			'platform' => $data['platform'],   //平台
			'money' => $data['money'],    //充值金额
			'diamond' => $diamond,   //钻石
			'ptime' => $_SERVER['REQUEST_TIME'],    //交易时间
			'paytype' => $data['paytype'],  //充值类型
			'tradeno' => $data['zhanghao'],  //sdk订单流水号
		);

		$sql = "insert into `t_order` 
		(`openid`, `roleid`, `status`, `ctime`, `platform`,
		`money`,`diamond`,`ptime`,`paytype`,`tradeno`)
		values (
		'{$orderInfo['openid']}', 
		'{$orderInfo['roleid']}', 
		'{$orderInfo['status']}',
		'{$orderInfo['ctime']}',
		'{$orderInfo['platform']}',
		'{$orderInfo['money']}',
		'{$orderInfo['diamond']}',
		'{$orderInfo['ptime']}',
		'{$orderInfo['paytype']}',
		'{$orderInfo['tradeno']}'
		 )";
		
		$db = Common::getMyDb();
		$db->query($sql);
		
		return true;
	}
	
	/**
	 * 验证是否存在相同订单
	 * @param unknown_type $platform
	 */
	static public function check_tradeno($tradeno){
		Game::order_debug('订单:'.$tradeno);
		
		$db = Common::getMyDb();
		$sql = "select * from `t_order` where `tradeno`='{$tradeno}'";
		if ( $db->fetchRow($sql)) {
			Game::order_debug('存在相同订单:'.$tradeno);
			return false;
		}
		Game::order_debug('成功订单:'.$tradeno);
		return true;
	}
	
	
	/**
	 * 主服订单验证
	 * @param $pt   平台
	 * @param $sid  服务器id
	 * @param $oid  订单id
	 */
	static public function check_oid($pt,$sid,$oid){
		Game::order_debug('主服订单:'.$oid);
		
		//却换到1区
		$db = Common::getComDb();
		$sql = "select * from `check_oid` where `oid`='{$oid}'";
		if ( $db->fetchRow($sql)) {
			Game::order_debug('存在相同主服订单:'.$oid);
			return false;
		}
		Game::order_debug('插入主服订单:'.$oid);
		$ins_sql = "insert into `check_oid` (`pt`,`sid`,`oid`) values ( '{$pt}','{$sid}','{$oid}' )";
		$db->query($ins_sql);
		
		return true;
	}
	
	/**
	 * 获取 登陆 验证配置
	 * @param unknown_type $platform
	 */
	static public function get_platform_cfg($platform){
		require_once ROOT_DIR . '/public/pay_cfg/'.$platform.'.php';
		if(SNS_LOGIN_CLOSE){
			Master::error('login closed');
		}
	}

	/**
	 * 获取 支付验证配置
	 * @param unknown_type $platform
	 */
	static public function close_order($platform){
		require_once ROOT_DIR . '/public/pay_cfg/'.$platform.'.php';
		if(SNS_PAY_CLOSE){
			if(SNS_PAY_CLOSE_MSG){
				Master::error(SNS_PAY_CLOSE_MSG);
			}
			Master::error('pay closed');
		}
	}
	
	// 获取sdk登陆验证的接口
	static public function sdk_login($platform) {
		self::get_platform_cfg($platform);
		// 修改：有设置登录方式采用特定平台登录的情况下加载对应的平台类
		if(defined('SNS_LOGIN_BASE') && file_exists(ROOT_DIR . '/controller/' . SNS_LOGIN_BASE . '/Api.php')){
		    require_once ROOT_DIR . '/controller/' . SNS_LOGIN_BASE . '/Api.php';
		}
		else{
		    require_once ROOT_DIR . '/controller/' . SNS_BASE . '/Api.php';
		}
		return  Api::getInstance();
	}
	
	// 获取sdk支付验证的接口
	static public function sdk_func($platform) {
		self::get_platform_cfg($platform);
		require_once ROOT_DIR . '/controller/'.SNS_BASE.'/Api.php';
		return  Api::getInstance();
	}

	/**
	 * 成功后 更新订单
	 * @param $data
	 * $data['servid']:  服务器id
	 * $data['orderid']: 我们的订单号(我们唯一)
	 * $data['money']:   RMB(或其他货币)
	 * $data['tradeno']: 平台订单号
	 */
	static public function order_update($data){
		//服务器ID
		$db = Common::getMyDb();

		$sql = "select * from `t_order` where `orderid`='{$data['orderid']}'";
		$orderInfo = $db->fetchRow($sql);
		if (empty($orderInfo) || '0' != $orderInfo['status']) {
		    Game::order_debug('更新之前的订单状态='.var_export($orderInfo, 1));
		    return false;
		}
		
		//更新订单
		$sql = "update `t_order` set 
		`money`='{$data['money']}',
		`diamond`='{$data['diamond']}',
		`status`= 1,
		`ptime`='{$_SERVER['REQUEST_TIME']}',
		`paytype`='{$data['paytype']}',
		`tradeno`='{$data['tradeno']}'
		where `orderid`='{$data['orderid']}'";

		if (!$db->query($sql)) {
		    return false;
		}

		//返回 逻辑使用
		return array(
			'roleid' => $orderInfo['roleid'],
			'money' => $data['money'],
			'diamond' => $data['diamond'],
		);

	}

	/**
	 * 成功后 更新订单
	 * @param $data
	 * $data['servid']:  服务器id
	 * $data['orderid']: 我们的订单号(我们唯一)
	 * $data['money']:   RMB(或其他货币)
	 * $data['tradeno']: 平台订单号
	 */
	static public function order_gift_bag($tradeno, $giftBagName){
		//服务器ID
		$db = Common::getMyDb();

		$sql = "select * from `t_order` where `tradeno`='{$tradeno}'";
		$orderInfo = $db->fetchRow($sql);
		if (empty($orderInfo)) {
		    Game::order_debug('更新之前的订单状态='.var_export($orderInfo, 1));
		    return false;
		}
		
		//更新订单
		$sql = "update `t_order` set `gift_bag`='{$giftBagName}' where `tradeno`='{$tradeno}'";
		if (!$db->query($sql)) {
		    return false;
		}

		//返回 逻辑使用
		return array(
			'roleid' => $orderInfo['roleid']
		);

	}

    /**
     * 通过订单号获取订单信息
     * @param $orderid
     * @return array
     */
    static public function getOrderInfoByOrderid($orderid){
        //服务器ID
        $db = Common::getMyDb();
        $sql = "select * from `t_order` where `orderid`='{$orderid}'";
        $orderInfo = $db->fetchRow($sql);
        //返回 逻辑使用
        return array(
            'roleid' => $orderInfo['roleid'],
            'money' => $orderInfo['money'],
            'diamond' => $orderInfo['diamond'],
        );
    }


	/**
	 * 如果下单时没有从服务器获取订单号    要重新生成一条订单
	 * @param $data
	 */
	static public function order_insert($data){
		
		//服务器ID
		$db = Common::getMyDb();
		
		$orderInfo = array(
			'openid' => $data['openid'],   //手机标识
			'roleid' => $data['roleid'],   //玩家id
			'status' => 1, 	 //标记交易已经完成
			'ctime' => $_SERVER['REQUEST_TIME'], //订单创建时间
			'platform' => $data['platform'],   //平台
			'money' => $data['money'],    //充值金额
			'diamond' => $data['diamond'],   //钻石
			'ptime' => $_SERVER['REQUEST_TIME'],    //交易时间
			'paytype' => $data['paytype'],  //充值类型
			'tradeno' => $data['tradeno'],  //sdk订单流水号
		);

		$sql = "insert into `t_order` 
		(`openid`, `roleid`, `status`, `ctime`, `platform`,
		`money`,`diamond`,`ptime`,`paytype`,`tradeno`)
		values (
		'{$orderInfo['openid']}', 
		'{$orderInfo['roleid']}', 
		'{$orderInfo['status']}',
		'{$orderInfo['ctime']}',
		'{$orderInfo['platform']}',
		'{$orderInfo['money']}',
		'{$orderInfo['diamond']}',
		'{$orderInfo['ptime']}',
		'{$orderInfo['paytype']}',
		'{$orderInfo['tradeno']}'
		 )";

		if (!$db->query($sql)) {
		    return false;
		}

		//返回 逻辑使用
		return array(
			'roleid' => $orderInfo['roleid'],
			'money' => $orderInfo['money'],
			'diamond' => $orderInfo['diamond'],
		);

	}

	/**
	 * 充值价格列表
	 * @param unknown_type $data
	 */
	static public function recharge_list($platform,$channel){

		$platform_cfg = Game::getcfg_info('order_platform',AGENT_CHANNEL_ALIAS);
		if(empty($platform_cfg)){
			return array();
		}
		//充值档次id  如果不存在配置渠道  就获取android档次
		if(empty($platform_cfg[$channel])){
			$channel = 'android';
		}
		$sid = $platform_cfg[$channel]['sid'];

        // if(GAME_MARK == "epzjfhovergatpgshf"){
        //     $shop_cfg = Game::getcfg_info('order_shop_gat',$sid);
        // }
        // else {
        //     $shop_cfg = Game::getcfg_info('order_shop',$sid);
		// }
		
		$moneyflag = "¥";
		if(defined('OVERSEAS')  && OVERSEAS){
			$moneyflag =  "$";
		}
		$shop_cfg = Master::getOrderShopCfg($sid);
		// if(GAME_MARK == "xianyu")
		// {
		// 	// $moneyflag = "₩";
			
		// 	if($channel == "appstore")
		// 	{
		// 		$shop_cfg = Game::getcfg_info('order_shop_kapp',$sid);
		// 	}else{
		// 		$shop_cfg = Game::getcfg_info('order_shop_k',$sid);
		// 	}
		// }
		if(empty($shop_cfg)){
			return array();
		}
		
        $platform_cny = array('gt_kt','xianyu','epzjfh', 'epqgym', 'epdevlocal', 'epgtmzpgshf', 'epgtmz', "epgtmzch");

		//所以档次是否设置为普通档
		$shenhe = Game::get_peizhi('shenhe');
		$list = array();
		foreach($shop_cfg as $k => $v){
                $type = empty($shenhe[$platform])?intval($v['type']):1;
                $type = empty($shenhe['open'])?$type:1;
                $v['mrmb'] = $v['rmb'];
                switch ($v['rmb'])
                {
                    case "6":
                        $v['mrmb'] = "$0.99";
                        break;
                    case "30":
                        $v['mrmb'] = "$4.99";
                        break;
                    case "68":
                        $v['mrmb'] = "$9.99";
                        break;
                    case "198":
                        $v['mrmb'] = "$29.99";
                        break;
                    case "328":
                        $v['mrmb'] = "$49.99";
                        break;
                    case "648":
                        $v['mrmb'] = "$99.99";
                        break;
                    case "648":
                        $v['mrmb'] = "$99.99";
                        break;

                    case "12":
                        $v['mrmb'] = "$1.99";
                        break;
                    case "18":
                        $v['mrmb'] = "$2.99";
                        break;
                    case "25":
                        $v['mrmb'] = "$3.99";
                        break;
                    case "40":
                        $v['mrmb'] = "$5.99";
                        break;
                    case "45":
                        $v['mrmb'] = "$6.99";
                        break;
                    case "50":
                        $v['mrmb'] = "$7.99";
                        break;



                    // -----------------------------------------
                    case "28":
                        $v['mrmb'] = "$4.99";
                        break;
                    case "288":
                        $v['mrmb'] = "$45.99";
                        break;
                }
				$moneyvalue = $v['rmb'];
				if(GAME_MARK == "xianyu")
				{
					$moneyvalue= $v['krw'];
				}
                $list[$v['rmb']] = array(
                    'dc' => intval($v['dc']),
                    'rmb' => in_array(GAME_MARK, $platform_cny) ? $moneyflag.$moneyvalue : $v['mrmb'],
                    'ormb' => $v['rmb'],
                    'diamond' => intval($v['diamond']),
					'type' => $type,  //1:普通  2;月卡  3:年卡 5:周卡 6:成长基金/钱庄
					'dollar'=> $v['dollar'],
					'krw'=> $v['krw'],
					'cpId'=> $v['cpId']
					
                );

        }
		return $list;
	}

	/**
	 * vip经验列表
	 * @param unknown_type $data
	 */
	static public function vipexp_list($platform,$channel){

		$platform_cfg = Game::getcfg_info('order_platform',AGENT_CHANNEL_ALIAS);
		if(empty($platform_cfg)){
			return array();
		}
		//充值档次id  如果不存在配置渠道  就获取android档次
		if(empty($platform_cfg[$channel])){
			$channel = 'android';
		}
		$vid = $platform_cfg[$channel]['vid'];

		// $vip_cfg = Game::getcfg_info('order_vip',$vid);
		$vip_cfg = Game::getcfg('vip');
		if(empty($vip_cfg)){
			return array();
		}

		return $vip_cfg;
	}

	/**
	 * 获取平台
	 */
	static public function get_platform(){

		$platform = array();
		
		$dir = ROOT_DIR . '/public/pay_cfg/';    //文件路径
		$file=scandir($dir);   //获取所有文件名称
		if(empty($file)){
			return array();
		}
		foreach($file as $name){
			//过滤不是 php文件
			if(!strpos($name, '.php')){
				continue;
			}
			
			$dir_file = $dir . $name;//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
		        continue;
		    }
			$info = include($dir_file);  //读取新配置
		    //获取key
			$key = str_replace('.php','',$name);
            if (!empty($info['channel'])){
                if( !in_array(AGENT_CHANNEL_ALIAS,$info['channel']) ){
                   // continue;
                }
                $base_hd[$key] = $info['name'];
            }

		}
		return $base_hd;

	}

    /**
     * 获取平台
     */
    static public function get_all_platform(){

        $platform = array();

        $dir = ROOT_DIR . '/public/pay_cfg/';    //文件路径
        $file=scandir($dir);   //获取所有文件名称
        if(empty($file)){
            return $platform;
        }
        foreach($file as $name){
            //过滤不是 php文件
            if(!strpos($name, '.php')){
                continue;
            }

            $dir_file = $dir . $name;//需要包含的文件
            //过滤不能读取
            if (!file_exists($dir_file)){
                continue;
            }
            $info = include($dir_file);  //读取新配置

            //获取key
            $key = str_replace('.php','',$name);
			if (!empty($info['channel'])){
            	$base_hd[$key] = $info['name'];
			}
        }
        return $base_hd;

    }

	/**
	 * 获取平台
	 */
	static public function get_platform_classify(){

		$platform = array();

		$dir = ROOT_DIR . '/public/pay_cfg/';    //文件路径
		$file=scandir($dir);   //获取所有文件名称
		if(empty($file)){
			return $platform;
		}
		foreach($file as $name){
			//过滤不是 php文件
			if(!strpos($name, '.php')){
				continue;
			}

			$dir_file = $dir . $name;//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
				continue;
			}
			$info = include($dir_file);  //读取新配置

			//获取key
			$key = str_replace('.php','',$name);
			if (!empty($info['channel'])) {
				$base_hd[$key] = $info['classify'];
			}
		}
		return $base_hd;

	}
	/**
	 * 获取平台
	 */
	static public function get_platform_info(){

		$platform = array();

		$dir = ROOT_DIR . '/public/pay_cfg/';    //文件路径
		$file=scandir($dir);   //获取所有文件名称
		if(empty($file)){
			return array();
		}
		foreach($file as $name){
			//过滤不是 php文件
			if(!strpos($name, '.php')){
				continue;
			}

			$dir_file = $dir . $name;//需要包含的文件
			//过滤不能读取
			if (!file_exists($dir_file)){
				continue;
			}
			$info = include($dir_file);  //读取新配置
			//获取key
			$key = str_replace('.php','',$name);
			if (!empty($info['channel'])){
				$base_hd[$key] = $info;
			}

		}
		return $base_hd;

	}

	/**
	 * 获取平台
	 */
	static public function get_one_platform_info($name){
		$dir_file = ROOT_DIR . '/public/pay_cfg/'.$name.'.php';    //文件路径
		$info = include($dir_file);
		return $info;

	}
	/**
	 * 获取订单信息
	 * @param unknown_type $data
	 * $data['servid']:  服务器id
	 * $data['orderid']:  游戏唯一订单号
	 * $data['money']:   RMB
	 * $data['tradeno']:   订单流水号
	 */
	static public function get_order_info($data){
		
		
		$SevidCfg = Common::getSevidCfg($data['servid']);
		$diamond = 0;   //充值钻石
		//获取平台配置
		$platform_cfg = Game::getcfg_info('order_platform',AGENT_CHANNEL_ALIAS);
		if(empty($platform_cfg)){
			return false;
		}
		
		//如果没有配置平台
		if(empty($data['platform'])){
			$data['platform'] = 'android';  //安卓手机默认android
			if($data['paytype'] == 'appstore'){
				$data['platform'] = 'appstore';//苹果手机默认appstore
			}
		}
		
		//倍率  默认为10
		if(empty($platform_cfg[$data['platform']]['rate'])){
			$platform_cfg[$data['platform']]['rate'] = 10;
		}
		$diamond = $data['money'] * $platform_cfg[$data['platform']]['rate'];
		if($diamond <= 0){
			return false;
		}
		return array(
			'diamond' => $diamond,
		);
		
	}

	//获取一段时间内的充值人数
	static public function getOrderCount($startTime,$endTime){
		$db = Common::getMyDb();

		$sql = "select count(distinct `roleid`) as peopleCount from `t_order` where `ctime`>='{$startTime}' and `ptime` <= '{$endTime}'";
		$order = $db->fetchRow($sql);
		return $order['peopleCount'];
	}

	//获取我的充值金额
	static public function getMyPay($startTime,$endTime,$uid){
		$db = Common::getMyDb();

		$sql = "select COALESCE(SUM(`money`),0) as money from `t_order` where `ctime`>='{$startTime}' and `ptime` <= '{$endTime}' and `roleid` = '{$uid}'";
		$order = $db->fetchRow($sql);
		return $order['money'];
	}

	//获取我的充值金额
	static public function getMyTotalPay($uid){
		$db = Common::getMyDb();

		$sql = "select COALESCE(SUM(`money`),0) as money from `t_order` where `roleid` = '{$uid}'";
		$order = $db->fetchRow($sql);
		return $order['money'];
	}
	
}







