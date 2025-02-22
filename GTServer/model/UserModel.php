<?php
//用户
require_once "AModel.php";
class UserModel extends AModel
{
    protected $_syn_w = true;
	//这个作为USER内部类 不独立?
	private $_team_key = "_team";//阵法缓存
	private $team = null;//阵法信息 内部变量 阵法类去掉

	public $_key = "_user";
	protected  $updateSetKey = array(
		'name','job','sex','level','vip','step',
		'bmap','smap','mkill',
		'baby_num',
		'cb_time',
		'clubid','mw_num','mw_day',
		'voice','music','loginday','lastlogin',
		'platform','channel_id','ip','xuanyan',
		'allJob',

	);
	protected $updateAddKey =  array(
		'exp','coin','food','army','dresscoin',
		'cash_sys','cash_buy','cash_use',
	);
	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = $this->_getCache();
		$this->info = $cache->get($this->getKey());
		if($this->info == false){
			$table = 'user_'.Common::computeTableId($this->uid);
			$sql = "select * from `{$table}` where `uid`='{$this->uid}'";
			$db = $this->_getDb();
			if (empty($db))
			{
				Master::error(USER_ACCOUNT_NO_EXIT);
				return false;
			}
			$this->info = $db->fetchRow($sql);
			if($this->info == false) {
				$this->info = array();
				return;
			}
			$this->info['name'] = stripslashes($this->info['name']);
			$this->_rfcash();
			$cache->set($this->getKey(),$this->info);
		}
	}

	/*
	 * 各个类 数据写入数据库
	 */
	public function click_destroy(){
		foreach($this->models as $mol){
			$Model = $mol.'Model';
			if(isset($this->$Model)
			&& $this->$Model->_update == true){
				$this->$Model->destroy();
			}
		}

		if ($this->_update == true){
			$this->destroy();
		}
		return;
	}

	/*
	 * 刷新钻石数量
	 */
	public function _rfcash(){
		$this->info['cash'] = $this->info['cash_sys'] + $this->info['cash_buy'] - $this->info['cash_use'];
	}


	/*
	 * 登陆处理
	 */
	public function good_morning(){
		//如果是新的一天登陆
		if (!Game::is_today($this->info['lastlogin'])){

            //活动88 -  用户回归奖励
            $Act88Model = Master::getAct88($this->uid);
            $Act88Model->do_login($this->info['lastlogin'],$this->info['regtime']);

			$u_update = array(
				'lastlogin' => $_SERVER['REQUEST_TIME'],
			);
			$this->update($u_update);

			/*
			 * 每日重置的项目  该业务本身没有 时间戳 使用这个时间作为时间
			 * 然后在这里重置次数
			 * 声望+
			 * 朝拜时间
			 * 免费转运次数
			 *
			 * 其他?
			 */

			//累计登陆天数增加
			$this->info['loginday'] ++ ;

			//活动消耗 - 累计登录天数
			$HuodongModel = Master::getHuodong($this->uid);
			$HuodongModel->xianshi_huodong('huodong208',1);

            //活动消耗 - 冬至累计登录天数
            $HuodongModel = Master::getHuodong($this->uid);
            $HuodongModel->xianshi_huodong('huodong6186',1);

			//成就 - 累计登录天数
			$Act39Model = Master::getAct39($this->uid);
			$Act39Model->task_add(124,1);

			//活动293 - 获得骰子-每日登陆
            $Act293Model = Master::getAct293($this->uid);
            $Act293Model->get_touzi_task(1,1);

            //活动296 - 挖宝锄头-每日任务
            $Act296Model = Master::getAct296($this->uid);
            $Act296Model->get_chutou_task(1,1);

		}
		//更新最后一次登陆时间
		$this->info['lastlogin'] = $_SERVER['REQUEST_TIME'];
	}

	/*
	 * 获取输出值
	 */
	public function getBase()
	{
	    $name = Game::filter_char($this->info['name']);
        $Act6185Model = Master::getAct6185($this->uid);
		$zhichong = empty($Act6185Model->info['total'])?0:$Act6185Model->info['total']*10;

		$Act8012Model = Master::getAct8012($this->uid);
		$leiji = empty($Act8012Model->info['total'])?0:intval($Act8012Model->info['total']);
		
		//计算次数信息
		$data = array(
			'uid' => $this->info['uid'],	//UID
			'name' => $name,	//名字
			'job' => $this->info['job'],	//头像ID
			'sex' => $this->info['sex'],	//性别
			'level' => $this->info['level'],//官品级
			'exp' => $this->info['exp'],	//政绩
			'vip' => $this->info['vip'],	//VIP
			'cashbuy' => $this->info['cash_buy'] + $zhichong + $leiji,	//充值钻石
			'step' => $this->info['step'],	//账号进度(1未取名)
			'guide' => $this->info['guide'],//引导步骤

			'cash' => $this->info['cash'],	//元宝数量
			'coin' => $this->info['coin'],	//金币
			'food' => $this->info['food'],	//粮草
			'army' => $this->info['army'],	//军队
			'dresscoin' => intval($this->info['dresscoin']),//装扮货币

			'bmap' => $this->info['bmap'],	//地图大关ID
			'smap' => intval($this->info['smap']),	//地图小关ID
			'mkill' => $this->info['mkill'],	//已经打掉的小兵数量/BOSS血量
			'xuanyan' => isset($this->info['xuanyan'])?$this->info['xuanyan']:"",	//宣言

			'voice' => 0,//声音开关
			'music' => 0,//音乐开关
			'regtime' =>  $this->info['regtime'],//创建角色时间

			'allJob' => $this->info['allJob'],
		);
		//返回PVB战斗列表
		//关卡副本出战信息
		$Act3Model = Master::getAct3($this->uid);
		$Act3Model->back_data();

		//中地图ID加入返回信息里面
		//$data['mmap'] = ceil(($data['smap']+1)/8);
        $smap_cfg = Game::getcfg_info('pve_smap',intval($data['smap']) + 1);
        $data['mmap'] = intval($smap_cfg['mmap']);


		//插入称号返回
		$Act25Model = Master::getAct25($this->uid);
		$data['chenghao'] = $Act25Model->outf['setid'];

		Master::back_data($this->uid,'user','user',$data);
		return true;
	}



	/*
	 * 创建用户
	 */
	public function newUser($profile)
	{
		$uid = $profile['uid'];

		//渠道信息
		$platform = $profile['platform'];
		$channel_id = $profile['channel_id'];
		$ip = Common::GetIP();
		if(empty($ip)) $ip = 0;

		$time = $_SERVER['REQUEST_TIME'];

		//获取初始化配置信息
		/*
		$vip_cfg = Game::getGameCfg('userbase');
		$vip_cfg = Game::getGameCfg('vip');
		*/

		$db = $this->_getDb();
		//用户表数据
		$table = 'user_'.Common::computeTableId($uid);
		$sql = <<<SQL
INSERT INTO `{$table}` set
	`uid` = '{$uid}',
	`name` = '',
	`level` = '0',
	`coin` = '20000',
	`food` = '20000',
	`army` = '20000',
	`dresscoin` = '0',
	`step` = '{$profile['step']}',
	`loginday` = 1,
	`lastlogin` = '0',
	`regtime` = '{$time}',
	`clothe` = '0',

	`platform` = '{$platform}',
	`channel_id` = '{$channel_id}',
    `xuanyan` = '',
	/* 初始化地图信息 */
	`bmap` = '1',
	`smap` = '0',
	`mkill` = '0',

	`ip` = '{$ip}'
SQL;

		if (!$db->query($sql)){
			Master::error(NOTE_SYSTEM_ERROR.'USERMODEL');
		}
		//清缓存重新生成
		$this->clear_mem();
		return $uid;
	}

	/*
	 * 删除缓存重新读取
	 */
	public function clear_mem()
	{
		$cache = $this->_getCache();
		$this->info = $cache->delete($this->getKey());
		$this->__construct($this->uid);
	}

	/*
	 * 扣除钻石 成功返回true ,不足返回false
	 * $is_click 是否单纯检查 不扣除
	 */
	public function sub_cash($num,$is_click = false,$is_dun = false,$is_off = 0){
		if ($this->info['cash'] < $num
		|| $num < 0
		|| empty($num)){
			if ($is_click){
				return false;
			}
			Master::error(RES_SHORT.'|'."1");
		}
		//如果只是检查的话
		if ($is_click){
			return true;
		}
		$u_update = array(
			'cash_use' => $num,
		);
		$this->update($u_update);

		//活动消耗钻石/元宝
		if(!$is_off){
			$HuodongModel = Master::getHuodong($this->uid);
			$HuodongModel->xianshi_huodong('cash',$num);

            //活动296 - 挖宝锄头-每日任务
            $Act296Model = Master::getAct296($this->uid);
            $Act296Model->get_chutou_task(9,$num);

            //国力庆典-元宝消耗
            $Act6201Model = Master::getAct6201($this->uid);
            $Act6201Model->add($num);

            //御花园
            // $Act6190Model = Master::getAct6190($this->uid);
            // $Act6190Model->addType(4, $num);
            // $Act6190Model->addType(2, $num);

            //咸鱼日志
            Common::loadModel('XianYuLogModel');
            XianYuLogModel::consume($this->info['platform'], $this->uid, $num, $this->info['cash'], 0, 0, '扣除元宝');
			XianYuLogModel::roleinfo($this->info['platform'], $this->uid, $this->info['regtime'], $this->info['name'], $this->info['lastlogin'], $this->info['level'], $this->info['cash_buy'], $this->info['cash']);
			
			Common::loadModel('XianYuNewLogModel');
			XianYuNewLogModel::InsertGameCharacterCurrencyDot($this->uid, "钻石", $num, 3, 2, $this->info['level']);
		}

		return true;
	}

	/*
	 * 添加钻石
	 */
	public function add_cash($num){
		$u_update = array(
			'cash_sys' => $num,
		);
		$this->update($u_update);

		
		Common::loadModel('XianYuNewLogModel');
		XianYuNewLogModel::InsertGameCharacterCurrencyDot($this->uid, "钻石", $num, 3, 1, $this->info['level']);
	}

	/*
	 * 添加元宝  充值
	 */
	public function add_cash_buy($num,$money,$_type,$tradeno){
        Common::loadModel('XianYuLogModel');
	    $oldVip = $this->info['vip']; //以前的vip等级
		$doller = $money;
		$num = intval($num);
		$money = intval($money);
		Game::order_debug('更新数据验证服务器$num:'.$num);
		if ($num <= 0){
			return 0;
		}
        //更新vip等级
        $vip = 0;
        //累计vip经验
		$Act6185Model = Master::getAct6185($this->uid);
		
		//累计直购礼包增加vip经验
		$Act8012Model = Master::getAct8012($this->uid);

		$cash_add = $money*10;
		$giftVip = 0;
		//韩版特殊处理
		if(GAME_MARK == "xianyu")
		{
			if($_type == 4)
			{
				$cash_add = 0;
				// 获取礼包详情
				$giftBag = Game::getGiftBagCfg();
				$temp= $num /10000;
	            if( $temp >= 200){
	            	$hid = intval($temp - 100);
	            }else{
	            	$hid = $temp % 100;
				}
				
				
	            foreach ($giftBag as $key => $value) {
					if($value["type"] == 1 && $value["id"] == $hid){
						$vipExp = $value["exp"];
						$Act8012Model->add($vipExp);
						$giftVip = $vipExp;
	            		break;
					}elseif ($value["id"] == $hid && isset($value["exp"])) {

	            		$vipExp = $value["exp"];
						$Act8012Model->add($vipExp);
						$giftVip = $vipExp;
	            		break;
	            	}
	            }

			}else{
				$cash_add = $num;
			}
		}
		$Act6180Model = Master::getAct6180($this->uid);
		$sz_item = $Act6180Model->resItem($hid);
		if(!empty($sz_item)){
			$Act8012Model->add($sz_item['diamondpresent']);
			$giftVip = $sz_item['diamondpresent'];
		}

		$cash_buy = $this->info['cash_buy'] + $cash_add + $Act6185Model->info['total']*10 + $Act8012Model->info['total'];
		Game::order_debug('更新数据验证服务器cash_add:'.$cash_add);
        Common::loadModel('OrderModel');
        $channel = $this->info['channel_id'];
        $platform = $this->info['platform'];
        $list = OrderModel::vipexp_list($platform,$channel);
        if(!empty($list)){
            foreach($list as $v){
                if($v['recharge'] <= $cash_buy){
                    $vip = $v['level'];
                }
            }
		}
		//购买年月卡
		$Act68Model = Master::getAct68($this->uid);
		$flag = $Act68Model->buy($money);

		//直充礼包处理
        //if ($num > 6480){
		if($_type ==4){
            // $temp= $num /10000;
            // if( $temp >= 200){
            // 	$hid = intval($temp - 100);
            // }else{
            // 	$hid = $temp % 100;
            // }
            $flag = 4;
        }
        //第一次充值 记录等级和任务id流水
		$isFirst = $this->isFirstConsume();
        $Act39Model = Master::getAct39($this->uid);
		if ($isFirst){
		    $flowData = json_encode(array('lv'=>$this->info['level'],'task'=>$Act39Model->info['id']));
            Game::cmd_consume_flow($this->uid,0, 'userflow', 1, $flowData);
            Game::flow_php_record($this->uid, 8, $this->info['level'], $num, '', $this->info['smap']);
        }
		
		//flag = 2 月卡 flag = 3 年卡 flag = 4 礼包 flag = 5 周卡 type = 6 成长基金 type = 7 贵人令
		
		if($_type == 6) {
			$u_update = array(
				'cash_sys' => $num,
			);
			$this->update($u_update);
        }elseif($flag == 4 || $_type == 7){
			//直充不送元宝 但要记录
			// $Act6185Model->add($money);
        }
        else {
			$u_update = array(
				'cash_buy' => $num,
			);
			$this->update($u_update);

			Common::loadModel('XianYuNewLogModel');
			XianYuNewLogModel::InsertGameCharacterCurrencyDot($this->uid, "钻石", $num, 1, 1, $this->info['level']);
        }

		$ctip = MAIL_RECHANGE_CONTENT_HEAD."|".$num."|".MAIL_RECHANGE_CONTENT_FOOT;
		$title = MAIL_RECHANGE;
		if($flag == 2 ){
			//月卡
			$title = MAIL_RECHANGE_YUEKA;
			$ctip = MAIL_RECHANGE_CONTENT_YUEKA."|".$num."|".MAIL_RECHANGE_CONTENT_FOOT;
            Master::sendMail($this->uid,$title,$ctip,0,array());
            XianYuLogModel::pay($this->info['platform'],$this->uid,$money,2,$isFirst);
			Game::flow_php_record($this->uid, 3, $num,1,MAIL_RECHANGE_YUEKA,0,date("Y-m-d",$this->info['regtime']));
		}elseif($flag == 3 ){
			//年卡
			$title = MAIL_RECHANGE_NIANKA;
			$ctip = MAIL_RECHANGE_CONTENT_NIANKA."|".$num."|".MAIL_RECHANGE_CONTENT_FOOT;
            Master::sendMail($this->uid,$title,$ctip,0,array());
            XianYuLogModel::pay($this->info['platform'],$this->uid,$money,3,$isFirst);
			Game::flow_php_record($this->uid, 3, $num,1,MAIL_RECHANGE_NIANKA,0,date("Y-m-d",$this->info['regtime']));
        }elseif($flag == 5 ){
			//周卡
			$title = MAIL_RECHANGE_ZHOUKA;
			$ctip = MAIL_RECHANGE_CONTENT_ZHOUKA."|".$num."|".MAIL_RECHANGE_CONTENT_FOOT;
            Master::sendMail($this->uid,$title,$ctip,0,array());
            XianYuLogModel::pay($this->info['platform'],$this->uid,$money,3,$isFirst);
			Game::flow_php_record($this->uid, 3, $num,1,MAIL_RECHANGE_ZHOUKA,0,date("Y-m-d",$this->info['regtime']));
        }elseif( $_type == 6 ){
			$Act702Model = Master::getAct702($this->uid);
			$flag = $Act702Model->buy($money);
				//成长基金--钱庄
			$title = MAIL_RECHANGE_BANK;
			$ctip = MAIL_RECHANGE_CONTENT_BANK;
			Master::sendMail($this->uid,$title,$ctip,0,array());
			XianYuLogModel::pay($this->info['platform'],$this->uid,$money,3,$isFirst);
			Game::flow_php_record($this->uid, 3, $num,1,MAIL_RECHANGE_BANK,0,date("Y-m-d",$this->info['regtime']));

		}elseif( $_type == 7 ){
			//进阶贵人令

			$Act8011Model = Master::getAct8011($this->uid);
			$Act8011Model->UpGrade();

			$Act8016Model = Master::getAct8016($this->uid);
			$Act8016Model->UpGrade();
		}
        else {  // 年卡月卡不增加vip
            if ($flag == 4){
                //超值礼包
                $Act6180Model = Master::getAct6180($this->uid);
                $zc_item = $Act6180Model->resItem($hid);

                if (!empty($zc_item)) {

                	$price = $zc_item['sign'].$zc_item['present'];
	                $Act6180Model->Buy($hid, $tradeno);
	                switch ($hid){
	                    case 99://舞狮大会
	                        $Act6224Model = Master::getAct6224($this->uid);
	                        $Act6224Model->buyone();
	                        break;
	                    default://超值礼包
	                        $title = LEVEL_GIFT_CHAO_ZHI_LI_BAO;
	                        $ctip = MAIL_RECHANGE_CONTENT_DIRECT."|".$price."|".MAIL_RECHANGE_CONTENT_DIRECT_FOOT;
	                        Master::sendMail($this->uid,$title,$ctip,1,$zc_item['items']);
	                }
                }else{

                	$giftBag = Game::getGiftBagCfg();
	                $actid = 0;
	                foreach ($giftBag as $key => $value) {
						if($value["type"] == 1 && $value["id"] == $hid){
							$actid = 750;
	                		$zc_item = $value;
							$Act750Model = Master::getAct750($this->uid);
							$Act750Model->buyGift($value["id"]);
						}elseif ($value["id"] == $hid && isset($value["actid"])) {
	                		$actid = intval($value["actid"]);
	                		$zc_item = $value;
	                		break;
	                	}
	                }

	                if ($actid > 0) {

	                	$ActModel = Master::getActZhiGou($actid, $this->uid);
				        $ActModel->exchangeItem($hid, $zc_item);
	                }
                }

                Game::flow_php_record($this->uid, 1, $hid, 1, $zc_item['name'], $money,date("Y-m-d",$this->info['regtime']));
                XianYuLogModel::pay($this->info['platform'], $this->uid, $money,4,$isFirst);
            }else{
                Master::sendMail($this->uid,$title,$ctip,0,array());
                Game::cmd_consume_flow($this->uid, $money*10, 'huodong', 1, 'hd6180Buy');//直充流水
                Game::flow_php_record($this->uid, 4, $num, 1,'',0,date("Y-m-d",$this->info['regtime']));
                XianYuLogModel::pay($this->info['platform'], $this->uid, $money,1,$isFirst);
            }
            //如果vip升级-跑马灯
            if($vip > $oldVip){
                $Sev91Model = Master::getSev91();
                $Sev91Model->add_msg(array(101,Game::filter_char($this->info['name']),$vip));
            }
		}
		
		//除了贵人令，成长基金 其它的都算在首充中
		if($_type != 6 && $_type != 7){
			$u_update = array(
				'vip'	=> $vip,
			);
			$this->update($u_update);
			//首充
			$Act66Model = Master::getAct66($this->uid);
			$Act66Model->do_save();
			//连续首冲(单笔充值)
			$Act316Model = Master::getAct316($this->uid);
			$Act316Model->do_save($num,$money,$_type,$giftVip);
		}
				
		Game::order_debug('reward flag'.$flag);
        if($flag != 2 && $flag != 3 && $flag != 4 && $flag != 5 && $_type != 6 && $_type != 7) {
            //额外翻倍奖励
            $Act72Model = Master::getAct72($this->uid);
            $beishu = $Act72Model->do_save($money,$num);
            if($beishu > 0 ){
                $items = array();
                $items[] = array(
                    'id' => 1,
                    'count' => $num * $beishu,
                );
                Master::sendMail($this->uid,MAIL_RECHANGE_EXTRA,MAIL_RECHANGE_EXTRA_CONTENT,1,$items);
            }

            //特殊翻倍奖励  (首充翻倍不参与加成)
            $sys_beishu = Game::pv_beishu('order');
            if($sys_beishu > 1){
                $sysCount = ceil($num * ($sys_beishu - 1));
                $sys_items = array();
                $sys_items[] = array(
                    'id' => 1,
                    'count' => $sysCount,
                );

                $sys_title = MAIL_RECHANGE_SYSTEM;
                $sys_content = MAIL_RECHANGE_SYSTEM_CONTENT_1."|".$num."|".MAIL_RECHANGE_SYSTEM_CONTENT_2;
                $s_guanKaRwd = Game::pv_beishu('guanKaRwd');
                if(!empty($s_guanKaRwd) ){
                    $sys_title = $s_guanKaRwd['title'];
                    $sys_content = sprintf($s_guanKaRwd['content'],$num);
                }
                Master::sendMail($this->uid,$sys_title,$sys_content,1,$sys_items);
            }

            //百服开服充值不断,福利礼包不停
            $Act152Model = Master::getAct152($this->uid);
            $Act152Model->add($num);
  
			
			//新人团购
			$Act7010Model = Master::getAct7010($this->uid);
			$Act7010Model->setPayMoney($num);


            //累计充值多少钻石
            $HuodongModel = Master::getHuodong($this->uid);
			$HuodongModel->order_diamond($cash_add, $num);
			Game::order_debug('更新数据验证服务器cash_add2:'.$cash_add);

            //日常任务
            // $Act35Model = Master::getAct35($this->uid);
            // $Act35Model->do_act(12,1);
            //主线任务
            $Act39Model = Master::getAct39($this->uid);
            $Act39Model->task_add(56,1);
            $Act39Model->task_refresh(56);
		}

		$properties = array(
            'get_reason' => 1,
            'recharge_id' => (string)$tradeno,
            'pay_money' => $doller,
	        'doller' => Master::returnDoller($doller),
            'pay_type' => $flag, // 1.充值，2.月卡，3.年卡，4.礼包，5.周卡，6.成长基金
            'pay_item_id' => $hid,
		);
		Master::taTrack($this->uid,"log_pay_recharge",$properties);

		//充值流水
        Game::cmd_other_flow($this->uid, 'chongzhi', 'cash_buy', 'cash_buy', 1, 1, $flag!=4?$num:$num+100000, $this->info['cash']);

        //咸鱼日志
        XianYuLogModel::output($this->info['platform'], $this->uid, $flag!=4?$num:$num+100000, $this->info['cash'], 1, '充值基础获得');
        XianYuLogModel::vipgrade($this->info['platform'], $this->uid, $vip);
        XianYuLogModel::roleinfo($this->info['platform'], $this->uid, $this->info['regtime'], $this->info['name'], $this->info['lastlogin'], $this->info['level'], $this->info['cash_buy'], $this->info['cash']);

		$cache = $this->_getCache();
        $cache->set('order_back_'.$this->uid,array('cs' => 2),120);
		return 1;
	}


   /*
   * 添加元宝  充值
   */
    public function add_cash_buy_fuli($num,$money,$tradeno){
        Common::loadModel('XianYuLogModel');
        $oldVip = $this->info['vip']; //以前的vip等级

        $num = intval($num);
        $money = intval($money);
        if ($num <= 0){
            return 0;
        }
        //更新vip等级
        $vip = 0;
        //累计vip经验
		$Act6185Model = Master::getAct6185($this->uid);

		$Act8012Model = Master::getAct8012($this->uid);
		$giftVip = 0;
		if(GAME_MARK == "xianyu")
		{
			if($_type == 4)
			{
				$cash_add = 0;
				// 获取礼包详情
				$giftBag = Game::getcfg('gift_bag');
				$temp= $num /10000;
	            if( $temp >= 200){
	            	$hid = intval($temp - 100);
	            }else{
	            	$hid = $temp % 100;
				}
				
				foreach ($giftBag as $key => $value) {
					if($value["type"] == 1 && $value["id"] == $hid){
						$vipExp = $value["exp"];
						$Act8012Model->add($vipExp);
						$giftVip = $vipExp;
	            		break;
					}elseif ($value["id"] == $hid && isset($value["exp"])) {

	            		$vipExp = $value["exp"];
						$Act8012Model->add($vipExp);
						$giftVip = $vipExp;
	            		break;
	            	}
	            }

			}else{
				$cash_add = $num;
			}
		}
		$Act6180Model = Master::getAct6180($this->uid);
		$sz_item = $Act6180Model->resItem($hid);
		if(!empty($sz_item)){
			$Act8012Model->add($sz_item['diamondpresent']);
			$giftVip = $sz_item['diamondpresent'];
		}
	
        $cash_buy = $this->info['cash_buy'] + $money*10 + $Act6185Model->info['total']*10  + $Act8012Model->info['total'];
        Common::loadModel('OrderModel');
        $channel = $this->info['channel_id'];
        $platform = $this->info['platform'];
        $list = OrderModel::vipexp_list($platform,$channel);
        if(!empty($list)){
            foreach($list as $v){
                if($v['recharge'] <= $cash_buy){
                    $vip = $v['level'];
                }
            }
		}
        //购买年月卡
        $Act68Model = Master::getAct68($this->uid);
        $flag = $Act68Model->buy($money);
        //直充礼包处理
        if ($num > 6480){
            $temp= $num /10000;
            $hid = $temp % 100;
            $flag = 4;
        }
        //第一次充值 记录等级和任务id流水
        $isFirst = $this->isFirstConsume();
        $Act39Model = Master::getAct39($this->uid);
        if ($isFirst){
            $flowData = json_encode(array('lv'=>$this->info['level'],'task'=>$Act39Model->info['id']));
            Game::cmd_consume_flow($this->uid,0, 'userflow', 1, $flowData);
		}
		
		//flag = 2 月卡 flag = 3 年卡 flag = 4 礼包 flag = 5 周卡 type = 6 成长基金 type = 7 贵人令

		if($_type == 6) {
			$u_update = array(
				'cash_sys' => $num,
			);
			$this->update($u_update);
        }elseif($flag == 4 || $_type == 7){
			//直充不送元宝 但要记录
			// $Act6185Model->add($money);
        }
        else {
			$u_update = array(
				'cash_buy' => $num,
			);
			$this->update($u_update);

			Common::loadModel('XianYuNewLogModel');
			XianYuNewLogModel::InsertGameCharacterCurrencyDot($this->uid, "钻石", $num, 1, 1, $this->info['level']);
        }

        $ctip = MAIL_RECHANGE_CONTENT_HEAD."|".$num."|".MAIL_RECHANGE_CONTENT_FOOT;
        $title = MAIL_RECHANGE;
        if($flag == 2 ){
            //月卡
            $title = MAIL_RECHANGE_YUEKA;
            $ctip = MAIL_RECHANGE_CONTENT_YUEKA."|".$num."|".MAIL_RECHANGE_CONTENT_FOOT;
            Master::sendMail($this->uid,$title,$ctip,0,array());
            XianYuLogModel::pay($this->info['platform'],$this->uid,$money,2,$isFirst);
        }elseif($flag == 3 ){
            //年卡
            $title = MAIL_RECHANGE_NIANKA;
            $ctip = MAIL_RECHANGE_CONTENT_NIANKA."|".$num."|".MAIL_RECHANGE_CONTENT_FOOT;
            Master::sendMail($this->uid,$title,$ctip,0,array());
            XianYuLogModel::pay($this->info['platform'],$this->uid,$money,3,$isFirst);
        }elseif($flag == 5 ){
			//周卡
			$title = MAIL_RECHANGE_ZHOUKA;
			$ctip = MAIL_RECHANGE_CONTENT_ZHOUKA."|".$num."|".MAIL_RECHANGE_CONTENT_FOOT;
            Master::sendMail($this->uid,$title,$ctip,0,array());
            XianYuLogModel::pay($this->info['platform'],$this->uid,$money,3,$isFirst);
			Game::flow_php_record($this->uid, 3, $num,1,MAIL_RECHANGE_ZHOUKA,0,date("Y-m-d",$this->info['regtime']));
        }elseif( $_type == 6 ){
			$Act702Model = Master::getAct702($this->uid);
			$flag = $Act702Model->buy($money);
				//成长基金--钱庄
			$title = MAIL_RECHANGE_BANK;
			$ctip = MAIL_RECHANGE_CONTENT_BANK;
			Master::sendMail($this->uid,$title,$ctip,0,array());
			XianYuLogModel::pay($this->info['platform'],$this->uid,$money,3,$isFirst);
			Game::flow_php_record($this->uid, 3, $num,1,MAIL_RECHANGE_BANK,0,date("Y-m-d",$this->info['regtime']));
		}elseif( $_type == 7 ){
			//进阶贵人令

			$Act8011Model = Master::getAct8011($this->uid);
			$Act8011Model->UpGrade();

			$Act8016Model = Master::getAct8016($this->uid);
			$Act8016Model->UpGrade();
		}
        else {  // 年卡月卡不增加vip
            if ($flag == 4){

            	//超值礼包
                $Act6180Model = Master::getAct6180($this->uid);
                $zc_item = $Act6180Model->resItem($hid);
                if (!empty($zc_item)) {

                	$price = $zc_item['sign'].$zc_item['present'];
	                $Act6180Model->Buy($hid, $tradeno);
	                $title = LEVEL_GIFT_CHAO_ZHI_LI_BAO;
                    $ctip = MAIL_RECHANGE_CONTENT_DIRECT."|".$price."|".MAIL_RECHANGE_CONTENT_DIRECT_FOOT;
                    Master::sendMail($this->uid,$title,$ctip,1,$zc_item['items']);
                }else{

                	$giftBag = Game::getGiftBagCfg();
	                $actid = 0;
	                foreach ($giftBag as $key => $value) {
						if($value["type"] == 1 && $value["id"] == $hid){
							$Act750Model = Master::getAct750($this->uid);
							$Act750Model->buyGift($value["id"]);
						}elseif ($value["id"] == $hid && isset($value["actid"])) {
	                		$actid = intval($value["actid"]);
	                		$zc_item = $value;
	                		break;
	                	}
	                }

	                if ($actid > 0) {

				        $itemInfo = $zc_item['items'][0];
				        Master::add_item($this->uid, $itemInfo['kind'], $itemInfo['id'], $itemInfo['count']);
				        OrderModel::order_gift_bag($tradeno, $itemInfo["name"]);
	                }
                }

                Game::cmd_consume_flow($this->uid, $money*10, 'huodong', 1, 'hd6180Buy');//直充流水
                XianYuLogModel::pay($this->info['platform'], $this->uid, $money,4,$isFirst);
            }else{
                Master::sendMail($this->uid,$title,$ctip,0,array());
                XianYuLogModel::pay($this->info['platform'], $this->uid, $money,1,$isFirst);
            }
            //如果vip升级-跑马灯
            if($vip > $oldVip){
                $Sev91Model = Master::getSev91();
                $Sev91Model->add_msg(array(101,Game::filter_char($this->info['name']),$vip));
            }
		}
		//除了贵人令，成长基金 其它的都算在首充中
		if($_type != 6 && $_type != 7){
			$u_update = array(
				'vip'	=> $vip,
			);
			$this->update($u_update);
			//首充
			$Act66Model = Master::getAct66($this->uid);
			$Act66Model->do_save();
			//连续首冲(单笔充值)
			$Act316Model = Master::getAct316($this->uid);
			$Act316Model->do_save($num,$money,$_type,$giftVip);
		}


        if($flag != 2 && $flag != 3 && $flag != 4 && $flag != 5 && $_type != 6 && $_type != 7) {
            //额外翻倍奖励
            $Act72Model = Master::getAct72($this->uid);
            $beishu = $Act72Model->do_save($money,$num);
            if($beishu > 0 ){
                $items = array();
                $items[] = array(
                    'id' => 1,
                    'count' => $num * $beishu,
                );
                Master::sendMail($this->uid,MAIL_RECHANGE_EXTRA,MAIL_RECHANGE_EXTRA_CONTENT,1,$items);
            }

            //特殊翻倍奖励  (首充翻倍不参与加成)
            $sys_beishu = Game::pv_beishu('order');
            if($sys_beishu > 1){
                $sysCount = ceil($num * ($sys_beishu - 1));
                $sys_items = array();
                $sys_items[] = array(
                    'id' => 1,
                    'count' => $sysCount,
                );

                $sys_title = MAIL_RECHANGE_SYSTEM;
                $sys_content = MAIL_RECHANGE_SYSTEM_CONTENT_1."|".$num."|".MAIL_RECHANGE_SYSTEM_CONTENT_2;
                $s_guanKaRwd = Game::pv_beishu('guanKaRwd');
                if(!empty($s_guanKaRwd) ){
                    $sys_title = $s_guanKaRwd['title'];
                    $sys_content = sprintf($s_guanKaRwd['content'],$num);
                }
                Master::sendMail($this->uid,$sys_title,$sys_content,1,$sys_items);
			}
			//百服开服充值不断,福利礼包不停
			$Act152Model = Master::getAct152($this->uid);
			$Act152Model->add($num);
	
			
			//新人团购
			$Act7010Model = Master::getAct7010($this->uid);
			$Act7010Model->setPayMoney($num);


			//累计充值多少钻石
			$HuodongModel = Master::getHuodong($this->uid);
			$HuodongModel->order_diamond($cash_add, $num);
			Game::order_debug('更新数据验证服务器cash_add2:'.$cash_add);

			//日常任务
			// $Act35Model = Master::getAct35($this->uid);
			// $Act35Model->do_act(12,1);
			//主线任务
			$Act39Model = Master::getAct39($this->uid);
			$Act39Model->task_add(56,1);
			$Act39Model->task_refresh(56);
		}

        //充值流水
        Game::cmd_other_flow($this->uid, 'chongzhi', 'cash_buy', 'cash_buy', 1, 1, $flag!=4?$num:$num+100000, $this->info['cash']);

        //咸鱼日志
        XianYuLogModel::output($this->info['platform'], $this->uid, $flag!=4?$num:$num+100000, $this->info['cash'], 1, '充值基础获得');
        XianYuLogModel::vipgrade($this->info['platform'], $this->uid, $vip);
        XianYuLogModel::roleinfo($this->info['platform'], $this->uid, $this->info['regtime'], $this->info['name'], $this->info['lastlogin'], $this->info['level'], $this->info['cash_buy'], $this->info['cash']);

        $cache = $this->_getCache();
        $cache->set('order_back_'.$this->uid,array('cs' => 2),120);
        return 1;
    }

	/*
	 * 扣除 某种东西
	 */
	public function sub_sth($type,$num,$is_click = false,$is_off = 0){
		if ($this->info[$type] < $num || $num < 0 || empty($num)){
			if ($is_click){
				return false;
			}
			$err_type = 0;
			switch ($type){
			    case 'cash':
                    $err_type = 1;
			        break;
			    case 'coin':
                    $err_type = 2;
			        break;
			    case 'food':
                    $err_type = 3;
			        break;
			    case 'army':
                    $err_type = 4;
			        break;
			    case 'exp':
                    $err_type = 5;
			        break;
			}
			Master::error($err_type != 0?(RES_SHORT.'|'.$err_type):ITEMS_NUMBER_SHORT);
		}else{
			if ($is_click){
				return true;
			}
			//如果在增加序列里面
			if ($type == "cash"){
				return $this->sub_cash($num,$is_click = false,$is_dun = false,$is_off);
			}elseif (in_array($type,$this->updateAddKey)){
				//限时活动
				if(in_array($type,array('coin','army'))){
					$HuodongModel = Master::getHuodong($this->uid);
					$HuodongModel->xianshi_huodong($type,$num);
				}

				//冲榜活动 - 银两冲榜
				if($type == 'coin'){
					$HuodongModel = Master::getHuodong($this->uid);
					$HuodongModel->chongbang_huodong('huodong255',$this->uid,$num);

                    //国力庆典
                    $Act6202Model = Master::getAct6202($this->uid);
                    $Act6202Model->add($num);
				}
				//冲榜活动 - 士兵冲榜
				if($type == 'army'){
					$HuodongModel = Master::getHuodong($this->uid);
					$HuodongModel->chongbang_huodong('huodong257',$this->uid,$num);

                    //国力庆典
                    $Act6204Model = Master::getAct6204($this->uid);
                    $Act6204Model->add($num);
				}
                //冲榜活动 - 粮食冲榜
                if($type == 'food'){
                    $HuodongModel = Master::getHuodong($this->uid);
                    $HuodongModel->chongbang_huodong('huodong259',$this->uid,$num);

                    //限时活动  限时粮食消耗
                    $HuodongModel = Master::getHuodong($this->uid);
                    $HuodongModel->xianshi_huodong('huodong226',$num);

                    //国力庆典
                    $Act6203Model = Master::getAct6203($this->uid);
                    $Act6203Model->add($num);
                }

				$num *= -1;

                //咸鱼日志
                $user_item = array('coin', 'army', 'food');
                if(in_array($type, $user_item)){
                    Common::loadModel('XianYuLogModel');
                    XianYuLogModel::item($this->uid, $type, $this->info[$type], $num, '扣除用户类型道具');
                }
			} else if (in_array($type,$this->updateSetKey)){
				$num = $this->info[$type] - $num;
			} else{
				Master::error('sub_sth_err_'.$type);
			}

			$u_update = array(
				$type => $num,
			);
			$this->update($u_update);
		}
		return true;
	}

	/*
	 * 检测资源是否足够
	 */
	public function check_sth($id,$num){
		$count = 0;
		switch ($id) {
			case 1:
				$count = $this->info['cash'];
				break;
			case 2:
				$count = $this->info['coin'];
				# code...
				break;
			case 3:
				$count = $this->info['food'];
				# code...
				break;
			case 4:
				$count = $this->info['army'];
				# code...
				break;
			case 5:
				$count = $this->info['exp'];
				# code...
				break;
			default:
				# code...
				break;
		}
		if($count < $num){
			return false;
		}
		return true;
	}

	/*
	 * 添加 银两 粮食 军队
	 */
	public function add_sth($type,$num){
		if ($type == 'cash'){
			return $this->add_cash($num);
		}
		//如果在增加序列里面
		if (in_array($type,$this->updateAddKey)){
			//
		} else if (in_array($type,$this->updateSetKey)){
			$num += $this->info[$type];
		} else{
			Master::error('add_sth_err_'.$type);
		}

		$u_update = array(
			$type => $num,
		);
		$this->update($u_update);
	}

	/*
	 * 根据ID扣除/检查 项目
	 * 成功返回true
	 * 如果没有此ID 返回false
	 */
	public function subitem($itemid,$num,$is_click = false){

		return false;
	}

	// 判断首冲状态
	// 已经冲过 返回 false
	//还没冲过  返回 true
	public function isFirstConsume() {
		//先用老方法判断从没冲过钱的人
		if ($this->info['cash_buy'] <= 0){
			return true;
		}

		//首冲重置?

		return false;
	}

	/////---------------活动相关函数----------------

	/*
	 * 升官
	 */
	public function shengguan(){
		//判断满级
		$next_level = $this->info['level']+1;
		Game::getCfg_info('guan',$next_level, DASUANMOUFAN);

		//经验够不够 / 是不是已经满级
		$guan_cfg_info = Game::getCfg_info('guan',$this->info['level']);
		$exp = $guan_cfg_info['need_exp'];

		if (!$this->sub_sth('exp',$exp,true)){
			Master::error(RES_SHORT.'|'."5");
		}
		$this->sub_sth('exp',$exp);
		//加上等级
		$this->add_sth('level',1);

        //咸鱼日志
        Common::loadModel('XianYuLogModel');
        XianYuLogModel::rolelevel($this->info['platform'], $this->uid, $next_level);
		XianYuLogModel::roleinfo($this->info['platform'], $this->uid, $this->info['regtime'], $this->info['name'], $this->info['lastlogin'], $this->info['level'], $this->info['cash_buy'], $this->info['cash']);
		
		
        Common::loadModel('XianYuNewLogModel');
		XianYuNewLogModel::InsertGameCharacterlevelDot($this->uid,$next_level);

		//if($this->info['level'] == 2){
		//    Master::add_item($this->uid,KIND_WIFE,1);
		//}
	}

	/*
	 * 更新
	 */
	public function update($data)
	{
		foreach ($data as $k => $v){
			if (in_array($k,$this->updateSetKey)){
				//数值被改变
				$this->info[$k] = $v;
			} else if (in_array($k,$this->updateAddKey)){
				//数值被增减
				if (isset($this->info[$k])){
					$this->info[$k] += $v;
				}else{
					$this->info[$k] = $v;
				}
			}
			//设置返回信息
			switch ($k){
				//这些数值被更新的话 返回原字段更新信息
				case 'name':	case 'job':	case 'sex':
				case 'level':	case 'vip':	case 'exp':
				case 'step':    case 'allJob':
				case 'bmap':	case 'smap':	case 'mkill':
				case 'coin':	case 'food':	case 'army':
				case 'dresscoin':
				case 'baby_num':
					Master::back_data($this->uid,'user','user',array($k => $this->info[$k]),true);
					if ($k == 'smap'){
						//$mmap = ceil(($this->info['smap']+1)/8);
                        $smap_cfg = Game::getcfg_info('pve_smap',intval($this->info['smap']) + 1);
                        $mmap = intval($smap_cfg['mmap']);

						Master::back_data($this->uid,'user','user',array('mmap' => $mmap),true);
					}
					if ($k == 'bmap' || $k == 'smap'){
                        //舞狮大会 - 完成主线关卡次数
                        $Act6224Model = Master::getAct6224($this->uid);
                        $Act6224Model->task_add(1,1);
                    }
					break;
				//更新元宝
				case 'cash_sys':
				case 'cash_buy':
				case 'cash_use':
					$this->_rfcash();//刷新元宝
					Master::back_data($this->uid,'user','user',
						array('cash' => $this->info['cash'],'cashbuy' => $this->info['cash_buy']),true);
					break;
			}

//流水
            switch ($k){
                case 'food':
                    Game::cmd_flow(3, 3, $v, $this->info[$k]);
                    break;
                case 'army':
                    Game::cmd_flow(4, 4, $v, $this->info[$k]);
                    break;
                case 'coin':
                    Game::cmd_flow(2, 2, $v, $this->info[$k]);
                    break;
                case 'cash_sys':
                    if($v > 15000000){
                        Master::error(ITEMS_ERROR);
                    }
                    Game::cmd_flow(1, 1, $v, $this->info['cash']);
                    break;
                case 'cash_use':
                    Game::cmd_flow(1, 2, -$v, $this->info['cash']);
                    break;
                case 'exp':
                    Game::cmd_flow(5, 5, $v, $this->info[$k]);
                    break;
            }

			/*
		'coin_time','coin_num',
		'food_time','food_num',
		'army_time','army_num',
		'exp_time','exp_num',
		'love_time','love_num',
		'cb_time',
		'pk_time','pk_num','clubid','mw_num','mw_time',
		'fr_id','fr_da','fr_time','xf_num','xf_time','ys_num','ys_time',
		'voice','music','uptime','loginday','lastlogin','loginday',
		'platform','channel_id','ip',
	);
			 */
		}
		$this->_update = true;
	}

	/*
	 */
	public function sync()
	{
		if(is_array($this->info) && $this->info){
			$table = 'user_'.Common::computeTableId($this->uid);
			$updateKeysToDb = array_merge($this->updateAddKey,$this->updateSetKey);
			$updateKeysToDb = array_unique($updateKeysToDb);//去重复
			$sql = "update `{$table}` set ";
			foreach( $updateKeysToDb as  $perKey){
				$perValue = $this->info[$perKey];
				if(is_numeric($perValue)){
					$sql .= "`{$perKey}`={$perValue},";
				}else{
					$perValue = addslashes($perValue);
					$sql .= "`{$perKey}`='{$perValue}',";
				}
			}
			$sql = substr($sql,0,-1) ." where `uid`={$this->uid}";
			$db = $this->_getDb();
			$flag = $db->query($sql);
			if(!$flag){
				Master::error('db error UserModel_'.$sql);
			}
			return true;
		}
		return false;
	}


    /*
     * 删档返利
     */
    public function fvip(){

        $f_cfg = Game::get_peizhi('fvip');
        //如果没有配置返回
        if(empty($f_cfg)){
            return false;
        }
        //时间是否过期
        if(Game::is_over(strtotime($f_cfg['endtime']))){
            return false;
        }
        //判断是不是指定服务器
        $SevidCfg = Common::getSevidCfg();
        if($f_cfg['serv'] != 'all' ){
            $servs  = Game::serves_str_arr($f_cfg['serv']);
            if(!in_array($SevidCfg['he'],$servs)){
                return false;
            }
        }

        //检测返利数据
        $f_db = Common::getComDb();

        $isTable = $f_db->query('SHOW TABLES LIKE "fvip"');
        if( !$isTable ){
            return false;
        }
        $openid = Common::getOpenid($this->uid);
        $sql = "select * from `fvip` where `openid`='{$openid}';";
        $data = $f_db->fetchRow($sql);
        if( empty($data) || !empty($data['uid'])){
            return false;
        }

        //标记已返利
        $sql = "update `fvip` set `uid` = '{$this->uid}' where `openid`='{$openid}';";
        if ($f_db->query($sql)) {
//            //邮件下发物品
//            $items = array();
//            $items[] = array(
//                'id' => 1,
//                'count'  => floor($data['money'] * $f_cfg['rate'] * $f_cfg['fdiamond']),
//                'kind'   => 1,
//            );
//            Master::sendMail($this->uid, $f_cfg['title'], $f_cfg['content'],1,$items );
            $fUserModel = Master::getUser($this->uid);

            $orderlist = explode("|",$data['money']);
            foreach ($orderlist as $cmoney){
                if(!empty($cmoney)) {
                    $rtdata = $fUserModel->add_cash_buy_fuli(intval($cmoney) * 10, intval($cmoney));
                }
            }

            $fanlilist = explode("|",$data['fanli']);
            foreach ($fanlilist as $fanlimoney){
                if(!empty($fanlimoney)) {
                    $items = array();
                    $items[] = array(
                        'id' => 1,
                        'count'  => floor(intval($fanlimoney) * 10),
                        'kind'   => 1,
                    );
                    Master::sendMail($this->uid, "充值额外奖励", "以下是你单档首次充值获得的奖励",1,$items );
                }
            }

            //$rtdata = $fUserModel->add_cash_buy_fuli($data['money'] * 10,$data['money']);
            Master::click_destroy();
        }

        return true;
	}
	public function addface($job){
		//性别
	   // $sex = Game::intval($params,"sex");
	    $sex = empty($sex)?2:$sex;
	    //头像
	    //$job = Game::intval($params,'job');

        $UserModel = Master::getUser($this->uid);
        if ($job == $UserModel->info['job'])return;

	    //性别合法
	    if (!in_array($sex,array(1,2))){
	        Master::error("sex_err_".$sex);
	    }
	    //头像合法
        $sys_job = Game::getcfg("clothe_job");
	    if (empty($sys_job[$job])){
	        Master::error(COMMON_DATA_ERROR);
		}
		$IsOk = false;
		$allJob = json_decode($UserModel->info['allJob'],true);
		foreach($allJob as $value){
			if($value == $job){
				$IsOk=true;
				break;
			}
		}
		if($IsOk){
			return;
		}else{
			$d = $sys_job[$job];
			$cost = $d['cost'];
			//Master::sub_item($this->uid,KIND_ITEM, $cost["itemid"], $cost["count"]);
			array_push($allJob,$job);
		}
		$allJob = json_encode($allJob,JSON_UNESCAPED_UNICODE);

	
	    $s_update = array(
	        'sex' => $sex,
			'job' =>  $UserModel->info['job'],
			'allJob' => $allJob,
		);
		$UserModel->update($s_update);
		$TeamModel = Master::getTeam($UserModel->uid);
		$TeamModel->reset(4);
		$TeamModel->back_all_ep();//输出总属性
	}
	
	public function addOnLine(){

		$redis = Common::getRedisBySevId($this->_serverID);
		$lastlogin = $redis->zScore("user_last_login", $this->uid);
		if (empty($lastlogin) || $lastlogin <= $this->info["lastlogin"] ){
			$lastlogin = $this->info["lastlogin"];
		}

		$onLineTime = $_SERVER['REQUEST_TIME'] - $lastlogin;
		if ($onLineTime <= 300 && $onLineTime >= 0) {
			$redis->zIncrBy("user_on_line", $onLineTime, $this->uid );
			// Game::crontab_debug($this->uid." === ".json_encode($onLineTime), "user_on_line");
		}
		$redis->zAdd("user_last_login", $_SERVER['REQUEST_TIME'], $this->uid );
	}
}
