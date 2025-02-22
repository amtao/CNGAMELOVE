<?php
/*
 * 世界BOSS 葛二蛋
 */
require_once "SevBaseModel.php";
class Sev2Model extends SevBaseModel
{
	public $comment = "世界BOSS葛二蛋";
	public $act = 2;//活动标签
	public $_init = array(//初始化数据
		"day" => 0,//开战日期
		"allhp" => 300000,//BOSS初始话血量 / 增减?
        "hindex" => 0,
		"hday" =>0,
		"hid" => 1,
		"damage" => 0,//当前BOSS伤害量
	);
	//下次开战时间日期  时间点数 使用配置
	public $stime = 0;//19
	public $etime = 0;//23
    public $hids = array();//
    public $minHp = 0;
    public $maxHp = 0;
	
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
		$start_data = $this->get_start_time();	//获取BOSS当前状态
		
		//暂存 其他接口读取
		$this->outof = array(
			'state' => $start_data['state'],//1未开始,2战斗中,3已结束,4逃跑了
			'stime' => $start_data['time'],//开战 / 结束 倒计时
			'allhp' => $this->info['allhp'],//总血量
			'damage' => $this->info['damage'],//伤害值
            'heroId' => $this->info['hid'] == 0?1:$this->info['hid'],//几天打的皇子id
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

	private function getParamTime(){
        if ($this->stime == 0){
            $this->stime = Game::getcfg_param('world_boss_start_hour');
            $this->etime = Game::getcfg_param('world_boss_end_hour');
            $this->maxHp = Game::getcfg_param('world_boss_maxHp');
            $this->minHp = Game::getcfg_param('world_boss_minHp');
            $ss = Game::getcfg_param('world_boss_ids');
            $this->hids = explode('|', $ss);
        }
    }
	
	/*
	 * 重置BOSS操作
	 * $day:0今天,1明天
	 */
	public function reset_boss($day = 0){
		//判断击杀时间  增减BOSS血量
		//开打时间
        $this->getParamTime();
		$s_time = strtotime(date("Y-m-d ".$this->stime.":00:00"));
		
		$d_time = $_SERVER['REQUEST_TIME'] - $s_time;
		//成功击杀算法
		$js_sf = array(
			240 => 2,    //1分钟以内击杀  上调2倍
			720 => 1.5,    //3分钟以内击杀  上调1.5倍
			1200 => 1,    //5分钟以内击杀  上调1倍
			2400 => 0.8,    //10分钟以内击杀  上调0.8倍
			4800 => 0.5,    //20分钟以内击杀  上调0.5倍
			9600 => 0.3,    //40分钟以内击杀  上调0.3倍
			12000 => 0.1,    //50分钟以内击杀  上调0.1倍
			13200 => 0.05,    //55分钟以内击杀  上调0.05倍
		);
		//未击杀算法
		$fail_sf = array(
			90 => 80,  //血量剩余90%以上  扣除80% 
			80 => 70,
			70 => 60,
			60 => 50,
			50 => 40,
			40 => 30,
			30 => 20,
			20 => 10,
		);
		
		//如果击杀
		if($this->info['damage'] >= $this->info['allhp']){
			foreach($js_sf as $k => $v){
				if($d_time < $k){
					$this->info['allhp'] += floor($this->info['allhp']*$v);
					break;
				}
			}
            $this->info['allhp'] = $this->info['allhp'] > $this->maxHp ? $this->maxHp:$this->info['allhp'];
		}else{ //未击杀
			//剩余血量
			$left_hp = intval($this->info['allhp'] - $this->info['damage']);
			//剩余血量的百分比   基值100
			$rate_hp = intval( $left_hp * 100 / $this->info['allhp'] );
			foreach($fail_sf as $k => $v){
				if($rate_hp >= $k){
					$this->info['allhp'] -= intval($this->info['allhp'] * $k / 100);
					break;
				}
			}
            $this->info['allhp'] = $this->info['allhp'] < $this->minHp ? $this->minHp:$this->info['allhp'];
		}
		
		$this->info['damage'] = 0;
		$this->info['day'] = Game::get_today_id($day);
        $this->nextHids();
	}

	public function nextHids(){
	    $this->getParamTime();
        $index = $this->info['hindex'];
        if ($this->info['hday'] == 0){
            $this->info['hday'] = Game::get_today_id();
        }
        if ($this->info['hday'] < Game::get_today_id()){
            $this->info['hindex'] = ($this->info['hindex'] + 1) % count($this->hids);
            $this->info['hday'] = Game::get_today_id();
        }
        $this->info['hid'] = intval($this->hids[$index]);
    }
	
	
	/*
	 * 获取下次世界BOSS开打时间
	 * $is_init 是否初始化 初始化/在结束前 都使用今天的开打时间
	 */
	public function get_start_time(){
        $this->getParamTime();
        $this->nextHids();
		if (empty($this->info['day'])){
			$this->info['day'] = Game::get_today_id();
		}
		
		//开打时间
		$s_time = strtotime('20'.$this->info['day'].' '.$this->stime.':0:0');
		//结束时间
		$e_time = strtotime('20'.$this->info['day'].' '.$this->etime.':0:0');
		
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
				$e_time = strtotime('20'.$this->info['day'].' '.$this->etime.':0:0');
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
