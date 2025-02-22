<?php
/*
 * 世界BOSS 葛二蛋
 */
require_once "SevBaseModel.php";
class Sev6010Model extends SevBaseModel
{
	public $comment = "活动boss";
	public $act = 6010;//活动标签
	public $_init = array(//初始化数据
		"day" => 0,//开战日期
		"allhp" => 0,//BOSS初始话血量 / 增减?
		"damage" => 0,//当前BOSS伤害量
	);
	//下次开战时间日期  时间点数 使用配置
	public $stime = 9;//19
	public $etime = 23;//23
	
	public $outof = NULL;
	
	//
	//今天 未开战 ? 战斗中 ? 已击杀?
	//对比日期ID 如果不是今天 
	//每天的超时操作?
	public function __construct(){
		parent::__construct();
		$this->mk_outf();
	}
	
	/*
	 * 构造业务输出数据
	 */
	public function mk_outf(){
	    if ($this->info['allhp'] == 0){
            Common::loadModel('HoutaiModel');
            $hd_cfg = HoutaiModel::get_huodong_info('huodong_6010');
            $this->info['allhp'] = empty($hd_cfg)?50000000:$hd_cfg['boss_max_hp'];
            $this->save();
        }
		$start_data = $this->get_start_time();	//获取BOSS当前状态
		
		//暂存 其他接口读取
		$this->outof = array(
			'state' => $start_data['state'],//1未开始,2战斗中,3已结束,4逃跑了
			'stime' => $start_data['time'],//开战 / 结束 倒计时
			'allhp' => $this->info['allhp'],//总血量
			'damage' => $this->info['damage'],//伤害值
		);
	}
	
	/*
	 * 执行一次伤害
	 * 返回是否击杀
	 */
	public function hit($damage){
		if (!$this->in_fight()){
			Master::error(GAME_LEVER_PLAY_END);
		}
		
		//伤害值增加
		$this->info['damage'] += $damage;
		//判断击杀
		$is_kill = false;
		if ($this->info['damage'] > $this->info['allhp']){
			//重置BOSS
			$this->reset_boss(1);
			$is_kill = true;
		}
		$this->save();
		return $is_kill;
	}

	/*
	 * 重置BOSS操作
	 * $day:0今天,1明天
	 */
	public function reset_boss($day = 0){
        Common::loadModel('HoutaiModel');
        $hd_cfg = HoutaiModel::get_huodong_info('huodong_6010');
	    $this->info['allhp'] = empty($hd_cfg)?50000000:$hd_cfg['boss_max_hp'];
		$this->info['damage'] = 0;
		$this->info['day'] = Game::get_today_id($day);
	}
	
	/*
	 * 获取下次世界BOSS开打时间
	 * $is_init 是否初始化 初始化/在结束前 都使用今天的开打时间
	 */
	public function get_start_time(){
		if (empty($this->info['day'])){
			$this->info['day'] = Game::get_today_id();
		}
		
		//开打时间
		$s_time = strtotime('20'.$this->info['day'].' '.$this->stime.':0:0');
		//结束时间
		$e_time = strtotime('20'.$this->info['day'].' '.$this->etime.':59:59');
		
		//今天开打时间
		$t_s_time = strtotime('20'.Game::get_today_id().' '.$this->stime.':0:0');
		//开打时间是不是今天
		if ($s_time > $t_s_time){
			//明天开打 / 已击杀 倒计时中
			return array(
				'state' => 3,//3已击杀
				'time' => $s_time,//开战倒计时
			);
		}else{
			if ($s_time < $t_s_time){
				//昨天开打 / 已逃跑
				//昨天的 今天不管了
				
				//重置BOSS 为今天
				$this->reset_boss();
				$this->save();//保存
				
				//重新获取开打时间
				$s_time = strtotime('20'.$this->info['day'].' '.$this->stime.':0:0');
				//结束时间
				$e_time = strtotime('20'.$this->info['day'].' '.$this->etime.':59:59');
			}
			
			//今天
			$state = 0;
			if (!Game::is_over($s_time)){
				//没到开始时间 
				$state = 1;//未开战
			}elseif (Game::is_over($e_time)){
				//过了结束时间  战斗结束
				//BOSS逃跑了 / 如果没有逃跑 就会是明天的日期了
				$state = 4;//战斗结束 逃跑了 
				//不倒计时了
				$s_time = 0;
			}else{
				//战斗中
				$state = 2;
				//返回BOSS逃跑倒计时时间
				$s_time = $e_time;
			}
			
			return array(
				'state' => $state,//1未开始,2战斗中,3已结束,
				'time' => $s_time,//开战 / 结束 倒计时
			);
		}
	}
	
	/*
	 * 当前是否战斗中
	 */
	public function in_fight(){
		if($this->outof['state'] == 2){
			return true;
		}else {
			return false;
		}
	}
	
	
	
	
	
}
