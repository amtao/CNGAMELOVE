<?php
require_once "ActBaseModel.php";
/*
 * 跨服衙门-战斗信息
 */
class Act301Model extends ActBaseModel
{
	public $atype = 301;//活动编号
	
	public $comment = "衙门战-战斗信息";
	public $b_mol = "kuayamen";//返回信息 所在模块
	public $b_ctrl = "fight";//返回信息 所在控制器
	public $hd_id = "huodong_300";
	public $hd_cfg;
	
	public function __construct($uid,$hid){
	    Common::loadModel('HoutaiModel');
	    $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
	    if(!empty($this->hd_cfg)){
	        parent::__construct($uid,$this->hd_cfg['info']['id']);
	    }
	}
	
	
	/*
	 * 初始化结构体 //战斗信息 / 不重置
	 */
	public $_init =  array(
		//出战信息
		'hid' => 0,//战斗中门客 > 0  表示有英雄战斗中
		'ftype' => 0,//当前战斗类型0 未开战 1自动出战(包括出师令)\2复仇\3追杀
		'fuid' => 0,//当前对战目标玩家UID
		'rwd' => 0,//已经选择了几次奖励 = 击杀数/5
		'dead' => array(),//已击败的门客列表
		'fhids' => array(),//对战的3个门客
		'shop' => array(),//加成列表 id => 0,
		
		//战斗属性
		'ackadd' => 0,//攻击加成
		'skilladd' => 0,//技能加成
		'damge' => 0,//受到伤害血量
		'maxhp' => 0,//生命值上限
		'money' => 0,//衙门大力丸
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$f_user = array();//对方玩家信息
		$fheros = array();//对方门客信息
		$shop = array();//加成商铺信息
		if ($this->get_state() > 0){
			//对方玩家信息
			$f_user = Master::fuidInfo($this->info['fuid']);
			//对方积分
			$Redis306Model = Master::getRedis306($this->hd_cfg['info']['id']);
			$f_user['num'] = intval($Redis306Model->zScore($this->info['fuid']));
					
			//对方门客信息
			if(!empty($this->info['fhids'])){
				$fHeroModel = Master::getHero($this->info['fuid']);
				foreach ($this->info['fhids'] as $fhid){
					$fheros[] = $fHeroModel->getEasyBase_buyid($fhid);
				}
			}
			//加成商铺信息
			if (!empty($this->info['shop'])){
				foreach ($this->info['shop'] as $id => $stype)  
				$shop[] = array(
					'id' => $id,//购买类型ID
					'type' => $stype['buy'], //购买状态 0:未购买 1:已购买
					'add' => $stype['add'], //加成数值
				);
			}
			//我方英雄信息
			$TeamModel = Master::getTeam($this->uid);
			//获取战斗阵法信息
			$pvp_member = $TeamModel->get_pvp_buyid($this->info['hid']);
			$hp = $pvp_member['prop']['hp'] - $this->info['damge'];
			$this->info['maxhp'] = $pvp_member['prop']['hp'];
		}
		$Act129Model = Master::getAct129($this->info['fuid']);
		$banish_num = empty($Act129Model->info['list']) ? 0 : count($Act129Model->info['list']);
		$this->outf = array(
			'hid' => $this->info['hid'],//当前出战英雄
			'fuser' => $f_user,//对方阵营玩家信息
			'fheros' => $fheros,//当前对阵的3个门客信息
			'shop' => $shop,//商铺信息
			
			//战斗属性
			'fstate' => $this->get_fight_state(),//当前战斗状态
			'ackadd' => $this->info['ackadd'],//攻击加成
			'skilladd' => $this->info['skilladd'],//技能加成
			'hp' => empty($pvp_member)?0:$hp,//当前血量
			'hpmax' => empty($pvp_member)?0:$pvp_member['prop']['hp'],//血量上限
			'killnum' => count($this->info['dead']),//当前击败几个门客 
			'fheronum' => count($fHeroModel->info) - $banish_num,//地方门客总数-被放逐的门客数量
			'money' => $this->info['money'],//衙门大力丸
		);
	}
	
