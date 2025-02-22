<?php 
class Zhichong {
	
	public $itemtype_conf = array (
		'1' => '钻石(普通)',
		'2' => '月卡',
		'3' => '钻石(大额)',
	);
	
	public function __construct(){
//		Common::loadModel('PlatFormModel');
//		$this->_limit = PlatFormModel::loadPlatFormConfigForQueryByAdmin($_SESSION['CURRENT_USER']);
		
	}
	
	public function index() {
		$this->order();
		//include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	
	public function order() {
		if ( empty($_SESSION['CURRENT_USER']) ) {
			echo "<script>alert('请先登录');window.history.go(-1);</script>";
			return;
		}
		if ( 1 <= $_REQUEST['step'] ) {
			// 验证角色
			$uid = intval($_REQUEST['uid']);
			$itemType = intval($_REQUEST['itemtype']);// 道具类型
			$isLargePay = (3 == $itemType) ? true : false;
			
			Common::loadModel('UserModel');
			$UserModel = new UserModel($uid);
			if ( empty($UserModel->info['uid']) ) {
				echo "<script>alert('用户不存在');location.href='?mod={$_REQUEST['mod']}&act={$_REQUEST['act']}';</script>";
				return;
			}
			$userinfo = $UserModel->info;
			$platform = empty($userinfo['platform']) ? 'local' : $userinfo['platform'];
			try {
				Common::loadModel('OrderModel');
				Common::loadModel('PlatFormModel');
				$ugPriceCfg = OrderModel::loadPlatformPriceCfg($platform, $itemType);
				$platformConfig = PlatFormModel::loadPlatFormConfig();// 获取平台配置
				if ( empty($platformConfig[$platform]) ) {
					echo "<script>alert('未知平台');location.href='?mod={$_REQUEST['mod']}&act={$_REQUEST['act']}';</script>";
					return;
				}
			} catch (Exception $e) {
				echo "<script>alert('{$e->getMessage()}');location.href='?mod={$_REQUEST['mod']}&act={$_REQUEST['act']}';</script>";
				return;
			}
			
			if ( 2 == $_REQUEST['step'] ) {
				$tradeno = trim($_REQUEST['tradeno']);//订单ID
				if ( empty($tradeno) ) {
					echo "<script>alert('订单凭证号不能为空');location.href='?mod={$_REQUEST['mod']}&act={$_REQUEST['act']}';</script>";
					return;
				}
				if ( $isLargePay ) {
					$orderMoney = $itemID = intval($_REQUEST['itemid']);// 道具类型
					$setting = CommonModel::getAllcfg('setting');
					if (!empty($setting['chargeLargeLeastMoney'])){
					    $chargeLargeLeastMoney = $setting['chargeLargeLeastMoney'];
					} else {
					    $chargeLargeLeastMoney = 1000;
					}
					if ( $chargeLargeLeastMoney > $orderMoney ) {
						echo "<script>alert('大额充值金额不能低于{$chargeLargeLeastMoney}');location.href='?mod={$_REQUEST['mod']}&act={$_REQUEST['act']}';</script>";
						return;
					}
				} else {
					$itemID = intval($_REQUEST['itemid']);// 道具类型
					if ( empty($ugPriceCfg[$itemID]) ) {
						echo "<script>alert('道具配置不存在');location.href='?mod={$_REQUEST['mod']}&act={$_REQUEST['act']}';</script>";
						return;
					}
					$orderMoney = round(floatval($ugPriceCfg[$itemID]['cash']), 2);
				}
				
				// 发货
				$errmsg = '';
				try {
					// 发放游戏币属于游戏内业务逻辑，抽离实现
					require_once API_DIR . '/order.php';
					$jsonParam = array(
						'orderid' => 0,// 游戏订单号
						'tradeno' => $tradeno,// 平台订单号
						'ordermoney' => $orderMoney,// 订单金额
						'itemid' => $itemID,// 道具id
						'paytype' => 'ZHICHONG',// 充值方式
					);
					ksort($jsonParam);
					$jsonParam['sign'] = md5(join('', $jsonParam) . orderMod::MD5_KEY, false);
					$jsonParam['extra'] = array(
						'roleid' => $uid,
						'itemType' => $itemType,
						'platform' => $platform,
						'time' => strtotime('now'),
					);// 透传参数
					$orderMod = new orderMod($uid);
					$result = $orderMod->dealTradeCode($jsonParam);
					if ( is_array($result) ) {
						// 后台添加操作流水
						OperateLogModel::flowAdmin(array(
							'uid' => $uid,
							'pre' => 'null',
							'cha' => json_encode($jsonParam),
							'next' => json_encode($result),
							'type' => 'zhichong',
							'optype' => $uid,
						));
						if (  1 == $result['0'] ) {
							echo "<script>alert('直充成功，请通知玩家及时查收');location.href='?mod={$_REQUEST['mod']}&act={$_REQUEST['act']}';</script>";
						}
					}
				} catch (Exception $e) {
					$errmsg = $e->getMessage();
				}
				echo "<script>alert('直充失败.{$errmsg}');location.href='?mod={$_REQUEST['mod']}&act={$_REQUEST['act']}';</script>";
				return;
			}
			include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'_confirm.php';
			return;
		}
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	
	public function manage(){
		$fieldNames = array(
			'orderid' => '游戏订单号',
			'tradeno' => '订单凭证号',
			'roleid' => '角色ID',
			'platform' => '平台标识',
			'itemtype' => '道具类型',
			'realmoney' => '充值金额(元)',
			'idealmoney' => '兑换游戏币',
			'ctime' => '创建时间',
			'ptime' => '支付时间',
		);
			// 如果限制用戶不能查詢直充
		if ( $this->_limit ['orderType'] ) {
			if ( in_array ( "FEIZHICHONG", $this->_limit ['orderType'] ) || in_array ( "feizhichong", $this->_limit ['orderType'] ) ) {
				// pass
			}
		} else {
			$pageConfig = array (
					'tableName' => 't_order',
					'sql' => 'select `orderid`, `tradeno`, `roleid`, `realmoney`, 
				`idealmoney`, `platform`, `itemtype`, `itemid`, 
				date_format(from_unixtime(`ctime`), \'%Y-%m-%d %H:%i:%s\') as `ctime`, 
				date_format(from_unixtime(`ptime`), \'%Y-%m-%d %H:%i:%s\') as `ptime` from `t_order`',
					'params' => $_REQUEST 
			); // 查询条件

			$condition = array ();
			$condition [] = "`status`>=1 and `paytype`='ZHICHONG'";
			if ( $_REQUEST ['keyword1'] ) {
				$_REQUEST ['keyword1'] = intval ( $_REQUEST ['keyword1'] );
				$condition [] = "`roleid`='{$_REQUEST['keyword1']}'";
			}
			$pageConfig ['condition'] = join ( ' and ', $condition );
			$db = Common::getMyDb();
			if($condi == ''){
				$sql = 'select * from t_order order by ctime desc';
			}else{
				$sql = 'select * from t_order where '.$condi.'order by ctime desc';
			}
			$data = $db->fetchArray($sql);
			
//			$pageManage = PageManage::getInstance ( $pageConfig );
//			$pageManage->setCurrentPage ( $_REQUEST ['pagenum'] );
//			$pageManage->setSql ( $pageManage->getSql () . ' order by `ctime` desc ' );
//			$dataList = $pageManage->returnDataArray ();
//			$pageTool = $pageManage->genBottomHtml ();
		}
		
		
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
}
