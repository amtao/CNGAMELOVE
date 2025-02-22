<?php
require_once "ActBaseModel.php";
/*
 * 衙门-战斗信息
 */
class Act61Model extends ActBaseModel
{
	public $atype = 61;//活动编号
	
	public $comment = "衙门战-战斗信息";
	public $b_mol = "yamen";//返回信息 所在模块
	public $b_ctrl = "fight";//返回信息 所在控制器
	public $onekey_rwd = array();
	public $onkey_kill = 0;
	public $onekey_kill_all = 0;
	protected $onekey_special_rwd = array();//存放特别道具 等结束后再同一发放
	
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
			$Redis6Model = Master::getRedis6();
			$f_user['num'] = $Redis6Model->zScore($this->info['fuid']);
			
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
			'fheronum' => count($fHeroModel->info)-$banish_num,//地方门客总数-放逐的门客数量
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
				Master::error('act61_get_state_err');
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
		//当前选择了几次奖励
		//$this->info['rwd'];
		
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
		//初始化数据? 不能全部初始化 衙门大力丸会没掉
		
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
		//money 不变
		
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
		Master::back_win("yamen","over","isover",0);
		
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
		Common::loadModel('SwitchModel');

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
		$Redis6Model = Master::getRedis6();//对方衙门分数增减

        //对方衙门流水
        $score = $Redis6Model->zScore($this->info['fuid']);
        //衙门冲榜流水
        $fAct254Model = Master::getAct254($this->info['fuid']);
        $cbscore = $fAct254Model->get_cbscore();
        $flowModel = new FlowModel($this->info['fuid'], 'yamen', 'fight', 'b_attack');
        $flowModel->add_record(20, '1_'.$cbscore, $mscore, $score+$mscore);
        $flowModel->destroy_now(Game::get_sevid($this->info['fuid']));
        unset($flowModel);

