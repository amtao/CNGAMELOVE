<?php
require_once "ActBaseModel.php";
/*
 *   卷轴升级伪概率
 */
class Act92Model extends ActBaseModel
{
	public $atype = 92;//活动编号

	public $comment = "卷轴升级伪概率";
	public $b_mol = "";//返回信息 所在模块
	public $b_ctrl = "";//返回信息 所在控制器
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
 		'门客id' => array(
			属性技能id => 失败次数,
		*/
	);
	
	//获取伪概率
	public function get_prob($hid,$skid){
	    //初始化次数
		if(empty($this->info[$hid][$skid])){
			$this->info[$hid][$skid] = 0;
		}
		return $this->info[$hid][$skid] * 5;
	}
	
	//添加为概率
	public function add_prob($hid,$skid){
	    //初始化次数
		if(empty($this->info[$hid][$skid])){
			$this->info[$hid][$skid] = 0;
		}
		$this->info[$hid][$skid] += 1;
		$this->save(); 
	}
	
	//清除增加的概率
	public function clear_prob($hid,$skid){
	    //初始化次数
		if(empty($this->info[$hid][$skid])){
			$this->info[$hid][$skid] = 0;
		}
		$this->info[$hid][$skid] = 0;
		$this->save(); 
	}

	/*
	 * 返回活动信息
	 */
	public function back_data(){
		
		
	}
	
}