	/*
	 * 返回当前战斗类型
	 * 0: 非战斗中
	 * 1: 自动出战
	 * 2: 出师令
	 * 3: 复仇
	 * 4: 追杀
	 */
	public function get_state(){
		if ($this->info['hid'] > 0){
			if ($this->info['ftype'] == 0){
				Master::error('act301_get_state_err');
			}
			//战斗中 返回战斗类型  1自动 2出征 3挑战 4复仇 5追杀
			return $this->info['ftype'];
		}else{
			//休息中
			return 0;
		}
	}
	
	/*
	 * 返回战斗中状态
	 * 0 不在战斗中
	 * 等待杀人中 1
	 * 等待选择奖励中 2
	 */
	public function get_fight_state(){
		//是否战斗中
		if ($this->get_state() == 0){
			return 0;
		}
		
		//当前击败数量
		$kill_num = count($this->info['dead']);
		
		//如果连胜次数到达了 就要选择奖励了
		if (floor($kill_num/3) > $this->info['rwd']){
			return 2;
		}else{
			return 1;
		}
	}
	
	/*
	 * 设置出战 启动一场战斗
	 */
	public function start_fight($hid,$fuid,$type){
		
		//设置出战状态
		$this->info['hid'] = $hid;//出战门客ID
		$this->info['fuid'] = $fuid;//对战玩家UID
		$this->info['ftype'] = $type;//战斗类型
		
		//启动战斗 下一步骤 (归0数据)
		$this->info['rwd'] = 0;//领取奖励次数
		$this->info['dead'] = array();//已击败的门客列表
		//战斗属性
		$this->info['ackadd'] = 0;//攻击加成
		$this->info['skilladd'] = 0;//技能加成
		$this->info['damge'] = 0;//受到伤害血量
		//从对方门客列表里面 选出3个对手
		$this->rand_f_hero();//对战的3个门客//$this->info['fhids'] = array();
		
		//获取基础配置 第一次写死这3个
		$this->info['shop'] = array(
			1 => array(
				'add' => 50,//加成百分比
				'buy' => 0,//是否已购买
			),
			2 => array(
				'add' => 100,
				'buy' => 0,
			),
			3 => array(
				'add' => 150,
				'buy' => 0,
			),
		);//初始化 加成列表

		$this->save();
		
		//结束标记初始化
		Master::back_win("kuayamen","over","isover",0);
		
	}
	
