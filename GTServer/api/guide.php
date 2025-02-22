<?php
//新手引导模块
class GuideMod extends Base
{


	/*
	* 发送初始化信息
	*/
	private function _init(){
		//清除异步消息
		Common::loadModel("OtherMsgModel");
		OtherMsgModel::clear_data($this->uid);

		//用户基础信息
		$UserModel = Master::getUser($this->uid);
		$UserModel->getBase();

		//新手引导信息
		$Act32Model = Master::getAct32($this->uid);
		$Act32Model->back_data();
        //道具列表
        $ItemModel = Master::getItem($this->uid);
        $ItemModel->getBase();

		//阵法类
		$TeamModel  = Master::getTeam($this->uid);
		$TeamModel->back_hero();//返回门客信息.
		$TeamModel->back_all_ep();//输出总属性
		//红颜信息
		// $WifeModel = Master::getWife($this->uid);
		// $WifeModel->getBase();//红颜列表
		// //总亲密信息
		// Master::back_data($this->uid,'wife','base',array('allLove'=>$TeamModel->info['alllove']));
		$Act11Model = Master::getAct11($this->uid);
		$Act11Model->back_data();
		$Act6131Model = Master::getAct6131($this->uid);
		$Act6131Model->back_data();
		//子嗣信息
		$SonModel = Master::getSon($this->uid);
		$SonModel->getBase();
		//卡牌信息
		$CardModel = Master::getCard($this->uid);
		$CardModel->backCardList();

		//卡牌信息
		$BaowuModel = Master::getBaowu($this->uid);
		$BaowuModel->backBaowuList();

		//对我的提亲请求
		$Act10Model = Master::getAct10($this->uid);
		$Act10Model->back_data();

		//3种资源经营活动
		$Act1Model = Master::getAct1($this->uid);
		$Act1Model->back_data();
		//勤政爱民
		$Act1Mode3l = Master::getAct31($this->uid);
		$Act1Mode3l->back_data();
		//政务处理
		$Act2Model = Master::getAct2($this->uid);
		$Act2Model->back_data();

		//学院信息
		$Act15Model = Master::getAct15($this->uid);
		$Act15Model->back_data();//座位数量
		$Act16Model = Master::getAct16($this->uid);
		$Act16Model->back_data();//学员列表

		$this->flushZero(null, false);

		//称号列表
		$Act25Model = Master::getAct25($this->uid);
		$Act25Model->back_data();

		//寻访-赈灾-运势恢复 --每15分钟恢复
		$Act27Model = Master::getAct27($this->uid);
		$Act27Model->back_data();

//		//王爷领取奖励
//		$Act30Model = Master::getAct30($this->uid);
//		$Act30Model->back_data();

		//副本 蒙古军来袭
//		$Act21Model = Master::getAct21($this->uid);
//		$Act21Model->back_data();
//		$Act4Model = Master::getAct4($this->uid);//出战列表
//		$Act4Model->back_data();
//		$Sev3Model = Master::getSev3();//蒙古战斗道具奖励日志 滚动信息初始化
//		$Sev3Model->list_init($this->uid);

		//郊试献礼
		$Act22Model = Master::getAct22($this->uid);
		$Act22Model->back_data();
		$Act5Model = Master::getAct5($this->uid);//出战列表
		$Act5Model->back_data();
		$Redis5Model = Master::getRedis5();//伤害排行
		$Redis5Model->back_data();
		$Redis5Model->back_data_my($this->uid);
		//副本积分兑换
		$Act23Model = Master::getAct23($this->uid);
		$Act23Model->back_data();

		//道具合成
		$Act14Model = Master::getAct14($this->uid);
		if(!empty($Act14Model->info)){
			$Act14Model->back_data();
		}

		//挂机奖励
		$Act770Model = Master::getAct770($this->uid);
		$Act770Model->back_data();

		//联盟
		$Act40Model = Master::getAct40($this->uid);
		$Act40Model->back_data();

		if($Act40Model->info['cid'] > 0){
			$Sev11Model = Master::getSev11();
			$list = $Sev11Model->apply_list($cid);
			Master::back_data($this->uid,'club','applyList',$list);
		    //联盟boss
            $Sev12Model = Master::getSev12($Act40Model->info['cid']);
			$Sev12Model->bake_data();
			
			$Sev17Model = Master::getSev17($Act40Model->info['cid']);
			$Sev17Model->bake_data();

			$ClubModel = Master::getClub($Act40Model->info['cid']);
			$mypost = $ClubModel->info['members'][$this->uid]['post']; //我的职位
			if(empty($mypost)){
				$Act40Model->qz_out($Act40Model->info['cid'],$mypost);
			}else{
				$h_info = $ClubModel->getBase();
				Master::back_data($this->uid,'club','clubInfo',$h_info);

				$Sev15Model = Master::getSev15($Act40Model->info['cid']);
				$Sev15Model->bake_data();

				//工会聊天频道
				$Sev24Model = Master::getSev24($Act40Model->info['cid']);//工会频道
				$Sev24Model->list_init($this->uid);
			}

            //红包-当前帮会最后一条发红包记录
            $Act295Model = Master::getAct295($this->uid);
            $Act295Model->getLastHb();
		}

		//邮件
		$MailModel = Master::getMail($this->uid);
		$mails = $MailModel->getMails();

		// 好友
		$Act130Model = Master::getAct130($this->uid);
		$Act130Model->back_data();

		$Act131Model = Master::getAct131($this->uid);
		$Act131Model->back_data();

//		//皇宫 - 历代王爷
//		$Sev5Model = Master::getSev5();
//		$Sev5Model->back_data();

		//成就
		$Act36Model = Master::getAct36($this->uid);
		$Act36Model->back_data();

//        //PVB2 战斗冷却
//        $Act13Model = Master::getAct13($this->uid);
//        $Act13Model->back_data();

        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->back_data_init();

//        //衙门战数据
//        $Act7Model = Master::getAct7($this->uid);
//        $Act7Model->back_data();
//
//        $Act60Model = Master::getAct60($this->uid);
//        $Act60Model->back_data();
//        //我的衙门排名(衙门战开放标记)
//        $Redis6Model = Master::getRedis6();
//        $Redis6Model->back_data_my($this->uid);
        //衙门战击败20名日志 初始化
        $Sev6Model = Master::getSev6();
		$Sev6Model->list_init($this->uid);
//        //防守信息
//		$Act62Model = Master::getAct62($this->uid);
//		$Act62Model->back_data();
//		//仇人信息
//		$Act63Model = Master::getAct63($this->uid);
//		$Act63Model->back_data();
//		//挑战出战表
//		$Act8Model = Master::getAct8($this->uid);
//		$Act8Model->back_data();

		//翰林院 7 级开启
		//if ($UserModel->info['level'] >= 7){
//		$Act58Model = Master::getAct58($this->uid);
//		$Act58Model->back_data();
		//}

        //神迹福利
//        $Act65Model = Master::getAct65($this->uid);
//        $Act65Model->back_data();
        //首充福利
        $Act66Model = Master::getAct66($this->uid);
        $Act66Model -> getRwd();
		$Act66Model->back_data();
		
		//连续首冲福利
		$Act316Model = Master::getAct316($this->uid);
		$Act316Model->check66rwd();
		$Act316Model->back_data();

		//返回卡池信息
		$Act317Model = Master::getAct317($this->uid);
		$Act317Model->back_data();

		//返回卡牌剧情信息
		$Act318Model = Master::getAct318($this->uid);
		$Act318Model->back_data();

		//返回卡池信息
		$Act319Model = Master::getAct319($this->uid);
		$Act319Model->back_data();

		//返回卡牌剧情信息
		$Act320Model = Master::getAct320($this->uid);
		$Act320Model->back_data();
        //vip福利
        //vip福利
        $Act67Model = Master::getAct67($this->uid);
        $Act67Model->back_data();

//        //加微信加QQ
//        $Act69Model = Master::getAct69($this->uid);
//        $Act69Model->back_data();

        //充值-充值档次
        $Act70Model = Master::getAct70($this->uid);
        $Act70Model->back_data();

        //vip经验列表
        $Act71Model = Master::getAct71($this->uid);
		$Act71Model->back_data();

		$Act703Model = Master::getAct703($this->uid);
		$Act703Model->back_data();

		$Act710Model = Master::getAct710($this->uid);
		$Act710Model->back_data();
		
		$Act716Model = Master::getAct711($this->uid);
		$Act716Model->back_data();
		
//
//        //用户场景
//        $Act74Model = Master::getAct74($this->uid);
//        $Act74Model->back_data_a();

        //官群QQ
        Common::loadModel('HoutaiModel');
        $guanq = Game::get_peizhi('gq_status');
        $guanqPlat = Game::get_peizhi('gq_status_'.$UserModel->info['platform']);
        if (!empty($guanqPlat)) {
            //合并或覆盖配置
            $guanq = array_merge($guanq, $guanqPlat);
        }

        if(empty($guanq['isNotice'])){
            //旧版公告
            $Act33Model = Master::getAct33($this->uid);
            $Act33Model->getGG();
        }else{
            //新版公告
            $Sev90Model = Master::getSev90();
            $Sev90Model->out_back($this->uid,$guanq['isNotice']);
        }
        
//        Master::$bak_data['a']['fuli']['guanqun']['status'] = empty($guanq['status']) ? 1 : $guanq['status'];
//        $UserModel = Master::getUser($this->uid);
//		Master::$bak_data['a']['fuli']['guanqun']['qq'] = HoutaiModel::getQQ($UserModel->info['platform']);
//        $switch = self::setDerail($guanq);
//		//各种限制开关
//		Master::$bak_data['a']['derail']['list'] = $switch;

		//聊天信息初始化
	    $Sev22Model = Master::getSev22();//公共频道
	    $Sev22Model->list_init($this->uid);

        $Sev6012Model = Master::getSev6012();//公共频道
        $Sev6012Model->list_init($this->uid);

        $Sev6013Model = Master::getSev6013();//公共频道
        $Sev6013Model->list_init($this->uid);

		$Sev25Model = Master::getSev25();//跨服频道
		$Sev25Model->list_init($this->uid);

//		Common::loadModel('HoutaiModel');
//		$hd300Cfg = HoutaiModel::get_huodong_info('huodong_300');
//		if(!empty($hd300Cfg)){
//		    $Sev62Model = Master::getSev62();
//			if(!empty($Sev62Model->info)) {
//				$Sev62Model->list_init($this->uid);
//				$Sev60Model = Master::getSev60($hd300Cfg['info']['id']);
//				$Sev60Model->list_init($this->uid);
//			}
//
//		}

		//记录登录日志
		HoutaiModel::insertData($this->uid,$UserModel->info['platform']);

		$properties = array(
            'power' => $TeamModel->info['shili'],
            'login_or_out' => 0,
        );
        Master::taTrack($this->uid,"log_role_login",$properties);

		//邮件
		$Act93Model = Master::getAct93($this->uid);
		$Act93Model->sendMail();

		$Act716Model = Master::getAct716($this->uid);
		$Act716Model->back_data();

		$Act725Model = Master::getAct725($this->uid);
		$Act725Model->back_data();

		
		$Act726Model = Master::getAct726($this->uid);
		$Act726Model->back_data();

		//华服等级领取奖励
		$Act755Model = Master::getAct755($this->uid);
		$Act755Model->back_data();
		
		//锦衣裁剪
		$Act756Model = Master::getAct756($this->uid);
		$Act756Model->back_data();

		//心忆
		$Act757Model = Master::getAct757($this->uid);
		$Act757Model->back_data();

		//获取装备特效
		$Act759Model = Master::getAct759($this->uid);
		$Act759Model->back_data();

		//登录检测卡牌羁绊
		$Act762Model = Master::getAct762($this->uid);
		$Act762Model->back_data();

		//战斗编队
		$Act763Model = Master::getAct763($this->uid);
		$Act763Model->back_data();

		//伙伴培养道具信息
		$Act2000Model = Master::getAct2000($this->uid);
		$Act2000Model->back_data();
		
		//伙伴信物信息
		$Act2001Model = Master::getAct2001($this->uid);
		$Act2001Model->back_data();

		//伙伴羁绊信息
		$Act2002Model = Master::getAct2002($this->uid);
		$Act2002Model->back_data();

		$Act2003Model = Master::getAct2003($this->uid);
		$Act2003Model->checkJibanUnlock();

		$Act2004Model = Master::getAct2004($this->uid);
		$Act2004Model->back_data();

		$Act2005Model = Master::getAct2005($this->uid);
		$Act2005Model->back_data();

		//黑名单
		$Act97Model = Master::getAct97($this->uid);
		$Act97Model->back_data();

		$Act135Model = Master::getAct135($this->uid);
		$FriendChat = Master::getFriendChat($this->uid);
		foreach($Act135Model->info as $k => $v){
			$FriendChat->listReset($this->uid,$k);
			$FriendChat->listCheck($this->uid,$k,$fuid);
		}
		$FriendChat->back_data_au($this->uid);
//
//		//私聊红点
//		$FriendChat = Master::getFriendChat($this->uid);
//		$FriendChat->noticeList($this->uid);


//        //所有版本
//        $SevidCfg = Common::getSevidCfg();
//        //开服绝对时间
//        Common::loadModel('ServerModel');
//        $show_time = ServerModel::getShowTime($SevidCfg['sevid']);
//        //新版本
//        $openHome2 = Game::get_peizhi('openHome2');
//        //---VIP版本
//        $vip_ver = 1;
//        if(!empty($openHome2['vip_ver'])){
//            if( isset($openHome2['vip_time']) && $show_time >  strtotime($openHome2['vip_time'])  ){
//                $vip_ver = $openHome2['vip_ver'];
//            }
//        }
//        Master::back_data($this->uid,'user','banben',array(
//            'vipver' => $vip_ver    //VIP版本
//        ));

		//发配-已发配的门客列表
		$Act129Model = Master::getAct129($this->uid);
        $Act129Model->back_data();
		$Act129Model->back_data_hero();

        //最后一次登陆时间
        $Act48Model = Master::getAct48($this->uid);
        $Act48Model->reset_ltime();

        //插入特殊关卡返回
        $Act6000Model = Master::getAct6000($this->uid);
        $Act6000Model->back_data();

        //玩家羁绊数据
        $Act6001Model = Master::getAct6001($this->uid);
        $Act6001Model->back_data();

        //委派
        $Act6003Model = Master::getAct6003($this->uid);
        $Act6003Model->back_data();

        //书信
        $Act6004Model = Master::getAct6004($this->uid);
        $Act6004Model->back_data();

        //羁绊属性
        $Act6005Model = Master::getAct6005($this->uid);
		$Act6005Model->back_data();
		
		//卡牌羁绊
		$Act6006Model = Master::getAct6006($this->uid);
		$Act6006Model->back_data();

        //徒弟历练信息
        $Act6133Model = Master::getAct6133($this->uid);
        $Act6133Model->back_data();

        //徒弟历练信息
        $Act6134Model = Master::getAct6134($this->uid);
        $Act6134Model->back_data();

		//衣服
		$Act6140Model = Master::getAct6140($this->uid);
		$Act6140Model->back_data();
		
        $Act6141Model = Master::getAct6141($this->uid);
        $Act6141Model->back_data();
        $Act6143Model = Master::getAct6143($this->uid);
		$Act6143Model->back_data();
		$Act6144Model = Master::getAct6144($this->uid);
		$Act6144Model->checkInitBlanks();
		$Act6145Model = Master::getAct6145($this->uid);
        $Act6145Model->checkInitEmojis();

        //头像
        $Act6150Model = Master::getAct6150($this->uid);
        $Act6150Model->back_data();
        $Act6151Model = Master::getAct6151($this->uid);
        $Act6151Model->back_data();

        //御膳房信息
        $Act6100Model = Master::getAct6100($this->uid);
        $Act6100Model -> back_data();
        $Act6101Model = Master::getAct6101($this->uid);
        $Act6101Model -> back_data();
        $Act6103Model = Master::getAct6103($this->uid);
        $Act6103Model -> back_data();
        $Act6104Model = Master::getAct6104($this->uid);
        $Act6104Model -> back_data();
        $Act6105Model = Master::getAct6105($this->uid);
        $Act6105Model -> back_data();
        $Act6107Model = Master::getAct6107($this->uid);
        $Act6107Model -> back_data();
        
        //珍宝馆
        $Act6111Model = Master::getAct6111($this->uid);
        $Act6111Model -> back_data();

        //伙伴展示
        $Act6120Model = Master::getAct6120($this->uid);
        $Act6120Model -> back_data();

        //伙伴语音包
        $Act6137Model = Master::getAct6137($this->uid);
		$Act6137Model -> back_data();
		
		//七日庆典登录检测
		$Act700Model = Master::getAct700($this->uid);
		if($Act700Model->getOpenDay() < 9){
			$Act700Model->setSevenSign();
			$Act700Model->back_data();
		}

		//0元购登录检测
		$Act701Model = Master::getAct701($this->uid);
		$Act701Model -> back_data();

		//钱庄
		$Act702Model = Master::getAct702($this->uid);
		$Act702Model -> back_data();

        //御花园
        // $Act6192Model = Master::getAct6192($this->uid);
        // $Act6192Model -> make_out(true);
        // $Act6192Model -> back_data();
        $Act6194Model = Master::getAct6194($this->uid);
        $Act6194Model -> initIndex();

        //许愿树
        $Act6210Model = Master::getAct6210($this->uid);
		$Act6210Model->back_data();

		//卡牌
		$Act8500Model = Master::getAct8500($this->uid);
		$Act8500Model->back_data();
		
		//直购礼包
		$Act6180Model = Master::getAct6180($this->uid);
		Master::back_data($this->uid,"zchuodong","clickInfo",array("isClick" => ($Act6180Model->info['isClick'])));

        // 活动直购回调
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->huodong_order_back();

        //活动公告
        $activity_note = Game::get_peizhi('activity_note');
        Master::$bak_data['a']['notice']['activity'] = $activity_note;
	}

