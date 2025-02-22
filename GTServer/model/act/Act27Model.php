<?php
require_once "ActBaseModel.php";
/*
 * 寻访-赈灾-运势恢复
 */
class Act27Model extends ActBaseModel
{
	public $atype = 27;//活动编号
	
	public $comment = "寻访-赈灾-运势恢复";
	public $b_mol = "xunfang";//返回信息 所在模块
	public $b_ctrl = "recover";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		'num' => 90,  //当前运势
		'time'  => 0,  //上次恢复时间点
		'auto2' => 0, //0:自动银两赈灾未设置 1:自动银两赈灾已设置
		'auto3' => 0, //0:自动粮草赈灾未设置 1:自动粮草赈灾已设置
		'ysSet' => 90, //运势设置
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//计算恢复时间
		$hf_num = Game::hf_num($this->info['time'],60*5,$this->info['num'],90);
		
		$this->info['num'] = $hf_num['num'];
		$this->info['time'] = $hf_num['stime'];
		
		$outf = array(
			'num' => $this->info['num'],
			'auto2' => $this->info['auto2'],
			'auto3' => $this->info['auto3'],
			'ysSet' => $this->info['ysSet'],
		);
		//构造输出
		$this->outf = $outf;
	}
	
	/**
	 * 加运势
	 * @param $num  加的运势值
	 */
	public function add_ys($num,$max = 100 ,$flag = true){
		if($this->info['num'] >= $max && $flag){
			Master::error(LOOK_FOR_FATE_FULL);
		}
		$this->info['num'] += $num;
		$this->info['num'] = min($max,$this->info['num']);
		$this->save();
	}
	
	/**
	 * 减运势
	 * @param $num  减的运势值
	 */
	public function sub_ys($num){
		$this->info['num'] -= $num;
		if($this->info['num'] <= 1 ){
			Master::error(LOOK_FOR_FATE_SHORT);
		}
		$this->save();
	}
	
	/**
	 * 0:自动银两赈灾未设置 1:自动银两赈灾已设置
	 * @param unknown_type $type
	 */
	public function set_auto2($type){
		$this->info['auto2'] = $type;
		$this->save();
	}
	
	/**
	 * 0:自动粮草赈灾未设置 1:自动粮草赈灾已设置
	 * @param unknown_type $type
	 */
	public function set_auto3($type){
		$this->info['auto3'] = $type;
		$this->save();
	}
	
	/**
	 * 运势设置
	 * @param unknown_type $type
	 */
	public function set_ys($num){
		$this->info['ysSet'] = $num;
		$this->save();
	}
	
	
}
