	/*
	 * 战斗结束 清理战场
	 */
	public function fight_over($is_win){
		//战斗信息结算------
		//击杀数量
		$kill_num = count($this->info['dead']);
		
		//各个统计数据写入
		//连杀次数 ;
		//衙门分数增减
		$mscore = 0;//对方分数增减
		if ($is_win > 0){
			$mscore = $kill_num;
		}else{
			$mscore = $kill_num - 2;
		}
		
		//追杀 扣分加倍
		if ($this->info['ftype'] == 5){
			$mscore *= 2;
		}
		
		$mscore *= -1;
		
		//积分增减
		//合服积分全服
		$sid = Game::get_sevid($this->info['fuid']);
		$SevObj = Common::getSevCfgObj($sid);
		$Redis304Model = Master::getRedis304($this->hd_cfg['info']['id']);
		$Redis304Model->zIncrBy($SevObj->getHE(), $mscore);
		//合服总积分跨服
		$Redis305Model = Master::getRedis305($this->hd_cfg['info']['id']);
		$Redis305Model->zIncrBy($SevObj->getHE(), $mscore);
		//跨服总排行
		$Redis306Model = Master::getRedis306($this->hd_cfg['info']['id']);
		$Redis306Model->zIncrBy($this->info['fuid'], $mscore);
		//本服排行
		$Redis307Model = Master::getRedis307($this->hd_cfg['info']['id'].'_'.$SevObj->getHE());
		$Redis307Model->zIncrBy($this->info['fuid'], $mscore);

		//对方衙门流水
		$score = $Redis306Model->zScore($this->info['fuid']);

		$flowModel = new FlowModel($this->info['fuid'], 'kuayamen', 'fight', array('uid' => $this->uid));
		$flowModel->add_record(33, 1, $mscore, $score);
		$flowModel->destroy_now($sid);
		unset($flowModel);

		
		//进入对方防守信息表
		$fAct304Model = Master::getAct304($this->info['fuid']);
		$fAct304Model->add(array(
			'uid' => $this->uid,	//谁来打的我
			'hid' => $this->info['hid'],	//对方用什么门客打我
			'kill' => $kill_num,	//杀了我几个人
			'win' => $is_win,	//是不是全歼了
			'mscore' => $mscore,	//我的衙门分数变化情况
		));
		//超过5人 进入仇人列表
		if ($kill_num >= 5){
			$fAct305Model = Master::getAct305($this->info['fuid']);
			$fAct305Model->add($this->uid);
		}
		$Act300Model = Master::getAct300($this->uid);
		//超过20 进入日志榜
		if ($kill_num >= 20){
			$Sev60Model = Master::getSev60($this->hd_cfg['info']['id']);
			$Sev60Model->add_msg(array(
				'uid' => $this->uid,	//进攻方
				'fuid' => $this->info['fuid'],	//防守方
				'hid' => $this->info['hid'],	//使用门客打我
				'kill' => $kill_num,	//杀了我几个人
				'lkill' => $Act300Model->info['lkill'],	//连杀次数
				'win' => $is_win,	//是不是全歼了
				'ftype' => $this->info['ftype'],	//战斗类型 / (是否追杀)
			));
		}
		//胜利结算弹窗
		$over_win = array(
			'hid'	=> $this->info['hid'],
			'isover' => 1,
			'fuser' => Master::fuidInfo($this->info['fuid']),
			'killnum' => $kill_num,
			'win' => $is_win,
		);
		Master::$bak_data['a']["kuayamen"]['win']["over"] = $over_win;
		//清理战场-----
		$_ftype = $this->info['ftype'];//临时保存战斗类型
		//对战角色信息
		$this->info['hid'] = 0;	//战斗中门客
		$this->info['ftype'] = 0;	//当前战斗类型
		$this->info['fuid'] = 0;	//当前对战目标玩家UID
		//业务数据
		$this->info['rwd'] = 0;	//已经选择了几次奖励
		$this->info['dead'] = array();	//已击败的门客列表
		$this->info['fhids'] = array();	//对战的3个门客
		$this->info['shop'] = array();	//加成列表
		//战斗属性
		$this->info['ackadd'] = 0;	//攻击加成
		$this->info['skilladd'] = 0;	//技能加成
		$this->info['deamge'] = 0;	//受到伤害血量
		//衙门信息类 结束战斗 连杀次数
		$Act300Model = Master::getAct300($this->uid);
		$Act300Model->battle_complete($is_win,$kill_num,$_ftype);

		//刷新 20名日志表
		$Sev60Model = Master::getSev60($this->hd_cfg['info']['id']);
		$Sev60Model->list_click($this->uid);
	}
	
	/*
	 * 随机加成列表
	 */
	public function rand_add_list(){
		$this->outf['killnum'];
		
		//获取随机加成信息
		$cfg = Game::getcfg("yamen_buff");
		//构造 档次=> 加成类型概率 表
		$cfg_dc = array();
		foreach ($cfg as $v){
			$cfg_dc[$v['dc']][$v['id']] = $v['dcprob_100'];
		}
		
		//所需档次列表
		$need_dc = array(1,2,3);
		
		//构造3个档次
		$this->info['shop'] = array();
		foreach ($need_dc as $nd){
			//随机档次ID
			$id = Game::get_rand_key(100,$cfg_dc[$nd]);
			//随机加成数值
			$add_num = rand($cfg[$id]['add_min'],$cfg[$id]['add_max']);
			$this->info['shop'][$id] = array(
				'add' => $add_num,
				'buy' => 0,
			);
			//保存 略 放在外部
		}
	}
	