	/*
	* 登陆
	*/
	public function login($params)
	{
		//检查开服状态
		//检查全服延迟开关
		$platform = Game::strval($params,'platform');
		//获取平台信息
		Common::loadModel('OrderModel');
	    OrderModel::get_platform_cfg($platform);

		$UserModel = Master::getUser($this->uid);

		if (empty($UserModel->info['uid'])) {//新用户

			//初始化新用户
			$UserModel->newUser(array(
				'uid' => $this->uid,
				'step' => 0,
				'channel_id' => SNS_BASE,
				'platform' => $platform,
			));
			//记录登录日志
			Common::loadModel('HoutaiModel');
			HoutaiModel::insertRegData(array('uid'=> $this->uid,'platform' => $platform));
			//发送用户初始化信息
			$UserModel->getBase();//用户基础信息

			//下发新手礼包
			$giftcfg = Game::get_peizhi('usergift_'.$platform);
            if (empty($giftcfg)) {
                $giftcfg = Game::get_peizhi('usergift');
            }
			if(!empty($giftcfg)){
			    foreach ($giftcfg as $val){
			        Master::sendMail($this->uid, $val['title'], $val['content'],1,$val['items']);
			    }
			}

            //删档返利
            //$UserModel->fvip();
            //咸鱼日志
            Common::loadModel('XianYuLogModel');
            XianYuLogModel::role($platform, $this->uid);
			XianYuLogModel::loginrole($platform, $this->uid);

			$properties = array(
                '#reg_time' => date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']),
            );
            Master::taTrack($this->uid,"log_account_reg",$properties);

		} else {
			$allJob = json_decode($UserModel->info['allJob'],true);
			if(empty($allJob)){
				$allJob = array(
					$UserModel->info['job']
				);
			}
			$allJob = json_encode($allJob,JSON_UNESCAPED_UNICODE);
			$u_update = array(
				'channel_id' => SNS_BASE,
				'platform' => $platform,
				'allJob' => $allJob,
			);
			$UserModel->update($u_update);

			if ($UserModel->info['step'] != 0){
				$this->_init();
			}

            //咸鱼日志
            Common::loadModel('XianYuLogModel');
			XianYuLogModel::loginrole($platform, $this->uid);

		}
		//留存报表
        $diff = ceil(($_SERVER['REQUEST_TIME']-$UserModel->info['regtime'])/86400);
        $diffday = Game::is_today($UserModel->info['regtime'])?0:$diff;
        Game::flow_php_record($this->uid, 11, $UserModel->info['level'], $diffday,0,'',date("Y-m-d",$UserModel->info['regtime']));
        //七天签到活动签到
        $Act287Model = Master::getAct287($this->uid);
		$Act287Model->sign();
		
		$Act700Model = Master::getAct700($this->uid);
        $Act700Model->sendSevenMail();

        //四十五天签到活动签到
        $Act6500Model = Master::getAct6500($this->uid);
		$Act6500Model->sign();
		
		Common::loadModel('XianYuNewLogModel');
		XianYuNewLogModel::InsertGameCharacterLineDateDot($this->uid,1);

	}

