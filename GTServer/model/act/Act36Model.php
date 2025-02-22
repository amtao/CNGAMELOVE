<?php
require_once "ActBaseModel.php";
/*
 * 成就
 */
class Act36Model extends ActBaseModel
{
	public $atype = 36;//活动编号
	
	public $comment = "成就";
	public $b_mol = "chengjiu";//返回信息 所在模块
	public $b_ctrl = "cjlist";//返回信息 所在控制器
	
	
	public $outf_id;//更新ID标记
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 * id => array(
		 * 	 'num' => 0,//累计次数
		 *   'rwd' => 0//领奖档次
		 * )
		 */
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$this->outf = array();
		
		foreach ($this->info as $k => $v){
			$this->outf[] = array(
				'id' => $k,
				'num' => $v['num'],
				'rwd' => $v['rwd'],
			);
		}
	}

	public function setTask($type){
		$cjInfoCfg = Game::getcfg('cj_list');
		foreach($cjInfoCfg as $v){
			if($v['type'] == $type){
				if (empty($this->info[$v['id']])){
					$this->info[$v['id']] = array(
						'num' => 0,//累计次数
						'rwd' => 0//领奖档次
					);
				}
				$Act39Model = Master::getAct39($this->uid);
				$this->info[$v['id']]['num'] = $Act39Model->task_num($type);
				$this->save();
				$this->outf_id[$v['id']] = 1;
				break;
			}
		}
	}
	
	/*
	 * 累计成就数量 增加
	 */
	public function add($id,$num = 1){
		if ($num <= 0){
			return;
		}
		if (empty($this->info[$id])){
			$this->info[$id] = array(
				'num' => 0,//累计次数
				'rwd' => 0//领奖档次
			);
		}
		$this->info[$id]['num'] += $num;
		$this->save();
		
		//更新标记
		$this->outf_id[$id] = 1;
	}
	
	/*
	 * 累计成就数量 设置
	 */
	public function set($id,$num = 1){
		if (empty($this->info[$id])){
			$this->info[$id] = array(
				'num' => 0,//累计次数
				'rwd' => 0//领奖档次
			);
		}
		$this->info[$id]['num'] = $num;
		$this->save();
		
		//更新标记
		$this->outf_id[$id] = 1;
	}
	
	/*
	 * 获取成就奖励
	 */
	public function rwd($id){
		if (empty($this->info[$id])){
			Master::error('chenjiu_rwd_err_'.$id);
		}
		
		//当前领奖档次
		$this->info[$id]['rwd'];
		//领奖目标档次
		$rwdid = $this->info[$id]['rwd'] + 1;
		
		//配置
		$cj_rwd_info = Game::getcfg_info('cj_info',$id);
		
		//配置溢出 已经领完?
		if (!isset($cj_rwd_info[$rwdid])){
			Master::error(ACT_36_LINGWAN.$rwdid);
		}
		
		//判断是否达到了下一档次奖励
		if ($cj_rwd_info[$rwdid]['need'] > $this->info[$id]['num']){
//			Master::error(ACHIEVEMENT_UN_TO_ACHIEVE);
            Master::error();
		}
		
		//发放成就奖励
		foreach ($cj_rwd_info[$rwdid]['rwd'] as $v){
			Master::add_item2($v);
		}
		
		//更新领奖记录
		$this->info[$id]['rwd'] = $rwdid;
		$this->save();
		//更新标记
		$this->outf_id[$id] = 1;

	}
	
	/*
	 * 返回活动数据
	 * 初始化返回
	 * 更新返回?
	 */
	public function back_data_u(){
		//遍历更新标记信息
		$outf_u = array();
		foreach ($this->outf_id as $id => $v){
			$outf_u[] = array(
				'id' => $id,
				'num' => $this->info[$id]['num'],
				'rwd' => $this->info[$id]['rwd'],
			);
		}
		
		//返回更新信息
		Master::$bak_data['u'][$this->b_mol][$this->b_ctrl] = $outf_u;
	}
	
}
