	/*
	 * 随机3个可选对战门客
	 * 如果返回空 则标示已经胜利
	 */
	public function rand_f_hero(){
		//对方门客列表
		$fHeroModel = Master::getHero($this->info['fuid']);
		//敌方所有英雄ID列表
		$all_heros = array_keys($fHeroModel->info);
		//剩余可出战列表
		$huo_heros = array_diff($all_heros,$this->info['dead']);
		//扣除发配的成员列表
		$Act129Model = Master::getAct129($this->info['fuid']);
		if(!empty($Act129Model->info['list'])){
			$huo_heros = array_diff($huo_heros,array_keys($Act129Model->info['list']));
		}
		if (empty($huo_heros)){
			//如果空 则全歼
			$this->info['fhids'] = $huo_heros;
			return ;
		}
		//设置
		$this->info['fhids'] = Game::array_rand($huo_heros,3);
	}
	
	/*
	 * 选择一个加成属性
	 */
	public function select_add($id){
		//当前是否可以选择加成
		if (empty($this->info['shop'])){
			Master::error(YAMUN_BONUS_STATUS_ERROR);
		}
		//当前是否已经选择过
		foreach ($this->info['shop'] as $v){
			if ($v['buy'] > 0){
				Master::error(YAMUN_BONUS_SHOP_BUYED);
			}
		}
		//是否有当前档次
		if (empty($this->info['shop'][$id])){
			Master::error('ID_err_'.$id);
		}
		
		//执行加成
		
		//加成配置
		$cfg = Game::getcfg("yamen_buff");
		
		//扣除资源
		Master::sub_item2($cfg[$id]['need']);
		
		//设置为已购买
		$this->info['shop'][$id]['buy'] = 1;
		
		//加上加成项
		switch ($cfg[$id]['type']){
			case 1://血量加成
				//生命值增加幅度
				$hp_add = round($this->info['maxhp'] * $this->info['shop'][$id]['add'] / 100);
				$this->info['damge'] -= $hp_add;
				$this->info['damge'] = max(0,$this->info['damge']);
				break;
			case 2://技能加成
				$this->info['skilladd'] += $this->info['shop'][$id]['add'];
				break;
			case 3://攻击加成
				$this->info['ackadd'] += $this->info['shop'][$id]['add'];
				break;
			default:
				Master::error(ACT_61_JIACHENG.$cfg[$id]['type']);
		}
		$this->save();
	}
	