	/*
	 * 取名字
	 *  "name":["","角色名"],
	  "sex":[0,"性别"],
	  "job":[0,"职业"],
	 */
	public function setUinfo($params)
	{
		//用户类
		$UserModel = Master::getUser($this->uid);
		//当前状态
		if ($UserModel->info['step'] != 0){
			Master::error(STATUS_ERROR.$UserModel->info['step']);
		}

		//名字
		$name = Game::strval($params,"name");
		//性别
		$sex = Game::intval($params,"sex");
		//职业
		$job = Game::intval($params,"job");
		//非法字符判定
		$name = Game::filter_char($name,0);
		//敏感字符判定
		$name = Game::str_mingan($name);
		//非法字符判定
		$name = Game::str_feifa($name);
		//名字长度判定
		$len = Common::utf8_strlen($name);
		if ($len  < 2 ||$len > 5){
			Master::error(USER_COUNT_SHORT_NAME);
		}
		//检查重名
		Game::chick_name($this->uid,$name);

		//性别合法
		if (!in_array($sex,array(1,2))){
			Master::error("sex_err_".$sex);
		}
		//头像合法
		if (!in_array($job,array(1,2,3,4,5,6,7,8,9,10, 99))){
			Master::error("job".$job);
		}
		$allJob = json_decode($UserModel->info['allJob'],true);
		if (empty($allJob)){
			$allJob = array(
				$job
			);
		}
		$allJob = json_encode($allJob,JSON_UNESCAPED_UNICODE);
		//确认取名
		$u_update = array(
			'name' => $name,
			'step' => 1,
			'sex' => $sex,
			'job' => $job,
			'allJob' => $allJob,
		);
		$UserModel->update($u_update);

		//成就更新
		// $Act36Model = Master::getAct36($this->uid);
		// $Act36Model->set(2,1);//官品等级

		$TeamModel = Master::getTeam($this->uid);
		//加上初始化道具
		$this->_addinit();

		//清除信息
		Master::clear_bak();

		Master::error_msg(USER_CREATE_SUCCESS,1);

		//返回初始化信息
		$this->_init();

		Master::taTrack($this->uid,"log_role_reg",array());

		Common::loadModel('XianYuNewLogModel');
		XianYuNewLogModel::InsertGameCharacterDataDot($this->uid,$name,$UserModel->info['platform']);
	}



