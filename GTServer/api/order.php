<?php
//使用道具
class orderMod extends Base
{
	
	/**
	 * 获取订单id
	 * @param $params
	 */
	public function getOrderId($params){
		//获取服务器ID
		$SevidCfg = Common::getSevidCfg();
		$Sevid = $SevidCfg['sevid'];
		
		$UserModel = Master::getUser($this->uid);
		
		//支付关闭
		Common::loadModel('OrderModel');
		OrderModel::close_order($UserModel->info['platform']);
		
		$data = array(
			'roleid' => $this->uid,
			'openid' => Common::getOpenid($this->uid),
			'platform' => $UserModel->info['platform'], // 平台渠道
			'ctime' => $_SERVER['REQUEST_TIME'],
			'status' => 0,
		);

		$payflag = '';
		$platform = isset($params['platform']) && !empty($params['platform']) ? $params['platform'] : $UserModel->info['platform'];
		$snsfile_path = dirname( dirname( __FILE__ )) . '/public/pay_cfg/' . $platform . '.php';
		//切支付
		if(file_exists($snsfile_path)){
            require_once $snsfile_path;
            $is_open_origipay = defined('IS_OPEN_ORIGIPAY') ? IS_OPEN_ORIGIPAY : 0;
            if($is_open_origipay){
                $is_open_weixinpay = defined('IS_OPEN_WEIXINPAY') ? IS_OPEN_WEIXINPAY : 0;
                $is_open_alipay = defined('IS_OPEN_ALIPAY') ? IS_OPEN_ALIPAY : 0;
                $set_sdk_pay_num = defined('SET_SDK_PAY_NUM') ? SET_SDK_PAY_NUM : 0;
                $sns_set_money_min = defined('SNS_SET_MONEY_MIN') ? SNS_SET_MONEY_MIN : 0;
                $set_sdk_pay_time = defined('SET_SDK_PAY_TIME') ? SET_SDK_PAY_TIME : '';
                $ptime = empty($set_sdk_pay_time) ?  '' : ' and `ptime`>' . strtotime($set_sdk_pay_time);
                $moneystr = empty($sns_set_money_min) ?  '' : ' and `money`>=' . $sns_set_money_min;
                $db = Common::getDbBySevId($Sevid);
                $sql = "select count(`orderid`) as totalNum from `t_order` where `roleid`={$this->uid} and `status`>0 and `platform`='{$UserModel->info['platform']}' {$moneystr} {$ptime} ";
                $result = $db->fetchArray($sql);
                $totalNum = empty($result[0]['totalNum']) ? 0 : $result[0]['totalNum'];
                if($totalNum >= $set_sdk_pay_num){
                    $data['platform'] = defined('SNS_ORIGIPAY_OFFICIAL') ? SNS_ORIGIPAY_OFFICIAL : $UserModel->info['platform'];
                    //按减号分隔，第一个值为是否切原生支付(1是0否【默认第三方sdk支付】)，第二个值为微信支付(1开启0关闭)，第三个值为支付宝支付(1开启0关闭)；
                    $payflag = "{$is_open_origipay}-{$is_open_weixinpay}-{$is_open_alipay}";
                }
            }
        }

        $orderid = OrderModel::order_id($data);
        if(empty($orderid)){
            Master::error(ORDER_CREATE_ABNORMAL);
        }

		$info = array(
			'id' => $orderid, //订单id   整个游戏订单id唯一
			'servid' => $Sevid,  //服务器id
            'payflag' => $payflag,  //切支付标识
		);
		
		Master::back_data($this->uid,'order','rorder',$info);
	}
	
	
	/**
	 * 充值客户端回调请求=>用于同步信息
	 * @param $params
	 */
	public function orderBack($params){
//        $cache 	= Common::getMyMem();
//		$backInfo = $cache->get('order_back_'.$this->uid);
//		if(empty($backInfo)){
//			$backInfo = array('cs' => 30);
//		}
//		if($backInfo['cs'] > 1){
//			Master::back_s(2);
//			$backInfo['cs'] -= 1;
//			$cache->set('order_back_'.$this->uid,$backInfo);
//		}else{
			//用户基础信息
			$UserModel = Master::getUser($this->uid);
			$UserModel->getBase();
			//神迹福利
	        $Act65Model = Master::getAct65($this->uid);
	        $Act65Model->back_data();
			//首充福利
	        $Act66Model = Master::getAct66($this->uid);
			$Act66Model->back_data();
			//连续首冲福利
			$Act316Model = Master::getAct316($this->uid);
			$Act316Model->back_data();
	        //vip福利
	        $Act67Model = Master::getAct67($this->uid);
	        $Act67Model->back_data();
	        //年月卡
	        $Act68Model = Master::getAct68($this->uid);
	        $Act68Model->back_data();
	        //充值-充值档次
	        $Act70Model = Master::getAct70($this->uid);
	        $Act70Model->back_data();
            //活动列表
            $Act200Model = Master::getAct200($this->uid);
            $Act200Model -> back_data();
            //邮件
            $MailModel = Master::getMail($this->uid);
			$MailModel->getMails();
			
			//每日任务
            $Act35Model = Master::getAct35($this->uid);
            $Act35Model->back_data();

            //皇子累充解锁
            $Act6181Model = Master::getAct6181($this->uid);
			$Act6181Model->back_data_hd();
			
			//新人团购
			$Act7010Model = Master::getAct7010($this->uid);
			$Act7010Model->back_data_hd();

			//钱庄
			$Act702Model = Master::getAct702($this->uid);
			$Act702Model->back_data();

            //超值礼包
	        $Act6180Model = Master::getAct6180($this->uid);
			$Act6180Model->back_data();

			$Act261Model = Master::getAct261($this->uid);
			$Act261Model->back_data_hd();

			$Act8011Model = Master::getAct8011($this->uid);
			$Act8011Model->back_data_hd();

			$Act8016Model = Master::getAct8016($this->uid);
			$Act8016Model->back_data_hd();

			$Act750Model = Master::getAct750($this->uid);
			$Act750Model->exchangeOrderBack();

        	// 活动直购回调
            $HuodongModel = Master::getHuodong($this->uid);
            $HuodongModel->huodong_order_back();

			Master::back_s(1);

			Common::loadModel('OrderModel');
	        $money = OrderModel::getMyTotalPay($this->uid);
	        Master::back_data($this->uid,'fuli','money',array('totalMoney'=> $money));
//			$cache->delete('order_back_'.$this->uid);
//		}
	}
	
	
	/*
	 * 因为苹果支付成功回调前不与服务端通信，故出现错误很难查询，
	 * appstorefailorder
	 */
	public function AppFailCallback($params) {
		
		$params['type'] = empty($params['type'])?0:$params['type'];
		$params['cs1'] = empty($params['cs1'])?'':$params['cs1'];
		$params['cs2'] = empty($params['cs2'])?'':$params['cs2'];
		$params['cs3'] = empty($params['cs3'])?'':$params['cs3'];
		$params['cs4'] = empty($params['cs4'])?'':$params['cs4'];
		$params['cs5'] = empty($params['cs5'])?'':$params['cs5'];
		$params['cs6'] = empty($params['cs6'])?'':$params['cs6'];
		$params['cs7'] = empty($params['cs7'])?'':$params['cs7'];
		$params['cs8'] = empty($params['cs8'])?'':$params['cs8'];
		
		$db = Common::getComDb();
		$sql = "insert into `fail_order` 
			(`type`, `cs1`,`cs2`,`cs3`,`cs4`,`cs5`,`cs6`,`cs7`,`cs8`)
			values (
			'{$params['type']}', 
			'{$params['cs1']}', 
			'{$params['cs2']}', 
			'{$params['cs3']}', 
			'{$params['cs4']}', 
			'{$params['cs5']}', 
			'{$params['cs6']}', 
			'{$params['cs7']}', 
			'{$params['cs8']}'
			 )";
		$db->query($sql);
	}
	
	
	
}







