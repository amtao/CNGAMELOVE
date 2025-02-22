<?php
//排行榜
class JiulouMod extends Base
{
    public function __construct($uid)
    {
        parent::__construct($uid);

        $UserModel = Master::getUser($this->uid);
        $flag = Game::is_limit_level('jiulou', $this->uid, $UserModel->info['level']);
//        if ($flag == 2 && $UserModel->info['level'] < 5) {
//            //默认限制从7开启
//            Master::error(BOITE_NO_OPEN);
//        }
    }
	/**
	 * 酒楼信息
	 * @param $params
	 */
	public function jlInfo($params){
		//个人宴会信息
		$Act50Model = Master::getAct50($this->uid);
		$data = array();
		
		$outf = $Act50Model->get_outf();
		$data['type'] = $Act50Model->info['type'];
		if( Game::is_over($outf['ltime']['next'])){
			$Act50Model->info['type'] = 0;
		}
		Master::back_data($this->uid,'jiulou','yhType',$data);
		//宴会--赴会次数
		$Act55Model = Master::getAct55($this->uid);
		$Act55Model->back_data();
		
		//获取联盟人员可见宴会
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		$yhshow = array();
		
		//官宴 获取全服可见宴会
		$Sev21Model = Master::getSev21();
		$outf = $Sev21Model->get_outf();
		if(!empty($outf)){
			foreach($outf as $v){
				if($v['uid'] == $this->uid){
					continue;
				}
				$yhshow[] = $v;
			}
		}
		
		//联盟
		if(!empty($cid)){
			$Sev20Model = Master::getSev20($cid);
			$outf = $Sev20Model->get_outf();
			if(!empty($outf)){
				foreach($outf as $v){
					if($v['uid'] == $this->uid){
						continue;
					}
					$yhshow[] = $v;
				}
			}
			
		}
		
		//家宴公开联盟可见
		$Sev29Model = Master::getSev29();
		$outf = $Sev29Model->get_outf();
		if(!empty($outf)){
			foreach($outf as $v){
				if($v['uid'] == $this->uid){
					continue;
				}
				$yhshow[] = $v;
			}
		}
		
		//截断只剩6个
		$yhshow = array_slice($yhshow,0,6);
		
		Master::back_data($this->uid,'jiulou','yhshow',$yhshow);
		
		//消息信息-仇人
		$Act53Model = Master::getAct53($this->uid);
		$Act53Model->back_data();
		
		//消息信息-我的历史宴会
		$Act52Model = Master::getAct52($this->uid);
		$Act52Model->back_data();
		
		//消息信息-来宾统计
		$Redis21Model = Master::getRedis21($this->uid);
		$Redis21Model->back_data();
		
		//获取商店列表信息
		$Act51Model = Master::getAct51($this->uid);
		$Act51Model->back_data();
		//商店列表刷新信息
		$Act54Model = Master::getAct54($this->uid);
		$Act54Model->back_data();
		
	}
	
	/**
	 * 编号赴会-查询
	 * @param $params
	 * $params['fuid'] : 玩家id
	 */
	public function yhFind($params){
		$fuid = Game::intval($params,'fuid');
		if(empty($fuid)){
			Master::error(BOITE_ATTEND_NO_FIND_OWNER);
		}
		//是否合服范围内
		Game::isHeServerUid($fuid);
		//UID合法
		Master::click_uid($fuid);
		$Act50Model = Master::getAct50($fuid);
		
		if(empty($Act50Model->info['type'])){
			Master::error(BOITE_NO_FEAST);
		}
		//获取配置
		$outf = $Act50Model->get_outf();
		if( Game::is_over($outf['ltime']['next'])){
			Master::error(BOITE_FEAST_END);
		}
		
		//已占席位个数
		$xiwei = 0;
		foreach($Act50Model->info['list'] as $k => $v){
			if(empty($v['uid'])){
				continue;
			}
			$xiwei ++;
		}
		$yanhui_cfg = Game::getcfg_info('jl_yanhui',$outf['id']);
		//获取配置
		$data = array(
			'yhname' => $yanhui_cfg['name'],
			'fname' => $outf['name'],
			'xiwei' => $xiwei,
			'maxXiWei' => count($Act50Model->info['list']),
			'ltime' => array(
				'next' => $outf['ltime']['next'],//下次绝对时间
				'label' => 'jlyhltime',
			),
		);
		
		//输出基础信息
		Master::back_data($this->uid,'jiulou','yhBaseInfo',$data);
	}
	
