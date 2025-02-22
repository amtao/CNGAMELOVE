<?php
require_once "ActBaseModel.php";
/*
 * 神迹福利
 */
class Act65Model extends ActBaseModel
{
	public $atype = 65;//活动编号
	
	public $comment = "神迹福利";
	public $b_mol = "fuli";//返回信息 所在模块
	public $b_ctrl = "shenji";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		//神迹类型 => 触发次数
	);
	
	/*
	 * 尝试触发神迹
	 * 触发类型ID
	 */
	public function rand($id){
		//每天可以触发的次数
		$UserModel = Master::getUser($this->uid);
		
		//VIP配置
		$vip_cfg = Game::getcfg_info('vip',$UserModel->info['vip']);
		
		
		
		//初始化0次
		if (empty($this->info[$id])){
			$this->info[$id] = 0;
		}
		
		//可触发神迹次数
		$vip_cfg['shenji'];
		
		if ($vip_cfg['shenji'] <= $this->info[$id] ){
			return 0;
		}
		
		//随机触发神迹
		if (rand(1,100) <= 15){
			$this->info[$id] += 1;
			$this->save();
			
			//神迹弹窗
			Master::back_win_array('fuli','shenji',array('id' => $id));
			return 1;
		}
		return 0;
	}
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$outf = array();
		foreach ($this->info as $id => $v){
			$outf[] = array(
				'id' => $id,
				'times' => $v,
			);
		}
		$this->outf = $outf;
	}
	
}
















