<?php 
class activityMod extends Base
{
	/**
	 * 
	 * 兑换激活码
	 * @param unknown_type $params
	 */
	public function getCdKeyGift($params)
	{
		//检查cdkey开关
		Game::check_cdkeyopen();
		
		$platform = strtolower(trim($params['0']));// 平台渠道
		$cdkey = strtolower(trim($params['1']));// cdkey
		$uid = intval($_REQUEST['uid']);// 角色ID
		$ustr = Common::getOpenid($uid);// openid
		
		if ( empty($cdkey) ) {
			return array(
				0 => 0,// 失败：0，成功：1
				1 => sprintf('%s(%s)', NOTE_API_ACTIVITY_GETCDKEYGIFT_1, __LINE__),
			);
		}
		if ( empty($platform) ) {
			return array(
				0 => 0,// 失败：0，成功：1
				1 => sprintf('%s(%s)', NOTE_API_ACTIVITY_DOSIGNCDKEY_2, __LINE__),
			);
		}
		
		// 角色信息验证
		Common::loadModel('UserModel');
		$userModel = new UserModel($uid);
		if ( empty($userModel->info['uid']) ) {
			return array(
				0 => 0,// 失败：0，成功：1
				1 => sprintf('%s(%s)', NOTE_PLAYER_NOT_EXIST, __LINE__),
			);
		}
		// 流水统计
		Common::loadModel('FlowModel');
		global $cmd_FlowModel;
		$cmd_FlowModel = new FlowModel($uid, __FUNCTION__, json_encode($params));
		
		// 异步请求到入口区登记兑换码，如果成功则获取兑换信息
		try {
			$result = $this->requestSignCdKey($uid, $platform, $cdkey, intval(SERVER_ID), $ustr);
			if ( 1 != $result['1']['0'] ) {
				return array(
					0 => 0,// 失败：0，成功：1
					1 => ($result['1']['1'] ? $result['1']['1'] : NOTE_API_ACTIVITY_GETCDKEYGIFT_2),
				);
			}
			$giftCfg = $result['1']['1'];
		} catch (Exception $e) {
			return array(
				0 => 0,// 失败：0，成功：1
				1 => sprintf('%s(%s)', $e->getMessage(), __LINE__),
			);
		}
		
		$return = array(
			'ug' => 0,// 钻石
			'coin' => 0,// 金币
			's1' => 0,// 精华
			'itemid' => 0,// 道具
			'count' => 0,// 道具数量
		);
		if ( is_array($giftCfg) ) {
			$ConfigData = Common::getConfig('bset/ConfigData');
			Common::loadModel('ItemModel');
			$itemModel = new ItemModel($uid);
			foreach ($giftCfg as $itemid => $itemnum) {
				if ( !empty($ConfigData['cfg_item'][$itemid]) ) {
					$itemnum = intval($itemnum);
					switch ($itemid) {
						case 1://金币
							$return['coin'] = $itemnum;
							break;
						case 2://钻石
							$return['ug'] = $itemnum;
							
							// TODO 删档内侧特殊处理, 返利同时增加vip等级
							if ( 'fvip' == $cdkey ) {
								
								$set_rebate = Common::get_setting('fanli_set_rebate');//设置返利倍数  默认两倍，设置0无效，默认两倍
								$set_card = Common::get_setting('fanli_set_card');//是否在设置返利时免费送月卡 0：否    1：是
								if(!empty($set_rebate)){
									$userModel->add_vip_pay(intval($itemnum/$set_rebate));// vip累计消费按照实际充值金额来增加
								}else{
									$userModel->add_vip_pay(intval($itemnum/2));// vip累计消费按照实际充值金额来增加
								}
								if(!empty($set_card)){
									$userModel->set_monthcard();//送月卡
								}
								
//								if ( defined('AGENT_CHANNEL_ALIAS') && in_array(AGENT_CHANNEL_ALIAS, array('XSGJ')) ) {
//									$userModel->add_vip_pay(intval($itemnum/3));// vip累计消费按照实际充值金额来增加
//								} else if (defined('AGENT_CHANNEL_ALIAS') && in_array(AGENT_CHANNEL_ALIAS, array('MSRZB'))) {
//								    $userModel->add_vip_pay(intval($itemnum/5));// vip累计消费按照实际充值金额来增加
//								} else {
//									$userModel->add_vip_pay(intval($itemnum/2));// vip累计消费按照实际充值金额来增加
//								}
//								$userModel->set_monthcard();//送月卡
							}
							
							break;
						case 3://精华
							$return['s1'] = $itemnum;
							break;
						default:// 目前前端暂时只支持兑换一种
							$return['itemid'] = $itemid;
							$return['count'] = $itemnum;
							break;
					}
					
				}
			}
			$itemModel->destroy();
			$userModel->destroy();
		}
		return array(
			0 => 1,// 失败：0，成功：1
			1 => $return,
		);
	}
	