	/*
	 * 随机名字
	 */
	public function randName($params)
	{
		//载入文件
		$rand_name = Common::getLang('rand_name');
		$name = '';
		for ($i=0 ; $i < $rand_name['len'] ; $i++){
			$name .= $rand_name['names'][$i][array_rand($rand_name['names'][$i])];
		}
		Master::back_data($this->uid,"system","randname",array("name"=>$name));
	}

	/*
	 * 新手引导步骤保存
	 */
	public function guide($params){
		//新手引导步骤信息模块
		$Act32Model = Master::getAct32($this->uid);
		$Act32Model->set_guide($params);
	}

	/*
	 * 新手引导平民升官
	 */
	public function guideUpguan(){
		//新手引导步骤信息模块
		$Act32Model = Master::getAct32($this->uid);
		$Act32Model->up_guan();

        //增加等级服装
        $UserModel = Master::getUser($this->uid);
        $Act6140Model = Master::getAct6140($this->uid);
        $Act6140Model -> addUseLvClothe($UserModel->info['level']);
	}

	/*
	 * 加上初始化道具
	 */
	private function _addinit(){
		//加上初始门客
		// $HeroModel = Master::getHero($this->uid);
		// $HeroModel->add_hero(1);
		// $HeroModel->add_hero(2);
		// $HeroModel->add_hero(3);
		// $HeroModel->add_hero(4);
		// $HeroModel->add_hero(5);

		//获取道具列表
		$item_cfg = Game::getcfg('item');

		$SonModel = Master::getSon($this->uid);

		//阵法类
		$TeamModel  = Master::getTeam($this->uid);
		//进入3个主要榜单
		//势力榜
		$Redis1Model = Master::getRedis1();
		$Redis1Model->zAdd($this->uid,$TeamModel->info['shili']);
		//关卡榜
		$Redis2Model = Master::getRedis2();
		$Redis2Model->zAdd($this->uid,1);
		//亲密榜
		$Redis3Model = Master::getRedis3();
		$Redis3Model->zAdd($this->uid,$TeamModel->info['fetter']);

		//添加测试卡牌
		//$CardModel = Master::getCard($this->uid);
		//$CardModel->add_card(1001);
	}
	/*
	 * 联系客服
	 * */
	public function contackKF() {
	    $kefu = Game::get_peizhi('kefu');
	    if(empty($kefu)){
	        Master::error(SYSTEM_NO_KEFU);
	    }
	    Master::back_data(0,"system","kefu",$kefu);
	}