		$Redis6Model->zIncrBy($this->info['fuid'],$mscore);
		//进入对方防守信息表
		$fAct62Model = Master::getAct62($this->info['fuid']);
		$fAct62Model->add(array(
			'uid' => $this->uid,	//谁来打的我
			'hid' => $this->info['hid'],	//对方用什么门客打我
			'kill' => $kill_num,	//杀了我几个人
			'win' => $is_win,	//是不是全歼了
			'mscore' => $mscore,	//我的衙门分数变化情况
		));
		//超过5人 进入仇人列表
		if ($kill_num >= 5){
			$fAct63Model = Master::getAct63($this->info['fuid']);
			$fAct63Model->add($this->uid);
		}
		$Act60Model = Master::getAct60($this->uid);
		//超过20 进入日志榜
		if ($kill_num >= Game::getcfg_param("gongdou_shangbang_id")){
			$dkill = $Act60Model->get_day_kill($this->info['fuid']);
			$Sev6Model = Master::getSev6();
			$Sev6Model->add_msg(array(
				'uid' => $this->uid,	//进攻方
				'fuid' => $this->info['fuid'],	//防守方
				'hid' => $this->info['hid'],	//使用门客打我
				'kill' => $kill_num,	//杀了我几个人
				'lkill' => $Act60Model->info['lkill'],	//连杀次数
				'dkill' => $dkill+1,//今日击杀该玩家次数
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
		Master::$bak_data['a']["yamen"]['win']["over"] = $over_win;
		//清理战场-----
		$_ftype = $this->info['ftype'];//临时保存战斗类型
		$fuid = $this->info['fuid'];//临时保存被打的用户id
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
		$Act60Model = Master::getAct60($this->uid);
		$Act60Model->battle_complete($is_win,$kill_num,$_ftype,$fuid);
		
		//刷新 20名日志表
		$Sev6Model = Master::getSev6();
		$Sev6Model->list_click($this->uid);
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
		//已经死的列表
		//$this->info['dead']
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
		$killnum = count($this->info['dead']);//已击杀人数
		if(count($huo_heros) > 12 && ($killnum+1)%3==0){//当活的门客人数大于12个时并且当前是3的倍数关卡
			$TeamModel = Master::getTeam($this->info['fuid']);
			$heroList = $TeamModel->get_heroallep_list();
			if(empty($heroList)) Master::error(HERO_POP_ERR);
			//正常来讲huo_hero里面的门客 herolist都有
			foreach ($heroList as $hid => $allepp){//留下存活的属性信息
				if(!in_array($hid,$huo_heros)){
					unset($heroList[$hid]);
				}
			}
			arsort($heroList);
			$heroList = array_slice($heroList,0,12,true);
			$heroidList = array_keys($heroList);
			$this->info['fhids'] = Game::array_rand($heroidList,3);
		}else{
			$this->info['fhids'] = Game::array_rand($huo_heros,3);
		}
		//保存 略 放在外部
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
		
		//衙门信息类
		$Act60Model= Master::getAct60($this->uid);

        $Redis6Model = Master::getRedis6();
        $score = $Redis6Model->zScore($this->uid);

		//胜负
		$fight_over = 0;//战斗是否结束
		$is_win = 0;//胜利标签
		$itemid = Game::getcfg_param("gongdou_itemid");
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
			Master::add_item($this->uid,2,9,$add_score,"yamen","fight");
			//衙门冲榜流水
			$Act254Model = Master::getAct254($this->uid);
        	$cbscore = $Act254Model->get_cbscore();
            Game::cmd_flow(20, $this->info['fuid'].'_'.$cbscore, $add_score, $score+$add_score);
			//书籍经验+2
			Master::add_item($this->uid,5,$this->info['hid'],2,"yamen","fight");
			//衙门精力+1
			Master::add_item($this->uid,2,6,1,"yamen","fight");
			$itemCount = Game::getcfg_param("gongdou_win_num");
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
            //衙门分数-1
            Master::add_item($this->uid,2,9,$add_score,"yamen","fight");
            //衙门流水
            //衙门冲榜流水
            $Act254Model = Master::getAct254($this->uid);
            $cbscore = $Act254Model->get_cbscore();
			Game::cmd_flow(20, $this->info['fuid'].'_'.$cbscore, $add_score, $score+$add_score);
			$itemCount = Game::getcfg_param("gongdou_lose_num");
			//战斗结束标记
			$fight_over = 1;
		}
		Master::add_item($this->uid,KIND_ITEM,$itemid,$itemCount,"yamen","fight");
		
		//战斗弹窗
		//扣去受伤血量
		$base_member = array(
			0 => $member['base'],
			1 => $fmember['base'],
		);
		Master::back_win("yamen","fight","base",$base_member);
		Master::back_win("yamen","fight","log",$log['log']);
		Master::back_win("yamen","fight","win",$is_win);
		$kill_num = count($this->info['dead']);
		Master::back_win("yamen","fight","winnum",$kill_num);
		Master::back_win("yamen","fight","nrwd",3-$kill_num%3);
		
		/*
		$fight_win = array(
			//阵营信息
			'base' => array(
				0 => $members[0]['base'],
				1 => $members[1]['base'],
			),
			//战斗日志/胜负信息
			'log' => $log,
			'winnum' => $kill_num,//连胜次数
			'nrwd' => $kill_num%3,//再打n场获得连胜奖励
		);
		*/
		
		//战斗结束
		if ($fight_over){
			//清理战场
			//结算前先向被打的用户加锁
			Master::get_lock_special('yamen','fight',$this->info['fuid']);
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
		'epnum' =>  count($info['epskill']),//资质技能数量
		'level' => $level //门客等级
	 */
	public function do_pk($members){
//		$a_id;//出手者
//		$b_id;//防御者
		$rand_num = rand(1,$members[0]['level']+$members[1]['level']);
		if($rand_num <= $members[0]['level']){
			$a_id = 0; // 我先
			$b_id = 1; // 对手先
		}else{
			$a_id = 1;
			$b_id = 0;
		}
//		$a_id = rand(0,1);//先手逻辑?
//		//$a_id = 1;//主动方先手
//		$b_id = ($a_id+1)%2;//防御者
		
		$hit = 0;
		
		//战斗循环
		$log = array();//战斗日志
		$win = $a_id;//胜利方
        $dead = false;
		for ($i = 1 ; $i < 200 ; $i++){
			//伤害值
			$damge = 0;
			$dtype = 0;//效果0无 1 暴击
			
			//出手方数据
			$a_mb = &$members[$a_id];
			//防守方数据
			$b_mb = &$members[$b_id];

			$damge = round($a_mb['attack'] * rand(90,110) / 100);

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
				$dead = true;
				break;
			}
			
			//转换攻防
//			$a_sy = $a_mb['epnum']-$i-1; 需前端配合
//			$b_sy = $b_mb['epnum']-$i-1;
//			if($a_sy < 0 || ($a_sy >= 0 && $b_sy >= 0)){
//				$a_id = $b_id;
//				$b_id = ($a_id+1)%2;//防御者
//			}
			$a_id = $b_id;
			$b_id = ($a_id+1)%2;//防御者

		}
		if(!$dead){//回合结束没有门客死亡，判定双方血量
            $win = $members[$a_id]['hp'] > $members[$b_id]['hp']?$a_id:$b_id;
            Game::cmd_flow(99, 'fight_not_dead', $members[0]['hp'], $members[1]['hp']);
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
		Master::add_item2($rwd,"yamen","rwd");
		//返回奖励弹窗
		$jiade = array();
		$jiade[] = $rwd_info;
		
		//随机现在其他5个奖励信息 一起返回为弹窗
		$ph_arr = Game::array_rand($cfg,5);
		foreach ($ph_arr as $v){
			$jiade[] = $v['rwd'];
		}

		//返回假的奖励弹窗
		Master::back_win("yamen","rwd","jiade",$jiade);
		//领奖次数+1 
		$this->info['rwd']+=1;
		//判断是否战斗结束(全歼)
		if (empty($this->info['fhids'])){
			//结算前先向被打的用户加锁
			Master::get_lock_special('yamen','getrwd',$this->info['fuid']);
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
		Game::cmd_flow(55,1,$num,$this->info['money']);
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
		Game::cmd_flow(55,1,-$num,$this->info['money']);
	}

	public function oneKeyPlay(){
		if(empty($this->info['fuid'])){
			Master::error(ONE_YAMEN_003);
		}
		$onekey_type = $this->info['ftype'];
		$fTeamModel= Master::getTeam($this->info['fuid']);
		$fnum = count($fTeamModel->info['heros']);//敌方门客信息
		//最多轮数换算 多出一轮保存
		$max_num = $fnum + ceil($fnum/3) + 1;
		$i=1;
		while ($max_num--){
			$state = $this->get_fight_state();
			if($state == 0){
				$this->save();
				break;
			}elseif($state == 1){//可打
				$this->onekey_select_fhid();
			}else{//抽奖
				$this->onkey_rand_rwd();
			}
			$i++;
		}
		$items = array();
		if(!empty($this->onekey_rwd)){
			foreach ($this->onekey_rwd as $itemid =>$val){
				foreach ($val as $kind => $num){
					$items[] = array('id' => $itemid,'kind' => $kind,'count' => $num);
				}
			}
		}
		$put = array(
			'ftype' => $onekey_type == 5? 1 : 0,
			'win' => $this->onekey_kill_all,
			'kill' => $this->onkey_kill,
			'items' => $items
		);
		Master::back_data($this->uid,$this->b_mol,'onekey',$put);
		if(!empty($this->onekey_special_rwd)){
			foreach ($this->onekey_special_rwd as $id => $val){
				foreach ($val as $kind => $num){
					Master::add_item($this->uid,$kind,$id,$num,"yamen","fight");
				}
			}
		}
	}

	/*
	 * 一键打
	 * 选择一个对手门客 进行战斗
	 */
	public function onekey_select_fhid(){
		//获取最低爵位的hid
		$hid = $this->getlowHero($this->info['fhids'],$this->info['fuid']);
		Game::cmd_flow(70, json_encode($this->info['fhids']).'-'.$this->info['hid'], $hid, $hid);
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

		$Redis6Model = Master::getRedis6();
		$score = $Redis6Model->zScore($this->uid);

		//胜负
		$fight_over = 0;//战斗是否结束
		$is_win = 0;//胜利标签
		if ($log['win'] == 0){//胜利
			$is_win = 1;
			$this->onkey_kill = empty($this->onkey_kill) ? 1 : $this->onkey_kill+1;
			//加入击毙队列
			$this->info['dead'][] = $hid;

			$add_score = 2;

			//如果是追杀
			if ($this->info['ftype'] == 5){
				$add_score *= 2;
			}
			//衙门分数+2
			$this->onekey_rwd[9][2] = empty($this->onekey_rwd[9][2]) ? $add_score : $this->onekey_rwd[9][2]+$add_score;
			Master::add_item($this->uid,2,9,$add_score,"yamen","fight");
			//衙门冲榜流水
			$Act254Model = Master::getAct254($this->uid);
			$cbscore = $Act254Model->get_cbscore();
			Game::cmd_flow(20, $this->info['fuid'].'_'.$cbscore, $add_score, $score+$add_score);
			//书籍经验+2
			if(!isset($this->onekey_rwd[$this->info['hid']][5])){
				$this->onekey_rwd[$this->info['hid']][5] = 0;
			}
			$this->onekey_rwd[$this->info['hid']][5] += 2;

			if(!isset($this->onekey_special_rwd[$this->info['hid']][5])){
				$this->onekey_special_rwd[$this->info['hid']][5] = 0;
			}
			$this->onekey_special_rwd[$this->info['hid']][5] += 2;
//			Master::add_item($this->uid,5,$this->info['hid'],2,"yamen","fight");
			//衙门精力+1
			$this->onekey_rwd[6][2] = empty($this->onekey_rwd[6][2]) ? 1 : $this->onekey_rwd[6][2]+1;
			Master::add_item($this->uid,2,6,1,"yamen","fight");
			//重置3个目标
			$this->rand_f_hero();

			//是否全歼
			if (empty($this->info['fhids'])){
				$this->onekey_kill_all = 1;
				//如果处于选择加成的回合 不结束游戏
				$kill_num = count($this->info['dead']);
				if ($kill_num%3 != 0){
					//战斗结束标记
					$fight_over = 1;
				}else{
					//等选择完加成 再判断一下战斗是否结束
				}
			}else{
				//重置加成商店
				$this->rand_add_list();
				//加成
				$this->onkey_select_add();
			}
		}else{

			//失败
			$is_win = 0;

			Common::loadModel('SwitchModel');
			if(!SwitchModel::isYamenV2()) {//不使用新版算法
				$add_score = -1;
				//如果是追杀
				if ($this->info['ftype'] == 5){
					$add_score *= 2;
				}
				//衙门分数-1
				$this->onekey_rwd[9][2] = empty($this->onekey_rwd[9][2]) ? $add_score : $this->onekey_rwd[9][2]+$add_score;
				Master::add_item($this->uid,2,9,$add_score,"yamen","fight");

				//衙门流水
				//衙门冲榜流水
				$Act254Model = Master::getAct254($this->uid);
				$cbscore = $Act254Model->get_cbscore();
				Game::cmd_flow(20, $this->info['fuid'].'_'.$cbscore, $add_score, $score+$add_score);
			}else{
				$add_score = 0;
				Master::add_item($this->uid,2,9,$add_score,"yamen","fight");
			}

			//战斗结束标记
			$fight_over = 1;
		}
		//战斗结束
		if ($fight_over){
			//清理战场
			$this->fight_over($is_win);
		}
	}

	public function getlowHero($heros,$fuid){
		$hid = 0;
		$low = 0;
		$HeroModel = Master::getHero($fuid);
		foreach ($heros as $id){
			if($hid == 0){
				$low = empty($HeroModel->info[$id]['senior']) ? 1 : $HeroModel->info[$id]['senior'];
				$hid = $id;
			}else{
				$senior = empty($HeroModel->info[$id]['senior']) ? 1 : $HeroModel->info[$id]['senior'];
				if($low > $senior){
					$hid = $id;
					$low = $senior;
				}
			}
		}
		return $hid;
	}


	/*
	 * 选择一个奖励
	 */
	public function onkey_rand_rwd(){
		//奖励配置
		$cfg = Game::getcfg("yamen_rwd");//配置有7个档次..
		$id = Game::get_rand_key(100,$cfg,'prob_100');
		$rwd_info = $cfg[$id]['rwd'];
		$rwd = null;
		if ($rwd_info['kind'] == 2){//如果是枚举类型
			$kind = 2;
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
			if(!isset($this->onekey_special_rwd[$rwd['id']][$rwd['kind']])){
				$this->onekey_special_rwd[$rwd['id']][$rwd['kind']] = 0;
			}
			$this->onekey_special_rwd[$rwd['id']][$rwd['kind']] += $rwd['count'];
		}elseif($rwd_info['kind'] == 1){
			$rwd = $rwd_info;
		}else{
			Master::error(ACT_61_WEIZHILEIXING.$rwd_info['kind']);
		}
		if(!isset($this->onekey_rwd[$rwd['id']][$rwd['kind']])){
			$this->onekey_rwd[$rwd['id']][$rwd['kind']] = 0;
		}
		$this->onekey_rwd[$rwd['id']][$rwd['kind']] += $rwd['count'];
		//加上选择的奖励
		if($rwd_info['kind'] == 1){
			Master::add_item2($rwd,"yamen","rwd");
		}
		//领奖次数+1
		$this->info['rwd']+=1;
		//判断是否战斗结束(全歼)
		if (empty($this->info['fhids'])){
			$this->fight_over(1);
		}
	}

	/*
	 * 选择一个加成属性
	 */
	public function onkey_select_add(){

		$shop_keys = array_keys($this->info['shop']);
		$id = $shop_keys[1];
		//执行加成

		//加成配置
		$cfg = Game::getcfg("yamen_buff");
		//扣除资源
		if(empty($cfg[$id]) || $this->info['money'] < $cfg[$id]['need']['count']){
			return;
		}
		$nid = $cfg[$id]['need']['id'];
		$nkind = $cfg[$id]['need']['kind'];
		$ncount = $cfg[$id]['need']['count'];
		$this->onekey_rwd[$nid][$nkind] = empty($this->onekey_rwd[$nid][$nkind]) ? -$ncount : $this->onekey_rwd[$nid][$nkind]-$ncount;
		Master::sub_item2($cfg[$id]['need']);

		//设置为已购买
		$this->info['shop'][$id]['buy'] = 1;
		//当前加成属性
		$jiacheng = array(
			'ackadd' => $this->info['ackadd'],//攻击加成
			'skilladd' => $this->info['skilladd'],//技能加成
			'damge' => $this->info['damge'],//受到伤害血量
		);

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
	}


	//--------------------------------优化版本-------------------------------

	/*
	 * 选择一个对手门客 进行战斗
	 */
	public function select_fhid2($hid){
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
		$fmember = $fTeamModel->get_pvp_buyid2($hid);

		//我方门客信息
		$TeamModel= Master::getTeam($this->uid);
		$member = $TeamModel->get_pvp_buyid2($this->info['hid']);
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

		$log = $this->do_pk2($members);

		//衙门信息类
		$Act60Model= Master::getAct60($this->uid);

		$Redis6Model = Master::getRedis6();
		$score = $Redis6Model->zScore($this->uid);

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
			Master::add_item($this->uid,2,9,$add_score,"yamen","fight");
			//衙门冲榜流水
			$Act254Model = Master::getAct254($this->uid);
			$cbscore = $Act254Model->get_cbscore();
			Game::cmd_flow(20, $this->info['fuid'].'_'.$cbscore, $add_score, $score+$add_score);
			//书籍经验+2
			Master::add_item($this->uid,5,$this->info['hid'],2,"yamen","fight");
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
			Common::loadModel('SwitchModel');
			if(!SwitchModel::isYamenV2()) {//不使用新版算法
				$add_score = -1;
				//如果是追杀
				if ($this->info['ftype'] == 5){
					$add_score *= 2;
				}
				//衙门分数-1
				Master::add_item($this->uid,2,9,$add_score,"yamen","fight");

				//衙门流水
				//衙门冲榜流水
				$Act254Model = Master::getAct254($this->uid);
				$cbscore = $Act254Model->get_cbscore();
				Game::cmd_flow(20, $this->info['fuid'].'_'.$cbscore, $add_score, $score+$add_score);
			}else{
				$add_score = 0;
				Master::add_item($this->uid,2,9,$add_score,"yamen","fight");
			}
			//战斗结束标记
			$fight_over = 1;
		}

		//战斗弹窗
		//扣去受伤血量
		$base_member = array(
			0 => $member['base'],
			1 => $fmember['base'],
		);
		Master::back_win("yamen","fight","base",$base_member);
		Master::back_win("yamen","fight","log",$log['log']);
		Master::back_win("yamen","fight","win",$is_win);
		$kill_num = count($this->info['dead']);
		Master::back_win("yamen","fight","winnum",$kill_num);
		Master::back_win("yamen","fight","nrwd",3-$kill_num%3);

		/*
		$fight_win = array(
			//阵营信息
			'base' => array(
				0 => $members[0]['base'],
				1 => $members[1]['base'],
			),
			//战斗日志/胜负信息
			'log' => $log,
			'winnum' => $kill_num,//连胜次数
			'nrwd' => $kill_num%3,//再打n场获得连胜奖励
		);
		*/

		//战斗结束
		if ($fight_over){
			//清理战场
			//结算前先向被打的用户加锁
			Master::get_lock_special('yamen','fight',$this->info['fuid']);
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
            'epnum' =>  count($info['epskill']),//资质技能数量
            'level' => $level //门客等级
         */
	public function do_pk2($members){
//		$a_id;//出手者
//		$b_id;//防御者
		$rand_num = rand(1,$members[0]['level']+$members[1]['level']);
		if($rand_num <= $members[0]['level']){
			$a_id = 0;
			$b_id = 1;
		}else{
			$a_id = 1;
			$b_id = 0;
		}

		$hit = 0;
		if($members[$a_id]['epnum'] > $members[$b_id]['epnum']){
			$members[$a_id]['epnum'] = $members[$a_id]['epnum']+1;
		}

		//战斗循环
		$log = array();//战斗日志
		$win = $a_id;//胜利方
		$dead = false;
		for ($i = 1 ; $i < 200 ; $i++){
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
				$dead = true;
				break;
			}

			//转换攻防
			$a_mb['epnum']--;
			if($a_mb['epnum']<=0 || ($a_mb['epnum'] > 0 && $b_mb['epnum'] > 0)){
				$a_id = $b_id;
				$b_id = ($a_id+1)%2;//防御者
			}
		}
		if(!$dead){//回合结束没有门客死亡，判定双方血量
			$win = $members[$a_id]['hp'] > $members[$b_id]['hp']?$a_id:$b_id;
			Game::cmd_flow(99, 'fight_not_dead', $members[0]['hp'], $members[1]['hp']);
		}
		$this->info['damge'] += $hit;

		return array(
			'win' => $win,//胜利方
			'log' => $log,
		);
	}


	public function oneKeyPlay2(){
		if(empty($this->info['fuid'])){
			Master::error(ONE_YAMEN_003);
		}
		$onekey_type = $this->info['ftype'];
		$fTeamModel= Master::getTeam($this->info['fuid']);
		$fnum = count($fTeamModel->info['heros']);//敌方门客信息
		//最多轮数换算 多出一轮保存
		$max_num = $fnum + ceil($fnum/3) + 1;
		$i=1;
		while ($max_num--){
			$state = $this->get_fight_state();
			if($state == 0){
				$this->save();
				break;
			}elseif($state == 1){//可打
				$this->onekey_select_fhid2();
			}else{//抽奖
				$this->onkey_rand_rwd();
			}
			$i++;
		}
		$items = array();
		if(!empty($this->onekey_rwd)){
			foreach ($this->onekey_rwd as $itemid =>$val){
				foreach ($val as $kind => $num){
					$items[] = array('id' => $itemid,'kind' => $kind,'count' => $num);
				}
			}
		}
		$put = array(
			'ftype' => $onekey_type == 5? 1 : 0,
			'win' => $this->onekey_kill_all,
			'kill' => $this->onkey_kill,
			'items' => $items
		);
		Master::back_data($this->uid,$this->b_mol,'onekey',$put);

		if(!empty($this->onekey_special_rwd)){
			foreach ($this->onekey_special_rwd as $id => $val){
				foreach ($val as $kind => $num){
					Master::add_item($this->uid,$kind,$id,$num,"yamen","fight");
				}
			}
		}
	}


	/*
	 * 一键打(优化版)
	 * 选择一个对手门客 进行战斗
	 */
	public function onekey_select_fhid2(){
		//获取最低爵位的hid
		$hid = $this->getlowHero($this->info['fhids'],$this->info['fuid']);
		Game::cmd_flow(70, json_encode($this->info['fhids']).'-'.$this->info['hid'], $hid, $hid);
		//构造门客战斗数据
		//对方门客信息
		$fTeamModel= Master::getTeam($this->info['fuid']);
		$fmember = $fTeamModel->get_pvp_buyid2($hid);

		//我方门客信息
		$TeamModel= Master::getTeam($this->uid);
		$member = $TeamModel->get_pvp_buyid2($this->info['hid']);

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

		$log = $this->do_pk2($members);

		$Redis6Model = Master::getRedis6();
		$score = $Redis6Model->zScore($this->uid);

		//胜负
		$fight_over = 0;//战斗是否结束
		$is_win = 0;//胜利标签
		if ($log['win'] == 0){//胜利
			$is_win = 1;
			$this->onkey_kill = empty($this->onkey_kill) ? 1 : $this->onkey_kill+1;
			//加入击毙队列
			$this->info['dead'][] = $hid;

			$add_score = 2;

			//如果是追杀
			if ($this->info['ftype'] == 5){
				$add_score *= 2;
			}
			//衙门分数+2
			$this->onekey_rwd[9][2] = empty($this->onekey_rwd[9][2]) ? $add_score : $this->onekey_rwd[9][2]+$add_score;
			Master::add_item($this->uid,2,9,$add_score,"yamen","fight");
			//衙门冲榜流水
			$Act254Model = Master::getAct254($this->uid);
			$cbscore = $Act254Model->get_cbscore();
			Game::cmd_flow(20, $this->info['fuid'].'_'.$cbscore, $add_score, $score+$add_score);
			//书籍经验+2
			if(!isset($this->onekey_rwd[$this->info['hid']][5])){
				$this->onekey_rwd[$this->info['hid']][5] = 0;
			}
			$this->onekey_rwd[$this->info['hid']][5] += 2;

			if(!isset($this->onekey_special_rwd[$this->info['hid']][5])){
				$this->onekey_special_rwd[$this->info['hid']][5] = 0;
			}
			$this->onekey_special_rwd[$this->info['hid']][5] += 2;
//			Master::add_item($this->uid,5,$this->info['hid'],2,"yamen","fight");
			//衙门精力+1
			$this->onekey_rwd[6][2] = empty($this->onekey_rwd[6][2]) ? 1 : $this->onekey_rwd[6][2]+1;
			Master::add_item($this->uid,2,6,1,"yamen","fight");
			//重置3个目标
			$this->rand_f_hero();

			//是否全歼
			if (empty($this->info['fhids'])){
				$this->onekey_kill_all = 1;
				//如果处于选择加成的回合 不结束游戏
				$kill_num = count($this->info['dead']);
				if ($kill_num%3 != 0){
					//战斗结束标记
					$fight_over = 1;
				}else{
					//等选择完加成 再判断一下战斗是否结束
				}
			}else{
				//重置加成商店
				$this->rand_add_list();
				//加成
				$this->onkey_select_add();
			}
		}else{

			//失败
			$is_win = 0;
			Common::loadModel('SwitchModel');
			if(!SwitchModel::isYamenV2()) {//不使用新版算法
				$add_score = -1;
				//如果是追杀
				if ($this->info['ftype'] == 5){
					$add_score *= 2;
				}
				//衙门分数-1
				$this->onekey_rwd[9][2] = empty($this->onekey_rwd[9][2]) ? $add_score : $this->onekey_rwd[9][2]+$add_score;
				Master::add_item($this->uid,2,9,$add_score,"yamen","fight");

				//衙门流水
				//衙门冲榜流水
				$Act254Model = Master::getAct254($this->uid);
				$cbscore = $Act254Model->get_cbscore();
				Game::cmd_flow(20, $this->info['fuid'].'_'.$cbscore, $add_score, $score+$add_score);
			}else{
				$add_score = 0;
				Master::add_item($this->uid,2,9,$add_score,"yamen","fight");
			}
			
			//战斗结束标记
			$fight_over = 1;
		}
		//战斗结束
		if ($fight_over){
			//清理战场
			$this->fight_over($is_win);
		}
	}


	//--------------------------------优化3版本-------------------------------

	/*
	 * 选择一个对手门客 进行战斗
	 */
	public function select_fhid3($hid){
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
		$fmember = $fTeamModel->get_pvp_buyid3($hid);
		//我方门客信息
		$TeamModel= Master::getTeam($this->uid);
		$member = $TeamModel->get_pvp_buyid3($this->info['hid']);

		//使用加成信息
//		$member['prop']['attack'] += round($member['prop']['attack']*$this->info['ackadd']/100);
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

		$log = $this->do_pk3($members);

		//衙门信息类
		$Act60Model= Master::getAct60($this->uid);

		$Redis6Model = Master::getRedis6();
		$score = $Redis6Model->zScore($this->uid);

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
			Master::add_item($this->uid,2,9,$add_score,"yamen","fight");
			//衙门冲榜流水
			$Act254Model = Master::getAct254($this->uid);
			$cbscore = $Act254Model->get_cbscore();
			Game::cmd_flow(20, $this->info['fuid'].'_'.$cbscore, $add_score, $score+$add_score);
			//书籍经验+2
			Master::add_item($this->uid,5,$this->info['hid'],2,"yamen","fight");
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
			Common::loadModel('SwitchModel');
			if(!SwitchModel::isYamenV2()) {//不使用新版算法
				$add_score = -1;
				//如果是追杀
				if ($this->info['ftype'] == 5){
					$add_score *= 2;
				}
				//衙门分数-1
				Master::add_item($this->uid,2,9,$add_score,"yamen","fight");

				//衙门流水
				//衙门冲榜流水
				$Act254Model = Master::getAct254($this->uid);
				$cbscore = $Act254Model->get_cbscore();
				Game::cmd_flow(20, $this->info['fuid'].'_'.$cbscore, $add_score, $score+$add_score);
			}else{
				$add_score = 0;
				Master::add_item($this->uid,2,9,$add_score,"yamen","fight");
			}
			//战斗结束标记
			$fight_over = 1;
		}

		//战斗弹窗
		//扣去受伤血量
		$base_member = array(
			0 => $member['base'],
			1 => $fmember['base'],
		);
		Master::back_win("yamen","fight","base",$base_member);
		Master::back_win("yamen","fight","log",$log['log']);
		Master::back_win("yamen","fight","win",$is_win);
		$kill_num = count($this->info['dead']);
		Master::back_win("yamen","fight","winnum",$kill_num);
		Master::back_win("yamen","fight","nrwd",3-$kill_num%3);

		/*
		$fight_win = array(
			//阵营信息
			'base' => array(
				0 => $members[0]['base'],
				1 => $members[1]['base'],
			),
			//战斗日志/胜负信息
			'log' => $log,
			'winnum' => $kill_num,//连胜次数
			'nrwd' => $kill_num%3,//再打n场获得连胜奖励
		);
		*/

		//战斗结束
		if ($fight_over){
			//清理战场
			//结算前先向被打的用户加锁
			Master::get_lock_special('yamen','fight',$this->info['fuid']);
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
            'attack' => array($attack1,$attack2),//攻击力集合
            'bprob' => $b_prop,//暴击概率
            'bhurt' => $b_hurt,//暴击伤害
            'epnum' =>  count($info['epskill']),//资质技能数量
            'level' => $level //门客等级
         */
	public function do_pk3($members){
//		$a_id;//出手者
//		$b_id;//防御者
		// 0 我方 1 敌方
		$rand_num = rand(1,$members[0]['level']+$members[1]['level']);
		if($rand_num <= $members[0]['level']){
			$a_id = 0;
			$b_id = 1;
		}else{
			$a_id = 1;
			$b_id = 0;
		}

		$hit = 0;
		$one_round = $members[$b_id]['epnum'];//一轮最高回合数
		if($members[$a_id]['epnum'] > $one_round){
			$one_round = $members[$a_id]['epnum'];
		}
		//总轮数  单轮数
		$total_num = 200 % $one_round > 0 ? intval(200/$one_round+1) : intval(200/$one_round);
		//战斗循环
		$log = array();//战斗日志
		$win = $a_id;//胜利方
		$dead = false;
		for ($i = 1; $i <= $total_num; $i++ ){//总循环
			//每一轮 书恢复  攻击集合恢复
            $pk[$a_id]['num'] = count($members[$a_id]['attack']);
            $pk[$a_id]['attack'] = $members[$a_id]['attack'];
            $pk[$b_id]['num'] = count($members[$b_id]['attack']);
            $pk[$b_id]['attack'] = $members[$b_id]['attack'];
			if($dead){
				break;
			}
			for ($j = 1; $j <= $one_round; $j++) {//单轮循环
				if($dead){
					break;
				}
				//每回合 敌我各打一次
				for ($k = 1; $k <= 2; $k++) {
					if($pk[$a_id]['num'] <= 0){//出手方没书不打 攻守转换
						$a_id = $b_id;
						$b_id = ($a_id + 1) % 2;//防御者
						continue;
					}
					//出手方数据
					$a_mb = &$members[$a_id];
					//防守方数据
					$b_mb = &$members[$b_id];
					$dtype = 0;//是否暴击
					//先手
					shuffle($pk[$a_id]['attack']);
					$a_attack = array_pop($pk[$a_id]['attack']);
					if ($a_id == 0) {//出手人是我方  攻击加成
						$a_attack += round($a_attack * $this->info['ackadd'] / 100);
					}
					$damge = $a_attack < 1000 ? ($a_attack + rand(1, 100)) : round($a_attack * rand(90, 110) / 100);
					//暴击概率
					if (rand(1, 10000) < $a_mb['bprob']) {
						//暴击伤害
						$damge = round($damge * $a_mb['bhurt'] / 100);
						$dtype = 1;//效果:暴击
					}

					//扣血
					$b_mb['hp'] -= $damge;

					if ($a_id == 1) {//出手是敌方 记录我受到的伤害
						$hit += $damge;
					}
					$log[] = array(
						'aid' => $a_id,//出手方
						'damge' => $damge,
						'type' => $dtype,
					);

					//死亡判定
					if ($b_mb['hp'] <= 0) {
						$win = $a_id;
						$dead = true;
						break;
					}
					//先扣书
					$pk[$a_id]['num']--;

					//攻守转换
					$a_id = $b_id;
					$b_id = ($a_id + 1) % 2;//防御者
				}
			}
		}
		if(!$dead){//回合结束没有门客死亡，判定双方血量
			$win = $members[$a_id]['hp'] > $members[$b_id]['hp']?$a_id:$b_id;
			Game::cmd_flow(99, 'fight_not_dead', $members[0]['hp'], $members[1]['hp']);
		}
		$this->info['damge'] += $hit;
		return array(
			'win' => $win,//胜利方
			'log' => $log,
		);
	}


	public function oneKeyPlay3(){
		if(empty($this->info['fuid'])){
			Master::error(ONE_YAMEN_003);
		}
		$onekey_type = $this->info['ftype'];
		$fTeamModel= Master::getTeam($this->info['fuid']);
		$fnum = count($fTeamModel->info['heros']);//敌方门客信息
		//最多轮数换算 多出一轮保存
		$max_num = $fnum + ceil($fnum/3) + 1;
		$i=1;
		while ($max_num--){
			$state = $this->get_fight_state();
			if($state == 0){
				$this->save();
				break;
			}elseif($state == 1){//可打
				$this->onekey_select_fhid3();
			}else{//抽奖
				$this->onkey_rand_rwd();
			}
			$i++;
		}
		$items = array();
		if(!empty($this->onekey_rwd)){
			foreach ($this->onekey_rwd as $itemid =>$val){
				foreach ($val as $kind => $num){
					$items[] = array('id' => $itemid,'kind' => $kind,'count' => $num);
				}
			}
		}
		$put = array(
			'ftype' => $onekey_type == 5? 1 : 0,
			'win' => $this->onekey_kill_all,
			'kill' => $this->onkey_kill,
			'items' => $items
		);
		Master::back_data($this->uid,$this->b_mol,'onekey',$put);

		if(!empty($this->onekey_special_rwd)){
			foreach ($this->onekey_special_rwd as $id => $val){
				foreach ($val as $kind => $num){
					Master::add_item($this->uid,$kind,$id,$num,"yamen","fight");
				}
			}
		}
	}


	/*
	 * 一键打(优化版)
	 * 选择一个对手门客 进行战斗
	 */
	public function onekey_select_fhid3(){
		//获取最低爵位的hid
		$hid = $this->getlowHero($this->info['fhids'],$this->info['fuid']);
		Game::cmd_flow(70, json_encode($this->info['fhids']).'-'.$this->info['hid'], $hid, $hid);
		//构造门客战斗数据
		//对方门客信息
		$fTeamModel= Master::getTeam($this->info['fuid']);
		$fmember = $fTeamModel->get_pvp_buyid3($hid);

		//我方门客信息
		$TeamModel= Master::getTeam($this->uid);
		$member = $TeamModel->get_pvp_buyid3($this->info['hid']);

		//使用加成信息
//		$member['prop']['attack'] += round($member['prop']['attack']*$this->info['ackadd']/100);
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

		$log = $this->do_pk3($members);

		$Redis6Model = Master::getRedis6();
		$score = $Redis6Model->zScore($this->uid);

		//胜负
		$fight_over = 0;//战斗是否结束
		$is_win = 0;//胜利标签
		if ($log['win'] == 0){//胜利
			$is_win = 1;
			$this->onkey_kill = empty($this->onkey_kill) ? 1 : $this->onkey_kill+1;
			//加入击毙队列
			$this->info['dead'][] = $hid;

			$add_score = 2;

			//如果是追杀
			if ($this->info['ftype'] == 5){
				$add_score *= 2;
			}
			//衙门分数+2
			$this->onekey_rwd[9][2] = empty($this->onekey_rwd[9][2]) ? $add_score : $this->onekey_rwd[9][2]+$add_score;
			Master::add_item($this->uid,2,9,$add_score,"yamen","fight");
			//衙门冲榜流水
			$Act254Model = Master::getAct254($this->uid);
			$cbscore = $Act254Model->get_cbscore();
			Game::cmd_flow(20, $this->info['fuid'].'_'.$cbscore, $add_score, $score+$add_score);
			//书籍经验+2
			if(!isset($this->onekey_rwd[$this->info['hid']][5])){
				$this->onekey_rwd[$this->info['hid']][5] = 0;
			}
			$this->onekey_rwd[$this->info['hid']][5] += 2;

			if(!isset($this->onekey_special_rwd[$this->info['hid']][5])){
				$this->onekey_special_rwd[$this->info['hid']][5] = 0;
			}
			$this->onekey_special_rwd[$this->info['hid']][5] += 2;
//			Master::add_item($this->uid,5,$this->info['hid'],2,"yamen","fight");
			//衙门精力+1
			$this->onekey_rwd[6][2] = empty($this->onekey_rwd[6][2]) ? 1 : $this->onekey_rwd[6][2]+1;
			Master::add_item($this->uid,2,6,1,"yamen","fight");
			//重置3个目标
			$this->rand_f_hero();

			//是否全歼
			if (empty($this->info['fhids'])){
				$this->onekey_kill_all = 1;
				//如果处于选择加成的回合 不结束游戏
				$kill_num = count($this->info['dead']);
				if ($kill_num%3 != 0){
					//战斗结束标记
					$fight_over = 1;
				}else{
					//等选择完加成 再判断一下战斗是否结束
				}
			}else{
				//重置加成商店
				$this->rand_add_list();
				//加成
				$this->onkey_select_add();
			}
		}else{

			//失败
			$is_win = 0;
			Common::loadModel('SwitchModel');
			if(!SwitchModel::isYamenV2()) {//不使用新版算法
				$add_score = -1;
				//如果是追杀
				if ($this->info['ftype'] == 5){
					$add_score *= 2;
				}
				//衙门分数-1
				$this->onekey_rwd[9][2] = empty($this->onekey_rwd[9][2]) ? $add_score : $this->onekey_rwd[9][2]+$add_score;
				Master::add_item($this->uid,2,9,$add_score,"yamen","fight");

				//衙门流水
				//衙门冲榜流水
				$Act254Model = Master::getAct254($this->uid);
				$cbscore = $Act254Model->get_cbscore();
				Game::cmd_flow(20, $this->info['fuid'].'_'.$cbscore, $add_score, $score+$add_score);
			}else{
				$add_score = 0;
				Master::add_item($this->uid,2,9,$add_score,"yamen","fight");
			}

			//战斗结束标记
			$fight_over = 1;
		}
		//战斗结束
		if ($fight_over){
			//清理战场
			$this->fight_over($is_win);
		}
	}
	
}
