	/**
	 * 登记cdkey
	 * @param unknown_type $uid：角色id
	 * @param unknown_type $platform：平台渠道
	 * @param unknown_type $cdkey：兑换码
	 * @param unknown_type $sid：服务器id
	 * @throws Exception
	 */
	public function requestSignCdKey($uid, $platform, $cdkey, $sid, $ustr)
	{
		$jsonParam = array(
			0 => $uid,
			1 => $platform,
			2 => $cdkey,
			3 => $sid,
			4 => $ustr,
		);
		$jsonParam = json_encode($jsonParam);
		$rs = Common::request(sprintf('http://%s/cmd.php', DOMAIN_HOST), array(
			'cmd' => 'g',
			'func' => 'doSignCdKey',
			'platform' => $platform,
			'ver' => '',
			'param' => $jsonParam,
		));
		if ( empty($rs) ) {
			throw new Exception(ERROR_EMPTY_RETURN, __LINE__);
		}
		$response = json_decode($rs, true);
		if ( empty($response) ) {
			throw new Exception(ERROR_JSONDECODE_FAIL, __LINE__);
		}
		return $response;
	}
	
	// 处理具体的兑换码登记
	public function doSignCdKey($params) {
		Common::loadModel('CDKeyModel');
		
		$uid = intval($params['0']);// 角色ID
		$platform = strtolower(trim($params['1']));// 平台渠道
		$cdkey = strtolower(trim($params['2']));// cdkey
		$sid = strtolower(trim($params['3']));// server id
		$ustr = trim($params['4']);// openid
		
		// TODO
		if ( 'fvip' == $cdkey ) {
			return $this->doSignCdKey2($params);
		}
		
		$cdkeyParam = array(
			'acode' => $cdkey,
			'sevid' => $sid,
			'platform' => $platform,
			'uid' => $uid,
			'ustr' => $ustr,
		);
		// 兑换码配置
		$cfgCdKey = CDKeyModel::loadConfig();
		if ( empty($cfgCdKey) ) {
			return array(
				0 => 0,// 失败：0，成功：1
				1 => sprintf('%s(%s)', NOTE_CONFIG_NOT_FOUND, __LINE__),
			);
		}
		$giftName = $giftType = '';
		$giftCfg = array();
		$addNew = false;// 是否需要插入新的记录
		if ( key_exists($cdkey, $cfgCdKey['common']) ) {
			// 通用兑换码的话按照一定格式组成新的cdkey便于插入库
			$cdkeyParam['acode'] = sprintf('%s-%s-%s', $cdkey, $uid, $sid);
			$cdkeyParam['state'] = CDKeyModel::STATE_INVALID;
			$cdkeyParam['type'] = $giftType = $cfgCdKey['common'][$cdkey]['type'];
			$cdkeyParam['comm'] = $giftName = $cfgCdKey['common'][$cdkey]['name'];
			// 查看是否已兑换过
			$cdkeyInfo = CDKeyModel::info($cdkeyParam['acode']);
			if ( !empty($cdkeyInfo) ) {
				return array(
					0 => 0,// 失败：0，成功：1
					1 => sprintf(NOTE_API_ACTIVITY_DOSIGNCDKEY_1, $giftName, __LINE__),
				);
			}
			$addNew = true;
			$giftCfg = $cfgCdKey['common'][$cdkey]['items'];
		} else {
			// 查看是否已兑换过
			$cdkeyInfo = CDKeyModel::info($cdkeyParam['acode']);
			if ( empty($cdkeyInfo) ) {
				return array(
					0 => 0,// 失败：0，成功：1
					1 => sprintf('%s(%s)', NOTE_API_ACTIVITY_DOSIGNCDKEY_2, __LINE__),
				);
			}
			if ( CDKeyModel::STATE_VALID != $cdkeyInfo['state'] ) {
				return array(
					0 => 0,// 失败：0，成功：1
					1 => sprintf('%s(%s)', NOTE_API_ACTIVITY_DOSIGNCDKEY_3, __LINE__),
				);
			}
			$giftType = $cdkeyInfo['type'];
			$giftName = $cfgCdKey['diy'][$giftType]['name'];
			
			// 根据兑换码类型统计是否已经领取过同类型礼包, 暂时默认每种礼包都只限制只能领取一次
			$maxNum = 1;
			if ( $maxNum <= CDKeyModel::countByType($giftType, $uid, $sid) ) {
				return array(
					0 => 0,// 失败：0，成功：1
					1 => sprintf(NOTE_API_ACTIVITY_DOSIGNCDKEY_1, $giftName, __LINE__),
				);
			}
			$giftCfg = $cfgCdKey['diy'][$giftType]['items'];
		}
		
		if ( empty($giftCfg) ) {
			return array(
				0 => 0,// 失败：0，成功：1
				1 => sprintf('%s(%s)', NOTE_API_ACTIVITY_GETCDKEYGIFT_2, __LINE__),
			);
		}
		
		// 更新记录
		if ( $addNew ) {
			$ret = CDKeyModel::add($cdkeyParam);
		} else {
			$ret = CDKeyModel::sign($cdkeyParam);
		}
		
		if ( $ret ) {
			return array(
				0 => 1,// 失败：0，成功：1
				1 => $giftCfg,
			);
		} else {
			return array(
				0 => 0,// 失败：0，成功：1
				1 => sprintf('%s(%s)', NOTE_API_ACTIVITY_GETCDKEYGIFT_2, __LINE__),
			);
		}
	}
	
