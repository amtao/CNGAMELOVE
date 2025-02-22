<?php
require_once "ActBaseModel.php";
/*
 * 割二蛋来袭
 */
class Act22Model extends ActBaseModel
{
	public $atype = 22;//活动编号
	
	public $comment = "割二蛋来袭";
	public $b_mol = "wordboss";//返回信息 所在模块
	public $b_ctrl = "ge2dan";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		"htime" => 0,//上次出手时间 / 打需要间隔
		"rankrwd" => 0,//排行奖励是否已领取 
		//如果有ID 并且这只ID 的BOSS已经死了 或者不等于本BOSS 就是有奖励未领取
	);
	
	/*
	 * 构造输出结构体
	*/
	public function make_out(){
		$Sev2Model = Master::getSev2();
		$Sev2Model->mk_outf();
		$sevdate = $Sev2Model->outof;//直接调用mk_outf 没经过缓存
		
		$this->outf =array(
			'cd' => array(
				'next' => $sevdate["stime"],//开战 / 结束 倒计时
//                'next' => strtotime('20'.Game::get_today_id().' 0:0:0'),
				'label' => 'wordboss2',
			),
			"state" => $sevdate["state"],//1未开始,2战斗中,3已结束,
			"allhp" => $sevdate["allhp"],//总血量
			"damage" => $sevdate["damage"],//伤害值
            "heroId" => $sevdate["heroId"],//伙伴id
		);
	}
	
	/*
	 * 战斗
	 * hit($hid);
	 */
	public function hit($hid, $type){
		//葛尔丹信息
		$Sev2Model = Master::getSev2();
		if (!$Sev2Model->in_fight()){
			Master::error(GAME_LEVER_PLAY_END);
		}

		//对应皇子不能出战
        $sevdate = $Sev2Model->outof;//直接调用mk_outf 没经过缓存
		if ($sevdate['heroId'] == $hid){
            Master::error(GAME_FIGHT_LIMIT);
        }
		
		//距离上次打的时间 
		if ($_SERVER['REQUEST_TIME'] - $this->info['htime'] < 10){
			Master::error(GAME_LEVER_PLAY_END);
		}
		
		$HeroModel = Master::getHero($this->uid);
		//门客存在
		$hero_info = $HeroModel->check_info($hid,true);
		
		//门客出战列表
		$Act5Model = Master::getAct5($this->uid);
		//这个门客 是不是可以出战(活的)
        $per = $Act5Model->go_fight($hid, $type);
		
		//获取阵法信息
		$TeamModel  = Master::getTeam($this->uid);
		//英雄伤害值
		$hero_damage = $TeamModel->getHeroDamage($hid, 4) * $per;
		//BOSS扣血
		$is_kill = $Sev2Model->hit($hero_damage);
		
		//伤血排行更新
		$Redis5Model = Master::getRedis5();
		$Redis5Model->zIncrBy($this->uid,$hero_damage);//加上伤害血量
		$Redis5Model->back_data();
		$Redis5Model->back_data_my($this->uid);
		
		$jifen = Game::getCfg_formula()->xianli_haogan($hero_damage); //积分算法?
		//加上兑换积分
		$Act23Molde = Master::getAct23($this->uid);
		$Act23Molde->add($jifen);
		//打葛二蛋 弹窗信息
		//加上排行分数
		$Redis4Model = Master::getRedis4();
		$Redis4Model->zIncrBy($this->uid,$jifen);
		
		//弹窗信息
		Master::back_win("wordboss","g2dHit","score",$jifen);
		Master::back_win("wordboss","g2dHit","score2",$jifen);
		Master::back_win("wordboss","g2dHit","damage",$hero_damage);
		
		if ($is_kill){
			//记录击杀信息
			$Sev4Model = Master::getSev4();
			$Sev4Model->add($this->uid);
			$Sev4Model->bake_data();
			
			//击杀奖励
			$kill_fen = 100;
			$Act23Molde->add($kill_fen);//加上兑换积分
			$Redis4Model->zIncrBy($this->uid,$kill_fen);//加上排行分数
			
			//击杀弹窗
			Master::back_win("wordboss","g2dKill","score",$kill_fen);
			Master::back_win("wordboss","g2dKill","score2",$kill_fen);
			
			//随机用
			$w_itemid = rand(160,162);
			//其他道具奖励?
			Master::add_item($this->uid,KIND_ITEM,$w_itemid,1,"wordboss","g2dKill");

            //双旦活动道具产出
            $Act292Model = Master::getAct292($this->uid);
            $hditem = $Act292Model->chanChu(9,0,1);
            if(!empty($hditem)){
                Master::add_item2($hditem,"wordboss","g2dKill");
            }
			
			//活动消耗 - 限时击杀葛尔丹次数
			$HuodongModel = Master::getHuodong($this->uid);
			$HuodongModel->xianshi_huodong('huodong215',1);

			//击杀跑马灯
            $UserInfo = Master::fuidInfo($this->uid);
            $Sev91Model = Master::getSev91();
            $Sev91Model->add_msg(array(103,Game::filter_char($UserInfo['name'])));

		}
		
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(121, $jifen);

		$this->save();
	}
	
	/*
	 * 使用出战令
	 */
	public function comeback($hid){
		//是否战斗中
		$Sev2Model = Master::getSev2();
		if (!$Sev2Model->in_fight()){
			Master::error(GAME_LEVER_PLAY_END);
		}
		
		//门客出战列表
		$Act5Model = Master::getAct5($this->uid);
		//恢复出战
		$Act5Model->cone_back($hid);
	}

	/*
	 * 检查发放排行奖励
	 */
	public function click_kill(){
		//是否发过奖励了
		if($this->info['rankrwd'] != 0){
            $this->save();
			return;
		}
		//是否战斗结束
		$Sev2Model = Master::getSev2();
		if ($Sev2Model->outof['state'] < 3){
			//战斗中
            $this->save();
			return;
		}
		//我的名次
		$Redis5Model = Master::getRedis5();
		$myRank= $Redis5Model->get_rank_id($this->uid);
		
		if ($myRank > 100 || $myRank <= 0){
            $this->save();
			return;
		}
		
		//奖励配置
		$wordboss_rankrwd_cfg = Game::getcfg('wordboss_rankrwd');
		$rwd = array();
		foreach($wordboss_rankrwd_cfg as $v){
			if ($myRank <= $v['id']){
				$rwd = $v;
				break;
			}
		}
		if (empty($rwd)){
			Master::error("wordboss_rankrwd_myrank_err_".$myRank);
		}
		
		//排行奖励
		$score = $rwd['score'];
		$score2 = $rwd['score2'];
		$zz = $rwd['zz'];//政绩
		$kill = 3;
		
		//逃跑 发放打折排行奖励
		if ($Sev2Model->outof['state'] == 4){
			$kill = 4;
			$score = floor($score/2);
			$score2 = floor($score2/2);
			$zz = floor($zz/2);
		}
		
		//加上奖励
		$Act23Molde = Master::getAct23($this->uid);
		$Act23Molde->add($score);
		//加上排行分数
		$Redis4Model = Master::getRedis4();
		$Redis4Model->zIncrBy($this->uid,$score2);
		
		//机上道具奖励
		Master::add_item($this->uid,KIND_ITEM,5,$zz,"wordboss","g2dRank");
		
		//排行奖励弹窗
		Master::back_win("wordboss","g2dRank","rid",$myRank);
		Master::back_win("wordboss","g2dRank","score",$score);
		Master::back_win("wordboss","g2dRank","score2",$score2);
		
		//记录已经发奖
		$this->info['rankrwd'] = $_SERVER['REQUEST_TIME'];
		$this->save();
	}
}