	/*
	 * 选择一个对手门客 进行战斗
	 */
	public function select_fhid($hid){
		$state = $this->get_fight_state();
		//当前状态对不对
		if ($state == 2){
			Master::error(YAMUN_PLAY_GIFT);
		}elseif($state == 0){
			Master::error(YAMUN_PLAY_END);
		}
		
		//选择门客合法
		if (!in_array($hid,$this->info['fhids'])){
			Master::error(ACT_61_IDWRONG);
		}
		
		
		//构造门客战斗数据
		//对方门客信息
		$fTeamModel= Master::getTeam($this->info['fuid']);
		$fmember = $fTeamModel->get_pvp_buyid($hid);
		//我方门客信息
		$TeamModel= Master::getTeam($this->uid);
		$member = $TeamModel->get_pvp_buyid($this->info['hid']);
		//使用加成信息
		$member['prop']['attack'] += round($member['prop']['attack']*$this->info['ackadd']/100);
		$member['prop']['bprob'] += round($member['prop']['bprob']*$this->info['skilladd']/100);
		$member['prop']['bhurt'] += round($member['prop']['bhurt']*$this->info['skilladd']/100);
		//扣去受伤血量
		$member['prop']['hp'] -= $this->info['damge'];
		$member['base']['hp'] -= $this->info['damge'];
		//等级加入战斗中
		$member['prop']['level'] = $member['base']['level'];
		$fmember['prop']['level'] = $fmember['base']['level'];
		//执行战斗
		$members = array(
			0 => $member['prop'],
			1 => $fmember['prop'],
		);
		
		$log = $this->do_pk($members);

		$Redis306Model = Master::getRedis306($this->hd_cfg['info']['id']);
		$score = $Redis306Model->zScore($this->uid);
		//胜负
		$fight_over = 0;//战斗是否结束
		$is_win = 0;//胜利标签
		if ($log['win'] == 0){//胜利
			$is_win = 1;
			//加入击毙队列
			$this->info['dead'][] = $hid;

			$add_score = 2;
			//如果是追杀
			if ($this->info['ftype'] == 5){
				$add_score *= 2;
			}
			
			//衙门分数+2
			Master::add_item($this->uid,2,38,$add_score,"kuayamen","fight");

			//添加流水
			Game::cmd_flow(33, 1, $add_score, $score+$add_score);
			//书籍经验+2
			Master::add_item($this->uid,5,$this->info['hid'],2,"kuayamen","fight");
			//衙门精力+1
			Master::add_item($this->uid,2,6,1,"yamen","fight");
			
			//重置3个目标
			$this->rand_f_hero();
			
			//是否全歼
			if (empty($this->info['fhids'])){
				//如果处于选择加成的回合 不结束游戏
				$kill_num = count($this->info['dead']);
				if ($kill_num%3 != 0){
					//战斗结束标记
					$fight_over = 1;
				}else{
					//等选择完加成 再判断一下战斗是否结束
				}
			}
			
			//重置加成商店
			$this->rand_add_list();
		}else{
			//失败
			$is_win = 0;
			$add_score = -1;
			//如果是追杀
			if ($this->info['ftype'] == 5){
				$add_score *= 2;
			}
			//衙门分数+2
			Master::add_item($this->uid,2,38,$add_score,"kuayamen","fight");

			//衙门流水
			Game::cmd_flow(33, 1, $add_score, $score+$add_score);
			//战斗结束标记
			$fight_over = 1;
		}
		
		//战斗弹窗
		//扣去受伤血量
		$base_member = array(
			0 => $member['base'],
			1 => $fmember['base'],
		);
		Master::back_win("kuayamen","fight","base",$base_member);
		Master::back_win("kuayamen","fight","log",$log['log']);
		Master::back_win("kuayamen","fight","win",$is_win);
		$kill_num = count($this->info['dead']);
		Master::back_win("kuayamen","fight","winnum",$kill_num);
		Master::back_win("kuayamen","fight","nrwd",3-$kill_num%3);
		
		//战斗结束
		if ($fight_over){
			//清理战场
			$this->fight_over($is_win);
		}
		//保存
		$this->save();
	}
	