	/**
	 * TODO
	 * 删档充值返利特殊处理
	 * 玩家在删档内侧期间的充值金额会按照一定比例返还
	 * @param unknown_type $params
	 */
	public function doSignCdKey2($params) {
		$uid = intval($params['0']);// 角色ID
		$sid = strtolower(trim($params['3']));// server id
		$ustr = trim($params['4']);// openid
		$now = strtotime('now');
		
		$db = Common::getMyDb();
		// 从t_order_statistics表查询玩家是否在删档内侧期间充值过
		$record = $db->fetchRow("select * from `t_order_statistics` where `openid`='{$ustr}'");
		if ( empty($record) ) {
			return array(
				0 => 0,// 失败：0，成功：1
				1 => sprintf('%s(%s)', NOTE_API_ACTIVITY_DOSIGNCDKEY2_1, __LINE__),
			);
		}
		
		// 判断是否已经领取过
		if ( 0 == $record['status'] ) {
			return array(
				0 => 0,// 失败：0，成功：1
				1 => sprintf('%s(%s)', NOTE_API_ACTIVITY_DOSIGNCDKEY2_2, __LINE__),
			);
		}
		
		// 获得总得充值金额，计算返利的钻石数
//		if ( defined('AGENT_CHANNEL_ALIAS') && in_array(AGENT_CHANNEL_ALIAS, array('XSGJ')) ) {
//			$idealmoney = intval($record['realmoney'] * 10 * 3);
//		} else if (defined('AGENT_CHANNEL_ALIAS') && in_array(AGENT_CHANNEL_ALIAS, array('MSRZB'))){
//		    $idealmoney = intval($record['realmoney'] * 10 * 5);
//		} else {
//			$idealmoney = intval($record['realmoney'] * 10 * 2);
//		}
		// 获得总得充值金额，计算返利的钻石数
		$idealmoney = intval($record['realmoney'] * 10 * 2);
		$set_rebate = Common::get_setting('fanli_set_rebate');//设置返利倍数  默认两倍，设置0无效，默认两倍
		if(!empty($set_rebate)){
			$idealmoney = intval($record['realmoney'] * 10 * $set_rebate);
		}
		
		// 标记当前记录已领取
		$ret = $db->query("update `t_order_statistics` 
			set `status`=0, `utime`='{$now}', `serverid`='{$sid}',
			`roleid`='{$uid}', `idealmoney`='{$idealmoney}'
			where `openid`='{$ustr}'");
		
		if ( $ret ) {
			return array(
				0 => 1,// 失败：0，成功：1
				1 => array(
			        2 => $idealmoney,
				),
			);
		} else {
			return array(
				0 => 0,// 失败：0，成功：1
				1 => sprintf('%s(%s)', NOTE_API_ACTIVITY_GETCDKEYGIFT_2, __LINE__),
			);
		}
	}
}