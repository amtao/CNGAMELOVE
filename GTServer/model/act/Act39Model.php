<?php
//
require_once "ActFlBaseModel.php";
/*
 * 主线任务
 */
class Act39Model extends ActBaseModel
{
	public $atype = 39;//活动编号
	
	public $comment = "主线任务";
	public $b_mol = "task";//返回信息 所在模块
	public $b_ctrl = "tmain";//返回信息 所在控制器
	
	public $add_type = 0;//添加种类信息 增加时的类型

	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		
	
	
	);
	
	/*
	 * 初始化函数
	 */
	public function do_init(){
		$task_cfg = Game::getcfg_info('task_main',1);
		
		//目标参数
		$key = self::task_max_key($task_cfg['type']);
		
		$init = array(
			'id' => 1,
			'type' => $task_cfg['type'],  //种类
			'max' => $task_cfg['set'][$key],  //目标数值
			'info' => array(),  //种类   => 当前数值
		);
		return $init;
	}
	
	/**
	 * 添加种类信息
	 * @param $type   种类
	 * @param $num    信息
	 */
	public function task_add($type,$num){
		//初始化
		if(empty($this->info['info'][$type])){
			$this->info['info'][$type] = 0;
		}
		
		//标记下发任务
		if($this->info['type'] == $type){
			$this->add_type = 1;
		}
		
		switch($type){
			case 1:  //养精蓄锐	经营商产次数达X次	
			case 2:  //五谷丰登	经营农产次数达X次	
			case 3:  //兵甲富足	招募士兵次数达X次	
			case 9:  //养儿育女	子嗣培养次数达X次	
			case 10:  //喜得贵子	子嗣数量达X个	
			case 12:  //处理政务	处理政务次数达X次	
			case 13:  //勤政爱民	累计使用1次政务令	
			case 15:  //领取俸禄	皇宫请安次数达到X次	
			case 16:  //宠爱有加	宠幸红颜次数达X次	
			case 17	:	//	随机传唤	随机传唤红颜次数达X次
			case 18	:	//	赏赐红颜	红颜赏赐次数达X次
			case 19	:	//	膜拜大神	膜拜次数达X次（三榜均可膜拜）
			case 20	:	//	每日签到	累计签到X天
			case 21	:	//	培养门客	使用强化卷轴升级门客书籍X次
			case 22	:	//	喜结良缘	联姻次数达X次
			case 23	:	//	游历寻访	寻访次数达X次
			case 24	:	//	体力恢复	使用体力丹X次
			case 25	:	//	技能升级	门客技能升级次数达X次
			case 26	:	//	红颜技能	升级红颜技能X次
			case 27	:	//	衙门出使	衙门出使X次
			case 28	:	//	活力恢复	使用活力丹X次
			case 29	:	//	严惩贪官	惩罚犯人次数达X次
			case 30	:	//	书院学习	书院学习次数达到X次
			case 31	:	//	唯我独尊	衙门使用挑战书X次
			case 32	:	//	购买道具	商城购买任意道具X次
			case 34	:	//	联盟兑换	联盟兑换次数达X次
			case 35	:	//	联盟建设	联盟建设次数达X次

            case 38	:	//	拔旗易帜	使用令旗X次（任意令旗）
            case 39	:	//	跨服发言	跨服聊天发X条消息
            case 40	:	//	拜访亲友	拜访次数X次
            case 41	:	//	乘奔逐北	使用X次追杀令
            case 42	:	//	围剿乱党	围剿X波乱党
            case 44	:	//	丝路竞价	丝路竞价X关
            case 45	:	//	开启校场	开启X次校场
            case 46	:	//	觥筹交错	赴宴次数X次
            case 47	:	//	完成中关卡id为xx的任务
            case 48	:	//	完成某个羁绊
            case 49	:	//	完成中关卡id为xx的任务
            case 50	:	//	做一次饭
            case 51	:	//	上缴宝物
            case 52	:	//	完成书信
            case 53	:	//	出游达到多少次
            case 54	:	//	开宴达到多少次
            case 55	:	//	领取一次月卡/年卡
            case 56	:	//	充值任意金额
            case 57	:	//	元宝祈福一次
            case 58	:	//	进行整理
            case 59	:	//	徒弟游历
			case 60	:	//	观星阁
			case 100:   //  玩家换装
			case 101:   //  伙伴换装
			case 102:   //  伙伴培养
			case 103:   //  送礼X次
			case 104:	//  伙伴资质升级X次
			case 105:	//  伙伴信物升级X次
			case 106:	//  伙伴升星X次
			case 107:	//  心动时刻卡升级X次
			case 108:	//  升级四海奇珍X次
			case 111:	//  四海奇珍许愿X次
			case 112:	//  心动故事许愿X次
			case 113:	//  许愿树许愿X次
			case 115:   //  收集X件不重复的服装
			case 117:   //  办差X次(暂时废弃 用1)
			case 120:   //  收集X个不同的羁绊故事
			case 121:   //  郊祀献礼累计获得X分
			case 122:   //  参与廷斗X次
			case 123:   //  激活X个伙伴信物
			case 124:   //  累计登录天数
			case 127:   //  收集灰鹞的X个羁绊故事
			case 128:   //  收集南宫辰的羁绊故事
			case 129:   //收集蔺晟安的羁绊故事
			case 130:   //收集萧易的羁绊故事
			case 131:   //收集卫戢的羁绊故事
			case 132:   //收集纪延的羁绊故事
			case 133:   //灰鹞升到X星
			case 134:   //南宫辰升星
			case 135:   //萧易升星
			case 136:   //卫戢升星
			case 137:   //纪延升星
			case 138:   //蔺晟安升星
			case 139:   //参与郊祀献礼次数
			case 140:   //解锁X个灰鹞的信物
			case 141:   //解锁X个南宫辰的信物
			case 142:   //解锁X个萧易的信物
			case 143:   //解锁X个卫戢的信物
			case 144:   //解锁X个纪延的信物
			case 145:   //解锁X个蔺晟安的信物
			case 146:   //将X个卡牌升到满星
			case 147:  //将X个奇珍升到满星
			case 148:  //累计赴约次数
			case 149:  //卡牌升级多少次
			case 151:  //弹劾了多少次
			case 152:  //累计邀约次数
			case 153:  //累计郊游次数
			case 154:  //剧情推进
			case 155:  //伙伴升级
			case 156:  //元宝祈福
			case 157:  //印痕升级多少次
			case 158:  //升华多少次
			case 159:  //公会捐献X次
			case 160:  //公会商店兑换X次
			case 161:  //帮会聊天X次
				$this->info['info'][$type] += $num;
				break;
			case 116:   //任意冲榜活动获得X名之内
				if($this->info['info'][$type] == 0){
					$this->info['info'][$type] = $num;
					break;
				}else {
					if($this->info['info'][$type] > $num){
						$this->info['info'][$type] = $num;
					}
					break;
				}
			case 109:	//  激活心动故事卡X张
			case 110:	//  激活四海奇珍X个
			case 118:   //伙伴总资质达到X
			case 119:   //伙伴羁绊等级达到X
			case 125:   //  累计伙伴数量
			case 150:   //弹劾最高到多少层
				if($this->info['info'][$type] < $num){
					$this->info['info'][$type] = $num;
				}
				break;
			default:
                $SevidCfg = Common::getSevidCfg();
                $fileName = "task_add_type_err_".date("Ymd");
                $array = array(
                    'errormsg' => 'task_add_type_err'.$type,
                    'request'=>file_get_contents("php://input"),
                    //执行路径
                    'debug'=>debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
                );
                $content = "\r\n" . date('Y-m-d H:i:s') .  '-serverID-'.$SevidCfg['sevid']."\r\n".'wen_jian: '.$_SERVER['argv'][0]."\r\n" .
                    ' Request: ' . 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER['REQUEST_URI']."\r\n".' DATA:' . var_export($array, true)  . "\r\n";
                Common::log($fileName, $content);
				Master::error('task_add_type_err'.$type);
		}
		$Act700Model = Master::getAct700($this->uid);
		if($Act700Model->getOpenDay() < 8){
			$Act700Model->setSevenTask($type,$this->info['info'][$type]);
		}
		$Act36Model = Master::getAct36($this->uid);
		$Act36Model->setTask($type);
		$Act35Model = Master::getAct35($this->uid);
		$Act35Model->setTask($type,$num);

		$Act8011Model = Master::getAct8011($this->uid);
		$Act8011Model->setCurrencyTask($type,$num);

		$Act8016Model = Master::getAct8016($this->uid);
		$Act8016Model->setCurrencyTask($type,$num);

		$Act761Model = Master::getAct761($this->uid);
		$Act761Model->completeTask($type,$num);
		
		$this->save();
	}
	
	/**
	 * 获取当前数值
	 * @param $type   种类
	 */
	public function task_num($type){
		$num = 0;  //返回任务当前完成程度的数值
		switch($type){
			case 1:  //养精蓄锐	经营商产次数达X次	
			case 2:  //五谷丰登	经营农产次数达X次	
			case 3:  //兵甲富足	招募士兵次数达X次	
			case 9:  //养儿育女	子嗣培养次数达X次	
			case 10:  //喜得贵子	子嗣数量达X个	
			case 12:  //处理政务	处理政务次数达X次	
			case 13:  //勤政爱民	累计使用1次政务令	
			case 15:  //领取俸禄	皇宫请安次数达到X次	
			case 16:  //宠爱有加	宠幸红颜次数达X次	
			case 17	:	//	随机传唤	随机传唤红颜次数达X次
			case 18	:	//	赏赐红颜	红颜赏赐次数达X次
			case 19	:	//	膜拜大神	膜拜次数达X次（三榜均可膜拜）
			case 20	:	//	每日签到	累计签到X天
			case 21	:	//	培养门客	使用强化卷轴升级门客书籍X次
			case 22	:	//	喜结良缘	联姻次数达X次
			case 23	:	//	游历寻访	寻访次数达X次
			case 24	:	//	体力恢复	使用体力丹X次
			case 25	:	//	技能升级	门客技能升级次数达X次
			case 26	:	//	红颜技能	升级红颜技能X次
			case 27	:	//	衙门出使	衙门出使X次
			case 28	:	//	活力恢复	使用活力丹X次
			case 29	:	//	严惩贪官	惩罚犯人次数达X次
			case 30	:	//	书院学习	书院学习次数达到X次
			case 31	:	//	唯我独尊	衙门使用挑战书X次
			case 32	:	//	购买道具	商城购买任意道具X次
			case 34	:	//	联盟兑换	联盟兑换次数达X次	
			case 35	:	//	联盟建设	联盟建设次数达X次

            case 38	:	//	拔旗易帜	使用令旗X次（任意令旗）
            case 39	:	//	跨服发言	跨服聊天发X条消息
            case 40	:	//	拜访亲友	拜访次数X次
            case 41	:	//	乘奔逐北	使用X次追杀令
            case 42	:	//	围剿乱党	围剿X波乱党
            case 44	:	//	丝路竞价	丝路竞价X关
            case 45	:	//	开启校场	开启X次校场
            case 46	:	//	觥筹交错	赴宴次数X次
            case 50:    //  在御膳房进行X次烹饪
            case 51:    // 上缴一键宝物
            case 53	:	//	出游达到x次
            case 54	:	//	开宴、赴宴达到多少次
            case 55	:	//	领取一次月卡/年卡
            case 56	:	//	充值任意金额
            case 57	:	//	元宝祈福一次
            case 58	:	//	进行整理
			case 59	:	//	徒弟游历
			case 60	:	//	抽卡
			case 100:   //  玩家换装
			case 101:   //  伙伴换装
			case 102:   //  伙伴培养X次
			case 103:   //  给伙伴送礼X次
			case 104:	//  伙伴资质升级X次
			case 105:	// 伙伴信物升级X次
			case 106:	//伙伴升星X次
			case 107:	// 心动时刻卡升级X次
			case 108:	// 升级四海奇珍X次
			case 109:	// 激活心动故事卡X张
			case 110:	// 激活四海奇珍X个
			case 111:	// 四海奇珍许愿X次
			case 112:	// 心动故事许愿X次
			case 113:	// 许愿树许愿X次
			case 115:   //收集X件不重复的服装
			case 116:   //任意冲榜活动获得X名之内
			case 117:   //办差X次
			case 118:   //伙伴总资质达到X
			case 119:   //伙伴好感度达到X
			case 120:   //收集X个不同的羁绊故事
			case 121:   //郊祀献礼累计获得X分
			case 122:   //参与廷斗X次
			case 123:   //激活x个伙伴信物
			case 124:   //累计登录天数
			case 125:  //累计伙伴数量
			case 127:  //收集灰鹞的X个羁绊故事
			case 128:  //收集南宫辰的羁绊故事
			case 129:  //收集蔺晟安的羁绊故事
			case 130:  //收集萧易的羁绊故事
			case 131:  //收集卫戢的羁绊故事
			case 132:  //收集纪延的羁绊故事
			case 133:  //灰鹞升到X星
			case 134:  //南宫辰升星
			case 135:  //萧易升星
			case 136:  //卫戢升星
			case 137:  //纪延升星
			case 138:  //蔺晟安升星
			case 139:  //参与郊祀献礼次数
			case 140:  //解锁X个灰鹞的信物
			case 141:  //解锁X个南宫辰的信物
			case 142:  //解锁X个萧易的信物
			case 143:  //解锁X个卫戢的信物
			case 144:  //解锁X个纪延的信物
			case 145:  //解锁X个蔺晟安的信物
			case 146:  //将X个卡牌升到满星
			case 147:  //将X个奇珍升到满星
			case 148:  //累计赴约次数
			case 149:  //卡牌升级多少次
			case 150:
			case 151:
			case 152:
			case 153:
			case 154:
			case 155:
			case 156:
			case 157:  //印痕升级多少次
			case 158:  //升华多少次
			case 159:  //公会捐献X次
			case 160:  //公会商店兑换X次
			case 161:  //帮会聊天X次

				$num = empty($this->info['info'][$type])?0:$this->info['info'][$type];
				break;
			case 4:  //初始门客升级	XX等级达X级 (华安等级达10级)
				$task_cfg = Game::getcfg_info('task_main',$this->info['id']);
				$heroid = $task_cfg['set'][0];
				$HeroModel = Master::getHero($this->uid);
				$hero_info = $HeroModel->check_info($heroid,true);
				$num = empty($hero_info['level'])?0:$hero_info['level'];
				break;
			case 5:   //升级门客	至少X名门客等级达X级	
				$task_cfg = Game::getcfg_info('task_main',$this->info['id']);
				$level = $task_cfg['set'][1];
				$HeroModel = Master::getHero($this->uid);
				foreach($HeroModel->info as $k => $v){
					if($v['level'] >= $level){
						$num ++;
					}
				}
				break;
			case 6:   //门客封爵	至少X名门客达到男爵	
				$task_cfg = Game::getcfg_info('task_main',$this->info['id']);
				$level = $task_cfg['set'][1];
				$HeroModel = Master::getHero($this->uid);
				foreach($HeroModel->info as $k => $v){
					if($v['senior'] >= $level){
						$num ++;
					}
				}
				break;
			case 7:   //累计关卡胜利	累计关卡战斗胜利X次	
				$fUserModel = Master::getUser($this->uid);
				$num = $fUserModel->info['bmap'] + $fUserModel->info['smap'] -1;
				break;
			case 8:   //势不可挡	势力达到X	
				$get_team = Master::get_team($this->uid);
				$num = $get_team['shili'];
//				$fuidData = Master::fuidData($this->uid);
//				$num = $fuidData['shili'];
				break;
			case 11:   //增添席位	扩建子嗣席位至X个	
				$Act12Model = Master::getAct12($this->uid);
				$num = $Act12Model->info['seat'];
				break;
			case 14:   //升官发财	官品等级达X品	
				$fUserModel = Master::getUser($this->uid);
				$num = $fUserModel->info['level'];
				break;
			case 33:   //加入联盟	成功加入联盟
				$Act40Model = Master::getAct40($this->uid);
				if($Act40Model->info['cid'] > 0){
					$num = 1;
				}
				break;
			case 36:   //增添席位	扩建书院席位至X个	
				$Act15Model = Master::getAct15($this->uid);
				$num = $Act15Model->info['desk'];
				break;
			case 37:   //联盟等级	所在联盟等级达X级	
				$Act40Model = Master::getAct40($this->uid);
				$cid = $Act40Model->info['cid'];
				if(!empty($cid)){
					$ClubModel = Master::getClub($cid);
					$num = $ClubModel->info['level'];
				}
				break;
            case 43:   //累计红颜达到X个
                $WifeModel = Master::getWife($this->uid);
                $num = empty($WifeModel->info)?0:count($WifeModel->info);
                break;
            case 47:   //打赢id为xxx的中关卡
                $task_cfg = Game::getcfg_info('task_main',$this->info['id']);
                $smap_id = $task_cfg['set'][1];
				$fUserModel = Master::getUser($this->uid);
                //$smap_cfg = Game::getcfg_info('pve_smap',intval($fUserModel->info['smap']) + 1);
                //$mmap = intval($smap_cfg['mmap']);
				if(intval($fUserModel->info['smap']) >= $smap_id){
                    $num = 1;
                }
				break;
			case 48:   //完成某个羁绊
                    $task_cfg = Game::getcfg_info('task_main',$this->info['id']);
                    $Act6000Model = Master::getAct6000($this->uid);
                    $status = $Act6000Model->isOver($task_cfg['set'][0]);
                    if($status){
                        $num = $task_cfg['set'][0];
                    }
             break;
            case 49:
                $task_cfg = Game::getcfg_info('task_main',$this->info['id']);
                $act29Model = Master::getAct29($this->uid);
                $isFirst = $act29Model->info['isFirst'];
                if($isFirst >= $task_cfg['set'][0]) {
                    $num = $task_cfg['set'][0];
                }
				break;
            case 52:    //完成奖励
                $act6001Model = Master::getAct6001($this->uid);
                $task_cfg = Game::getcfg_info('task_main',$this->info['id']);
                if($act6001Model->isOverGroup($task_cfg['set'][1])) {
                    $num = 1;
                }
                break;
            // case 60:    //观星阁

            // 	$CardModel = Master::getCard($this->uid);
	        //     $cardList = $CardModel->getCardList(true);
            // 	if (isset($this->info['info'][$type]) || count($cardList) > 0) {
            // 		$num = 1;
            // 	}
            //     break;
			default:
				Master::error('task_num_type_err'.$type);
		}
		return $num;
	}

	
	/**
	 * 获取目标参数的下标位置
	 * @param unknown_type $type
	 */
	public function task_max_key($type){
		
		$key = 0; //标识获取目标参数的下标位置
		switch($type){
			case 1:  //养精蓄锐	经营商产次数达X次	
			case 2:  //五谷丰登	经营农产次数达X次	
			case 3:  //兵甲富足	招募士兵次数达X次	
			case 5:   //升级门客	至少X名门客等级达X级	
			case 6:   //门客封爵	至少X名门客达到男爵	
		    case 7:   //累计关卡胜利	累计关卡战斗胜利X次	
			case 8:   //势不可挡	势力达到X	
			case 9:  //养儿育女	子嗣培养次数达X次	
			case 10:  //喜得贵子	子嗣数量达X个	
			case 11:   //增添席位	扩建子嗣席位至X个	
			case 12:  //处理政务	处理政务次数达X次	
			case 13:  //勤政爱民	累计使用1次政务令	
			case 14:   //升官发财	官品等级达X品	
			case 15:  //领取俸禄	皇宫请安次数达到X次	
			case 16:  //宠爱有加	宠幸红颜次数达X次	
			case 17	:	//	随机传唤	随机传唤红颜次数达X次
			case 18	:	//	赏赐红颜	红颜赏赐次数达X次
			case 19	:	//	膜拜大神	膜拜次数达X次（三榜均可膜拜）
			case 20	:	//	每日签到	累计签到X天
			case 21	:	//	培养门客	使用强化卷轴升级门客书籍X次
			case 22	:	//	喜结良缘	联姻次数达X次
			case 23	:	//	游历寻访	寻访次数达X次
			case 24	:	//	体力恢复	使用体力丹X次
			case 25	:	//	技能升级	门客技能升级次数达X次
			case 26	:	//	红颜技能	升级红颜技能X次
			case 27	:	//	衙门出使	衙门出使X次
			case 28	:	//	活力恢复	使用活力丹X次
			case 29	:	//	严惩贪官	惩罚犯人次数达X次
			case 30	:	//	书院学习	书院学习次数达到X次
			case 31	:	//	唯我独尊	衙门使用挑战书X次
			case 32	:	//	购买道具	商城购买任意道具X次
			case 33	:	//加入联盟	成功加入联盟
			case 34	:	//	联盟兑换	联盟兑换次数达X次	
			case 35	:	//	联盟建设	联盟建设次数达X次	
			case 36:   //增添席位	扩建书院席位至X个	
			case 37:   //联盟等级	所在联盟等级达X级

            case 38	:	//	拔旗易帜	使用令旗X次（任意令旗）
            case 39	:	//	跨服发言	跨服聊天发X条消息
            case 40	:	//	拜访亲友	拜访次数X次
            case 41	:	//	乘奔逐北	使用X次追杀令
            case 42	:	//	围剿乱党	围剿X波乱党
            case 43:    //  累计红颜达到X个
            case 44	:	//	丝路竞价	丝路竞价X关
            case 45	:	//	开启校场	开启X次校场
            case 46	:	//	觥筹交错	赴宴次数X次
            case 47:   //  打完id为xx的boss
            case 48:   //	完成某个羁绊
            case 49:   //  打完id为xx的特殊任务
            case 50:   //  做一次饭
            case 51:   //  上缴一件宝物
            case 52:   //完成奖励
            case 53:   //出游多少次
            case 54	:	//	开宴达到多少次
            case 55	:	//	领取一次月卡/年卡
            case 56	:	//	充值任意金额
            case 57	:	//	元宝祈福一次
            case 58	:	//	进行整理
            case 59	:	//	徒弟游历
			case 60	:	//	观星阁
			case 100:   //  玩家换装X次
			case 101:   //  伙伴换装X次
			case 102:   //  伙伴培养X次
			case 103:    // 伙伴送礼X次 
			case 104:	//  伙伴资质升级X次
			case 105:	// 伙伴信物升级X次
			case 106:	//伙伴升星X次
			case 107:	// 心动时刻卡升级X次
			case 108:	// 升级四海奇珍X次
			case 109:	// 升级四海奇珍X次
			case 110:	// 激活四海奇珍X个
			case 111:	// 四海奇珍许愿X次
			case 112:	// 心动故事许愿X次
			case 113:	// 许愿树许愿X次
			case 115:   //收集X件不重复的服装
			case 116:   //任意冲榜活动获得X名之内
			case 117:   //办差X次
			case 118:   //伙伴总资质达到X
			case 119:   //伙伴好感度达到X
			case 120:   //收集X个不同的羁绊故事
			case 121:   //郊祀献礼累计获得X分
			case 122:   //参与廷斗X次
			case 123:   //激活X个伙伴信物
			case 124:   //累计登录天数
			case 125:  //累计伙伴数量
			case 127:  //收集灰鹞的X个羁绊故事
			case 128:  //收集南宫辰的羁绊故事
			case 129:  //收集蔺晟安的羁绊故事
			case 130:  //收集萧易的羁绊故事
			case 131:  //收集卫戢的羁绊故事
			case 132:  //收集纪延的羁绊故事
			case 133:  //灰鹞升到X星
			case 134:  //南宫辰升星
			case 135:  //萧易升星
			case 136:  //卫戢升星
			case 137:  //纪延升星
			case 138:  //蔺晟安升星
			case 139:  //参与郊祀献礼次数
			case 140:  //解锁X个灰鹞的信物
			case 141:  //解锁X个南宫辰的信物
			case 142:  //解锁X个萧易的信物
			case 143:  //解锁X个卫戢的信物
			case 144:  //解锁X个纪延的信物
			case 145:  //解锁X个蔺晟安的信物
			case 146:  //将X个卡牌升到满星
			case 147:  //将X个奇珍升到满星
			case 148:  //累计赴约次数
			case 149:  //卡牌升级多少次
			case 150:
			case 151:
			case 152:
			case 153:
			case 154:
			case 155:
			case 156:
			case 157:  //印痕升级多少次
			case 158:  //升华多少次
			case 159:  //公会捐献X次
			case 160:  //公会商店兑换X次
			case 161:  //帮会聊天X次
				$key = 0;
				break;

			case 4:  //初始门客升级	XX等级达X级
				$key = 1;
				break;
			default:
				Master::error('task_key_err'.$type);
		}
		return $key;
	}
	
	/**
	 * 完成任务
	 * @param unknown_type $id  任务id
	 */
	public function task_do($id){
		if($this->info['id'] != $id){
			Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
			return true;
		}
		
		$num = self::task_num($this->info['type']);
		$key = $this->task_max_key($this->info['type']);

		$get_task_cfg = Game::getcfg_info('task_main',$this->info['id']);

        //判断是否完成任务
        if($num < $get_task_cfg['set'][$key]){
            Master::error(DAILY_UN_COMPLETE);
		}

		$Act700Model = Master::getAct700($this->uid);
		if($Act700Model->getOpenDay() < 8){
			$Act700Model->setSevenTask($get_task_cfg['type']);
		}

		//更新下一个任务
        $this->info['id'] = $get_task_cfg['nextid'];  //任务id+1
        $this->info['type'] = 0; //种类
        $this->info['max'] = 0;//目标数值

        if($this->info['id'] != 0){
            $task_cfg = Game::getcfg_info('task_main',$this->info['id']);
            if(empty($task_cfg)){
                Master::error(ACT_39_FAILUER);
            }
            $this->info['type'] = $task_cfg['type'];
            //目标参数
            $key = self::task_max_key($task_cfg['type']);
            $this->info['max'] = $task_cfg['set'][$key];
            //主线任务id大于950 接任务重置累计数值为0 (任务950前任务数值可累计)
            // if ($this->info['id'] >950  && isset($this->info['info'][$this->info['type']]) && $this->info['info'][$this->info['type']]>0){
            //     $this->info['info'][$this->info['type']] = 0;//接任务重置数值
            // }
		}
		$Act750Mdoel = Master::getAct750($this->uid);
		$Act750Mdoel->setIsPop(6,$this->info['id']);
		$this->save();
		
        Game::flow_php_record($this->uid, 9, $id, $this->info['id'],$task_cfg['name']);
		//加奖励
		if(!empty($get_task_cfg['rwd'])){
			foreach($get_task_cfg['rwd'] as $v){
				//添加道具
				$kind = empty($v['kind'])?1:$v['kind'];
				if($kind == 7){
					$HeroModel = Master::getHero($this->uid);
					$hero_info = $HeroModel->check_info($v['id'],true);
					if(!empty($hero_info)){
						break;
					}
				}
			    Master::add_item($this->uid,$kind,$v['id'],$v['count']);
			}
		}
		$this->add_type = 1;
	}
	
	/**
	 * 刷新任务  
	 * 如果是下面种类 更新给客户端
	 */
	public function task_refresh($type){
		if($type == $this->info['type'] ){
			switch($this->info['type']){
				case 4:  //初始门客升级	XX等级达X级 (华安等级达10级)
				case 5:   //升级门客	至少X名门客等级达X级	
				case 6:   //门客封爵	至少X名门客达到男爵	
				case 7:   //累计关卡胜利	累计关卡战斗胜利X次	
				case 8:   //势不可挡	势力达到X	
				case 11:   //增添席位	扩建子嗣席位至X个	
				case 14:   //升官发财	官品等级达X品	
				case 31	:	//	唯我独尊	衙门使用挑战书X次
				case 36:   //增添席位	扩建书院席位至X个	
				case 33	:	//加入联盟	成功加入联盟
				case 37:   //联盟等级	所在联盟等级达X级
                case 43:   //累计红颜达到X个
                case 47:   // 打完哪个bmap
                case 48:   //	完成某个羁绊
                case 49:   // 完成isFirst
                case 50:   // 做一次饭
                case 51:   // 上缴一件宝物
                case 52:   // 完成邮件
                case 53:   // 完成邮件
                case 54	:	//	开宴达到多少次
                case 55	:	//	领取一次月卡/年卡
                case 56	:	//	充值任意金额
                case 57	:	//	元宝祈福一次
                case 58	:	//	进行整理
                case 59	:	//	徒弟游历
                case 60	:	//	徒弟游历
					$this->make_out();
					Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
					$Act36Model = Master::getAct36($this->uid);
					$Act36Model->setTask($type);
					break;
			}
		}
		$Act700Model = Master::getAct700($this->uid);
		if($Act700Model->getOpenDay() < 8){
			$Act700Model->setSevenTask($type);
		}
	}
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
        //做兼容
        if($this->info['id'] == 0){
            if(empty($task_cfg)){
                Master::error(ACT_39_FAILUER);
            }
        }

		$this->outf = array();
		if(!empty($this->info['type']) || $this->info['id'] == 0){

			//获取当前数值
			$num = 0;
			$max = 0;
			if($this->info['id'] != 0){
				$num = self::task_num($this->info['type']);
                $task_cfg = Game::getcfg_info('task_main', $this->info['id']);
                //目标参数
                $key = self::task_max_key($task_cfg['type']);
                $max = $task_cfg['set'][$key];  //目标数值
			}

			//输出结构体
			$this->outf = array(
				'id'  => $this->info['id'],  //任务id
				'num' => $num,  //当前数值
				'max' => $max == 0?$this->info['max']:$max,  //目标数值
			);
		}
		//return $this->outf;
	}
	
	/*
	 * 返回活动信息
	 */
	public function back_data(){
		if( !empty($this->add_type)){
			Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
		}
	}
	/*
	 * 返回活动信息
	 */
	public function back_data_init(){
		Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
	}


	//高級号剧情直接通关
	public function set_final(){
		$this->info['id'] = 3200;
		$this->info['type'] = 14;
		$this->info['max'] = 32;
		$this->save();
	}
	
}

			