	/*
	 * 设置开关
	 * */
	public function setDerail($guanq) {
	    $derail = array();
	    if(!empty($guanq['status'])){
	        $derail['status'] =  $guanq['status'];//官群QQ
	    }
	    if(!empty($guanq['blacklist'])){
	        $derail['blacklist'] =  $guanq['blacklist'];//黑名单
	    }
	    //如果设置了 跨服榜单延迟开放
	    if(!empty($guanq['isKuaRankOpen_day'])){
	    	$SevidCfg = Common::getSevidCfg();
	    	//当前开服天数
	    	$openDay = ServerModel::getOpenDays($SevidCfg['sevid']);
	    	//开服绝对时间
	    	$show_time = ServerModel::getShowTime($SevidCfg['sevid']);

			//是否配置了旧服保留时间点
	    	if(!empty($guanq['isKuaRankOpen_time'])
	    	&& $show_time <= $guanq['isKuaRankOpen_time'])
	    	{
	    		//旧服保留功能 不限制 直接开放
	    		$guanq['isKuaRankOpen'] = 1;
	    	}
	    	else if ($openDay >= $guanq['isKuaRankOpen_day']){
	    		//按照开服天数开放
	    		$guanq['isKuaRankOpen'] = 1;
	    	}
	    	else
	    	{
	    		//新服关闭
	    		$guanq['isKuaRankOpen'] = 0;
	    	}
	    	unset($guanq['isKuaRankOpen_day']);
	    	unset($guanq['isKuaRankOpen_time']);
	    }
	    //如果设置了 跨服聊天延迟开放
		if(!empty($guanq['crossChat_day'])){
	    	$SevidCfg = Common::getSevidCfg();
	    	//当前开服天数
	    	$openDay = ServerModel::getOpenDays($SevidCfg['sevid']);
	    	//开服绝对时间
	    	$show_time = ServerModel::getShowTime($SevidCfg['sevid']);

			//是否配置了旧服保留时间点
	    	if(!empty($guanq['crossChat_time'])
	    	&& $show_time <= $guanq['crossChat_time'])
	    	{
	    		//旧服保留功能 不限制 直接开放
	    		$guanq['crossChat'] = 1;
	    	}
	    	else if ($openDay >= $guanq['crossChat_day']){
	    		//按照开服天数开放
	    		$guanq['crossChat'] = 1;
	    	}
	    	else
	    	{
	    		//新服关闭
	    		$guanq['crossChat'] = 0;
	    	}
	    	unset($guanq['crossChat_day']);
	    	unset($guanq['crossChat_time']);
	    }

	    if(!empty($guanq)){
	        $count = count($guanq);
	        $i = 1;
	        $derail['switch'] = '';
	        foreach ($guanq as $key =>$status){
	            if($i == $count){
	                $derail['switch'] .= $key.':'.$status;
	            }else{
	                $derail['switch'] .= $key.':'.$status.';';
	            }
	            $i++;
	        }
	    }
	    return $derail;
	}