	/*
	 * 执行战斗
	 * 
	 * 'hpmax' => $hp,//生命值上限
		'hp' => $hp,//生命值
		'attack' => $attack,//攻击力
		'bprob' => $b_prop,//暴击概率
		'bhurt' => $b_hurt,//暴击伤害
			
	 */
	public function do_pk($members){
		//$a_id = 0;//出手者
		//$b_id = 0;//防御者
		
//		$a_id = rand(0,1);//先手逻辑?
//		//$a_id = 1;//主动方先手
//		$b_id = ($a_id+1)%2;//防御者
		$rand_num = rand(1,$members[0]['level']+$members[1]['level']);
		if($rand_num <= $members[0]['level']){
			$a_id = 0;
			$b_id = 1;
		}else{
			$a_id = 1;
			$b_id = 0;
		}
		
		$hit = 0;
		
		//战斗循环
		$log = array();//战斗日志
		$win = $a_id;//胜利方
		for ($i = 1 ; $i < 100 ; $i++){
			//伤害值
			$damge = 0;
			$dtype = 0;//效果0无 1 暴击
			
			//出手方数据
			$a_mb = &$members[$a_id];
			//防守方数据
			$b_mb = &$members[$b_id];
			if ($a_mb['attack'] < 1000){
				$damge = $a_mb['attack'] + rand(1,100);
			}else{
				$damge = round($a_mb['attack'] * rand(90,110) / 100);
			}
			//暴击概率
			if (rand(1,10000) < $a_mb['bprob']){
				//暴击伤害
				$damge = round($damge * $a_mb['bhurt'] / 100);
				$dtype = 1;//效果:暴击
			}
			//扣血
			$b_mb['hp'] -= $damge;
			
			if($a_id == 1){
				$hit += $damge;
			}
			$log[] = array(
				'aid' => $a_id,//出手方
				'damge' => $damge,
				'type' => $dtype,
			);
			
			//死亡判定
			if ($b_mb['hp'] <= 0){
				$win = $a_id;
				break;
			}

			//转换攻防
//			$a_sy = $a_mb['epnum']-$i-1;
//			$b_sy = $b_mb['epnum']-$i-1;
//			if($a_sy < 0 || ($a_sy >= 0 && $b_sy >= 0)){
//				$a_id = $b_id;
//				$b_id = ($a_id+1)%2;//防御者
//			}
			$a_id = $b_id;
			$b_id = ($a_id+1)%2;//防御者
		}
		
		$this->info['damge'] += $hit;
		
		return array(
			'win' => $win,//胜利方
			'log' => $log,
		);
	}
	
	/*
	 * 选择一个奖励
	 */
	public function rand_rwd(){
		$state = $this->get_fight_state();
		//当前状态对不对
		if ($state == 1){
			Master::error(YAMUN_CHALLENGING);
		}elseif($state == 0){
			Master::error(YAMUN_PLAY_END);
		}
		
		//奖励配置
		$cfg = Game::getcfg("yamen_rwd");//配置有7个档次..
		$id = Game::get_rand_key(100,$cfg,'prob_100');
		$rwd_info = $cfg[$id]['rwd'];
		
		/*
		 * array (
	      'kind' => 2,
	      'item' => 18,
	      'num' => 4,
	    ),
		 */
		
		$rwd = null;
		if ($rwd_info['kind'] == 2){//如果是枚举类型
			switch($rwd_info['id']){
				case 17://书籍经验
					$kind = 5;
					break;
				case 18://技能经验
					$kind = 6;
					break;
				default:
					Master::error(ACT_61_WEIZHIDAOJU.$rwd_info['item']);
					break;
			}
			//构造奖励
			$rwd = array(
				'kind' => $kind,
				'id' => $this->info['hid'],
				'count' => $rwd_info['count'],
			);
		}elseif($rwd_info['kind'] == 1){
			$rwd = $rwd_info;
		}else{
			Master::error(ACT_61_WEIZHILEIXING.$rwd_info['kind']);
		}
		//加上选择的奖励
		Master::add_item2($rwd,"kuayamen","rwd");
		//返回奖励弹窗
		$jiade = array();
		$jiade[] = $rwd_info;
		
		//随机现在其他5个奖励信息 一起返回为弹窗
		$ph_arr = Game::array_rand($cfg,5);
		foreach ($ph_arr as $v){
			$jiade[] = $v['rwd'];
		}
		//返回假的奖励弹窗
		Master::back_win("kuayamen","rwd","jiade",$jiade);
		//领奖次数+1 
		$this->info['rwd']+=1;
		//判断是否战斗结束(全歼)
		if (empty($this->info['fhids'])){
			$this->fight_over(1);
		}
		$this->save();
	}
	
	/*
	 * 加上大力丸
	 */
	public function add_money($num){
		$this->info['money'] += $num;
		$this->save();
	}
	/*
	 * 减去大力丸
	 */
	public function sub_money($num){
		if ($this->info['money'] < $num){
			Master::error(YAMUN_BONUS_SCORE_SHORT);
		}
		$this->info['money'] -= $num;
		$this->save();
	}
	
	public function back_data(){
	    if(!empty($this->outf)){
	        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
	    }
	}
}
















