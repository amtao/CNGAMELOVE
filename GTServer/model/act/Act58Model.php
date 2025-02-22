<?php
require_once "ActBaseModel.php";
/*
 * 翰林院 个人信息
 */
class Act58Model extends ActBaseModel
{
	public $atype = 58;//活动编号
	
	public $comment = "翰林院-个人信息";
	public $b_mol = "hanlin";//返回信息 所在模块
	public $b_ctrl = "info";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'level' => 0,//翰林等级
		'exp' => 0,//翰林经验
		
		'state' => 0,//当前房间状态 0游荡中,1房间中,2被T了
		'ctime' => 0,//冷却时间
		'ruid' => 0,//房间号码
		'stime' => 0,//加入时间
		'etime' => 0,//房间完成时间
		'rlevel' => 0,//房主等级
		'tuid' => 0,//谁吧我T了
	
		'tcode' => array(
			/*
			 * '10086' => 13000,//T冷却时间
			 */
		),
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out()
	{
		$this->outf = array(
			'level' => $this->info['level'],//等级
			'exp' => $this->info['exp'],//经验
			'ruid' => $this->info['ruid'],//房间号
			'ctime' => Game::is_over($this->info['ctime'])?0:$this->info['ctime'],//被T冷却时间
		);
	}
	
	/*
	 * 匹配状态
	 */
	public function click_state($state){
		if ($state == 0){
			//判断是否空闲中
			if ($this->info['state'] > 0){
				Master::error(ACT_58_XIWU);
			}
		}else{
			//判断是否房间里
			if ($this->info['state'] == 0){
				Master::error(ACT_58_NOROOM);
			}
		}
	}
	
	/*
	 * 刷新T人冷却时间
	 */
	public function ti_code($uid){
		$this->info['tcode'][$uid] = Game::get_now() + 600;
		//检查 删除过期冷却时间
		foreach ($this->info['tcode'] as $fuid=>$time){
			if (Game::is_over($time)){
				unset($this->info['tcode'][$fuid]);
			}
		}
		$this->save();
	}
	
	/*
	 * 检查放学 / 被T
	 * 如果出发了 被T或者完成学习 返回true
	 */
	public function click_over(){
		if ($this->info['etime'] > 0 &&
		Game::is_over($this->info['etime'])){ //下课时间到了
			
			//经验配置
			if (empty($this->info['rlevel'])){
				//如果房间等级异常 
				//进行数据修复
				$this->info['state'] = 0;//状态:游荡中
				//$this->info['ctime'] = 0;
				$this->info['ruid'] = 0;
				$this->info['stime'] = 0;
				$this->info['etime'] = 0;
				$this->info['rlevel'] = 0;
				$this->info['tuid'] = 0;
				$this->save();
				return true;
			}
			$hanlin_exp_cfg_info = Game::getcfg_info('hanlin_exp',$this->info['rlevel']);
			$st = $this->info['etime'] - $this->info['stime'];//学习时间
			$exp = $hanlin_exp_cfg_info['exp'] * floor($st/60);//计算经验
			if ($exp > 0){
				$this->info['exp'] += $exp;//加上翰林经验
				//经验值流水
				Game::cmd_flow(22, 1, $exp, $this->info['exp']);
			}
		
			if ($this->info['state'] == 2){//被人T了
				//弹窗显示被T
				$t_win = array(
					'fuser' => Master::fuidInfo($this->info['tuid']),
					'score' =>$exp,
					'time' => $st,
				);
				Master::$bak_data['a']['hanlin']['win']['tim'] = $t_win;
				Master::back_s(2);
			}elseif ($this->info['state'] == 1){//顺利下课
				//弹窗显示下课
				$t_win = array(
					'score' =>$exp,
					'time' => $st,
				);
				Master::$bak_data['a']['hanlin']['win']['fang'] = $t_win;
				Master::back_s(3);
			}else{
				//状态错误
				Master::error("click_over_".$this->info['state']);
			}
		}else{
			//无动作
			return false;
		}
		
		
		
		//进行放学操作
		$this->info['state'] = 0;//状态:游荡中
		//$this->info['ctime'] = 0;
		$this->info['ruid'] = 0;
		$this->info['stime'] = 0;
		$this->info['etime'] = 0;
		$this->info['rlevel'] = 0;
		$this->info['tuid'] = 0;
		
		$this->save();
		return true;
	}
	
	/*
	 * 坐下座位
	 * 教室UID
		'ruid' => 0,//房间号码
		'etime' => 0,//房间完成时间
		'rlevel' => 0,//房主等级
	 */
	public function sitdown($fuser){
		$this->info['state'] = 1;//状态:上学
		$this->info['ruid'] = $fuser['uid'];//房主UID
		$this->info['stime'] = Game::get_now();
		$this->info['etime'] = $fuser['num'];//结束时间
		$this->info['rlevel'] = $fuser['level'];//房主等级
		
		$this->save();
	}
	
	/*
	 * 被T
	 */
	public function ti($tiud){
		$this->info['state'] = 2;//状态:被T
		$this->info['etime'] = Game::get_now();//结束时间
		$this->info['ctime'] = Game::get_now() + 600;//下次进房 冷却时间
		$this->info['tuid'] = $tiud;//T的人
		$this->save();
	}
	
	/*
	 * 升级翰林技能
	 */
	public function upskill(){
		//翰林加成配置
		$hanlin_skill_cfg_info = Game::getcfg_info('hanlin_skill',$this->info['level'] + 1);
		
		//升级翰林技能所需经验
		$hanlin_skill_cfg_info['costExp'];
		if ($this->info['exp'] < $hanlin_skill_cfg_info['costExp']){
			Master::error(ACT_58_BUZU);
		}
		
		//减去经验
		$this->info['exp'] -= $hanlin_skill_cfg_info['costExp'];
		//加上等级
		$this->info['level'] += 1;
		//经验值流水
		Game::cmd_flow(22, 1, -$hanlin_skill_cfg_info['costExp'], $this->info['exp']);
		
		$this->save();
		
		//提示提示技能的门客列表 //$this->info['level']
		$HeroModel = Master::getHero($this->uid);
		//遍历有这个技能的门客
		$upwin = array();
		foreach ($HeroModel->info as $v){
			foreach ($v['epskill'] as $skid => $skv){
				if ($skid == $hanlin_skill_cfg_info['skillId']){
					$upwin[] = array(
						'heroid' => $v['heroid'],
						'skillid' => $hanlin_skill_cfg_info['skillId'],
						'lv' => 1,
					);
					//设置标记这个门客要刷新
					Master::add_hero_rst($v['heroid']);
					break;
				}
			}
		}
		Master::$bak_data['a']["hanlin"]['win']["upskill"] = $upwin;
		
		return;
	}
	
	/*
	 * 获取翰林技能等级
	 */
	public function getskill(){
		//翰林加成配置
		$hanlin_skill_cfg = Game::getcfg('hanlin_skill');
		
		$skill_lv = array();
		foreach ($hanlin_skill_cfg as $v){
			if ($v['id'] > $this->info['level']){
				break;
			}
			if (isset($skill_lv[$v['skillId']])){
				$skill_lv[$v['skillId']] += 1;
			}else{
				$skill_lv[$v['skillId']] = 1;
			}
		}
		return $skill_lv;
	}
	
}
