	public function offline(){
		$UserModel = Master::getUser($this->uid);
		if(!empty($UserModel->info['uid'])){
			$this->_init();
		}
	}

	public function flushZero($param, $flag = true){
        //膜拜
        $Act17Model = Master::getAct17($this->uid);
        $Act17Model->back_data();

        //请安
        $Act18Model = Master::getAct18($this->uid);
        $Act18Model->back_data();

        //灵囿
        $Act19Model = Master::getAct19($this->uid);
        $Act19Model->back_data();

        //名望
        $Act20Model = Master::getAct20($this->uid);
        $Act20Model->back_data();

        //献礼
        $Act23Model = Master::getAct23($this->uid);
        $Act23Model->back_data();

        //寻访
        $Act26Model = Master::getAct26($this->uid);
        $Act26Model->back_data();

        //寻访-赈灾-转运
        $Act28Model = Master::getAct28($this->uid);
        $Act28Model->back_data();

        //日常任务
        $Act35Model = Master::getAct35($this->uid);
        $Act35Model->back_data();

        //签到福利
        $Act37Model = Master::getAct37($this->uid);
        $Act37Model->back_data();

        //年月卡
        $Act68Model = Master::getAct68($this->uid);
        $Act68Model->back_data();

        //祈福
        $Act6154Model = Master::getAct6154($this->uid);
		$Act6154Model->back_data();

		//弹劾数据
		$Act720Model = Master::getAct720($this->uid);
		$Act720Model->back_data();
		
		$Act730Model = Master::getAct730($this->uid);
		$Act730Model->refreshEventTime();
				
		$Act731Model = Master::getAct731($this->uid);
		$Act731Model->back_data();

		$Act733Model = Master::getAct733($this->uid);
		$Act733Model->back_data();
		
		$Act734Model = Master::getAct734($this->uid);
		$Act734Model->back_data();
		
		$Act736Model = Master::getAct736($this->uid);
        $Act736Model->back_data();

		//返回守护次数
		$Act740Model = Master::getAct740($this->uid);
		$Act740Model->back_data();
		$Act741Model = Master::getAct741($this->uid);
		$Act741Model->back_data();
		$Act742Model = Master::getAct742($this->uid);
		$Act742Model->back_data();
		$Act743Model = Master::getAct743($this->uid);
		$Act743Model->back_data();
		$Act745Model = Master::getAct745($this->uid);
		$Act745Model->back_data();

		$Act750Model = Master::getAct750($this->uid);
		$Act750Model->back_data();

        //生效列表信息
        $Act200Model = Master::getAct200($this->uid);
        if ($flag){
            $Act200Model->flushZero();
        }
        else {
            $Act200Model -> back_data();
        }


        //珍宝馆
        $Act6110Model = Master::getAct6110($this->uid);
        $Act6110Model -> back_data();

        //御花园
        // $Act6190Model = Master::getAct6190($this->uid);
        // $Act6190Model -> zeroFlush();

        //科举
        $Act6106Model = Master::getAct6106($this->uid);
        $Act6106Model -> back_data();

        //许愿树
        $Act6210Model = Master::getAct6210($this->uid);
		$Act6210Model -> back_data();
		
		//七日庆典登录检测
		$Act700Model = Master::getAct700($this->uid);
		if($Act700Model->getOpenDay() < 9){
			$Act700Model->setSevenSign();
			$Act700Model->sendSevenMail();
			$Act700Model->back_data();
		}else{
			Master::back_data($this->uid,"sevenCelebration","seveninfo",array());
		}

		Master::back_data($this->uid,"day","timeStamp",array(strtotime(date("Y-m-d",strtotime("+1 day")))));

		//直购礼包
		if($flag){
			$Act6180Model = Master::getAct6180($this->uid);
			$Act6180Model->initClick();
			$Act6180Model->back_data();
		}
    }
}
