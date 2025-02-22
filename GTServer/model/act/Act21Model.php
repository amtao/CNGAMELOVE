<?php
require_once "ActBaseModel.php";
/*
 * 蒙古军来袭
 */
class Act21Model extends ActBaseModel
{
	public $atype = 21;//活动编号
	
	public $comment = "蒙古军来袭";
	public $b_mol = "wordboss";//返回信息 所在模块
	public $b_ctrl = "menggu";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		"bo" => 1,	//当前打到第几波,
		"damage" => 0, //当前BOSS伤血量
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//12:00 ~ 14:00 
		$stime = 12; //10
		$etime = 14;//14
		
		$state = 0;
		//现在小时数
		$hour = date("G",$_SERVER['REQUEST_TIME']);
		if ($hour < $stime){
			//12点之前  还没开战
			$state = 1;
			$s_time = Game::day_0($stime);//今天12点
		}elseif ($hour >= $etime){
			//14点之后  战斗结束 明天开战
			$state = 3;
			$s_time = Game::day_0($stime) + 86400;//明天开始时间
		}else{
			//战斗中
			$state = 2;
			$s_time = Game::day_0($etime);//结束时间
		}
		
		//构造输出体
		$this->outf = array(
			'state' => $state,
			'cd' => array(
				'next' => $s_time,
				'label' => 'wordboss1',
			),
			'bo' => $this->info['bo'],
			'damage' => $this->info['damage'],
		);
	}
	
	/*
	 * 打蒙古 是否战斗中
	 */
	public function in_fight(){
		//是否战了
		if($this->outf['state'] == 2){
			return true;
		}
		Master::error(GAME_LEVER_PLAY_END);
	}
	
	/*
	 * 战斗
	 * hit($hid);
	 */
	public function hit($hid){
		$this->in_fight();
		
		$HeroModel = Master::getHero($this->uid);
		//门客存在
		$hero_info = $HeroModel->check_info($hid,true);
		
		//门客出战列表
		$Act4Model = Master::getAct4($this->uid);
		//这个门客 是不是可以出战(活的)
		$Act4Model->go_fight($hid);
		
		//BOSS血量配置 
		$mg_cfg_info = Game::getcfg_info("wordboss_mg",$this->info['bo'],"打完了");
		//BOSS剩余血量
		$boss_hp = $mg_cfg_info['hp'] - $this->info['damage'];
		
		//获取阵法信息
		$TeamModel  = Master::getTeam($this->uid);
		//英雄伤害值
		$hero_damage = $TeamModel->getHeroDamage($hid);
		if ($hero_damage >= $boss_hp){//击杀
			//加上兑换积分
			$Act23Molde = Master::getAct23($this->uid);
			$Act23Molde->add($mg_cfg_info['score2']);
			//加上排行分数
			$Redis4Model = Master::getRedis4();
			$Redis4Model->zIncrBy($this->uid,$mg_cfg_info['score']);
			
			//弹窗信息
			Master::back_win("wordboss","mghitwin","score",$mg_cfg_info['score']);
			Master::back_win("wordboss","mghitwin","score2",$mg_cfg_info['score2']);
			Master::back_win("wordboss","mghitwin","bo",$this->info['bo']);
			Master::back_win("wordboss","mghitwin","damage",$boss_hp);
			
			//加上银两
			$fUserdate = Master::fuidData($this->uid);
			
			$e2 = $fUserdate['ep']['e2'];
			$bo = $this->info['bo'];
			$mg_cfg_info['item2'] = eval("return ".$mg_cfg_info['item2'].";");
			$mg_cfg_info['item2'] = floor($mg_cfg_info['item2']);
			Master::add_item($this->uid,KIND_ITEM,2,$mg_cfg_info['item2'],"wordboss","mghitwin");
			
			//有概率获得道具奖励
			if( !empty($mg_cfg_info['rwd']) ){
				$extra = $mg_cfg_info['rwd'];
				$rk = Game::get_rand_key(10000,$extra,'prob_10000');
				if(!empty($extra[$rk])){
					$item = array(
						'kind' => $extra[$rk]['kind']?$extra[$rk]['kind']:1,
						'itemid' => $extra[$rk]['itemid'],
						'count' => $extra[$rk]['count'],
					);
					Master::add_item2($item,"wordboss","mghitwin");
					//如果奖励了道具 保存到奖励日志
					$Sev3Model = Master::getSev3();
					$Sev3Model->add($this->uid,$this->info['bo'],$extra[$rk]['itemid']);
				}
			}

            //双旦活动道具产出
            $Act292Model = Master::getAct292($this->uid);
            $hditem = $Act292Model->chanChu(7,$this->info['bo'],1);
            if(!empty($hditem)){
                Master::add_item2($hditem,"wordboss","mghitwin");
            }

			//下一只
			$this->info['damage'] = 0;
			$this->info['bo'] += 1;
		}else{
			$this->info['damage'] += $hero_damage;
			
			//失败弹窗
			Master::back_win("wordboss","mghitfail","bo",$this->info['bo']);
			Master::back_win("wordboss","mghitfail","damage",$hero_damage);
		}
		$this->save();
	}
	
	/*
	 * 使用出战令
	 */
	public function comeback($hid){
		$this->in_fight();
		
		//门客出战列表
		$Act4Model = Master::getAct4($this->uid);
		//恢复出战
		$Act4Model->cone_back($hid);
	}
}