	/**
	 * 编号赴会-前往
	 * @param $params
	 * $params['fuid'] : 玩家id
	 */
	public function yhGo($params){
		$fuid = Game::intval($params,'fuid');
		if(empty($fuid)){
			Master::error(PARAMS_ERROR);
		}
		
		//是否合服范围内
		Game::isHeServerUid($fuid);
		
		$Act50Model = Master::getAct50($fuid);
		
		if($fuid == $this->uid){
			$outDate = $Act50Model->clear_show();
			//弹窗 
			if(!empty($outDate['list'])){
				Master::$bak_data['a']['jiulou']['win']['yhnew'] = $outDate;
				//处理消息信息---------------
				foreach($outDate['list'] as $info){
					//消息-仇人信息  (有放老鼠)
					if($info['score'] < 0){
						$Act53Model = Master::getAct53($this->uid);
						$Act53Model-> add_bad($info['id']);
					}
					//消息-来宾统计 
					$Redis21Model = Master::getRedis21($this->uid);
					$Redis21Model->zIncrBy($info['id'],$info['score']);  //来宾统计
					$Redis21Model->back_data();
				}
			}
			//(宴会结束)
			if($outDate['isover']){
				//我的历史宴会  
				$Act52Model = Master::getAct52($this->uid);
				$type = $Act50Model->info['type'];
				$allScore = $Act50Model->info['score'];
				$ctime = $Act50Model->info['ctime'];
				$num = 0;
				foreach ($Act50Model->info['list'] as $lv){
				    //过滤空席位
                    if(empty($lv['uid'])){
                        continue;
                    }
                    $num += 1;
                }

				$Act52Model->add_yanhui($type,$allScore,$outDate['bad'],$ctime,$num);
				//我的积分计算
				if($allScore > 0){
					$Act51Model = Master::getAct51($this->uid);
					$Act51Model->add_score($allScore);
				}
				//个人宴会清空
				$Act50Model = Master::getAct50($this->uid);
				$Act50Model->close_yh();
				
				$data = array();
				$data['type'] = $Act50Model->info['type'];
				Master::back_data($this->uid,'jiulou','yhType',$data);

				//全服排行
				$Redis20Model = Master::getRedis20();
				$Redis20Model->zIncrBy($this->uid,$allScore);  //来宾统计
				
				$HuodongModel = Master::getHuodong($this->uid);
				$HuodongModel->chongbang_huodong('huodong256',$this->uid,$allScore);
				
				//活动消耗 - 限时酒楼积分涨幅
				$HuodongModel = Master::getHuodong($this->uid);
				$HuodongModel->xianshi_huodong('huodong225',$allScore);
			}
			
		}else{
			//获取配置
			$outf = $Act50Model->get_outf();
			if(empty($outf['ltime']['next'])){
				Master::error(BOITE_NO_FEAST);
			}
			if( Game::is_over($outf['ltime']['next'])){
				Master::error(BOITE_FEAST_END);
			}
			$data = $Act50Model->get_outf();
			if(empty($data['id'])){
				Master::error(BOITE_FEAST_END);
			}
			Master::back_data($this->uid,'jiulou','yhInfo',$data);
		}
		
	}
	
	/**
	 * 吃宴会
	 * @param unknown_type $params
	 * $params['fuid'] :  谁开的宴会
	 * $params['xwid'] :  席位id
	 * $params['type'] :  1:100礼金  2:500 礼金   3:礼盒  4:老鼠
	 */
	public function yhChi($params){
		$fuid = Game::intval($params,'fuid');
		$xwid = Game::intval($params,'xwid');
		$type = Game::intval($params,'type');
		
		//是否合服范围内
		Game::isHeServerUid($fuid);
		
		$Act50Model = Master::getAct50($fuid);
		//是否已过期/结束
		$Act50Model->check_yh();
		//占席位
		$Act50Model->add_xiwei($xwid,$this->uid,$type);
		
		//积分处理
		$score = 0;
		switch($type){
			case 1:
				Master::sub_item($this->uid,KIND_ITEM,1,100);
				$score = 100;
				if(rand(1,100) <= 50){
					$clid = rand(141,142);
					Master::add_item($this->uid,KIND_ITEM,$clid,1);
				}
				break;
			case 2:
				Master::sub_item($this->uid,KIND_ITEM,1,500);
				$score = 500;
				if(rand(1,100) <= 50){
					$clid = rand(143,144);
					Master::add_item($this->uid,KIND_ITEM,$clid,1);
				}
				break;
			case 3:
				Master::sub_item($this->uid,KIND_ITEM,145,1);
				$score = 1000;
				if(rand(1,100) <= 70){
					$clid = rand(143,144);
					Master::add_item($this->uid,KIND_ITEM,$clid,1);
				}
				break;
			case 4:
				Master::sub_item($this->uid,KIND_ITEM,146,1);
				$score = -1000;
				break;
			default:
				Master::error('type_err'.$type);
		}
		
		//给自己加积分
		Master::add_item($this->uid,KIND_OTHER,8,abs($score));
		
		//输出
		$this->outf = $Act50Model->get_outf();
		Master::back_data($this->uid,'jiulou','yhInfo',$this->outf);
		
		//扣除参加次数
		$Act55Model = Master::getAct55($this->uid);
		$Act55Model->sub_fynum();
		
		//活动消耗 - 限时赴宴次数
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->xianshi_huodong('huodong222',1);
		
        // 主线任务 参加宴会一次
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(54, 1);

//        //主线任务 ---   赴宴次数X次
//        $Act39Model = Master::getAct39($this->uid);
//        $Act39Model->task_add(46, 1);
		
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->chongbang_huodong('huodong256',$this->uid,abs($score));
		
		//活动消耗 - 限时酒楼积分涨幅
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->xianshi_huodong('huodong225',abs($score));

        //双旦活动道具产出
        $Act292Model = Master::getAct292($this->uid);
        $Act292Model->chanChu(3);

        //活动293 - 获得骰子-处理政务
        $Act293Model = Master::getAct293($this->uid);
        $Act293Model->get_touzi_task(6,1);

        //活动296 - 挖宝锄头-每日任务
        $Act296Model = Master::getAct296($this->uid);
        $Act296Model->get_chutou_task(6,1);
		
	}

	
	/**
	 * 商店积分兑换-兑换
	 * @param unknown_type $params
	 * $params['id'] :  物品标识id
	 */
	public function shopChange($params){
		$id = Game::intval($params,'id');
		$Act51Model = Master::getAct51($this->uid);
		$Act51Model->shop_buy($id);
	}
	
	/**
	 * 商店-刷新
	 * @param unknown_type $params
	 */
	public function shopRefresh($params){
		$Act54Model = Master::getAct54($this->uid);
		//扣除刷新次数
		$Act54Model->sub_fnum();
		//刷新列表所需费用
		Master::sub_item($this->uid,KIND_ITEM,1,$Act54Model->info['fcost']);
		//获取刷新列表
		$Act51Model = Master::getAct51($this->uid);
		$Act51Model->refresh_list();
	}
	
	/**
	 * 举办宴会
	 * @param unknown_type $params
	 * $params['type'] :  1:家宴  2:官宴
	 */
	public function yhHold($params){
		$type = Game::intval($params,'type');
		
		//家宴 是否公开宴会 默认0:不公开   1:公开
		$isOpen = Game::intval($params,'isOpen'); 
		if(empty($isOpen)){
			$isOpen = 0;
		}
		
		$Act50Model = Master::getAct50($this->uid);
		$Act50Model->open_yh($type,$isOpen);
		
		$data = array();
		$data['type'] = $Act50Model->info['type'];
		Master::back_data($this->uid,'jiulou','yhType',$data);

        //创建帮会-跑马灯
        $UserInfo = Master::fuidInfo($this->uid);
        $Sev91Model = Master::getSev91();
        $Sev91Model->add_msg(array(108,Game::filter_char($UserInfo['name']),$type ));

	}
	
	/**
	 * 消息信息-我的宴会
	 * @param unknown_type $params
	 */
	public function xxInfo($params){
		$Act50Model = Master::getAct50($this->uid);
		$Act50Model->back_data();
	}
	
	/**
	 * 酒楼-排行榜
	 * @param unknown_type $params
	 */
	public function jlRanking($params){
		$Redis20Model = Master::getRedis20();
		//全服排行
		$Redis20Model->back_data();
		//我的排行
		$Redis20Model->back_data_my($this->uid);
	}
}











