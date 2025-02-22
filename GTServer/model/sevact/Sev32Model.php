<?php
/*
 * 新官上任-boss血量
 */
require_once "SevListBaseModel.php";
class Sev32Model extends SevListBaseModel
{
	public $comment = "新官上任-boss血量";
	public $act = 32;//活动标签
	public $_init = array(//初始化数据
		/*
		 * array(
		 * 	'shijian' => '血量'
		 * )
		 */
	);
	
	/*
	 * 添加伤害
	 */
	public function add($hp){
	    $time = date('Ymd',time());
	    if(empty($this->info[$time])){
	        $this->info[$time] = 0;
	    }
	    $this->info[$time] += $hp;
		$this->save();
	}
	
	public function back_data(){
	    $time = date('Ymd',time());
	    Common::loadModel('HoutaiModel');
	    $hd_cfg = HoutaiModel::get_huodong_info('huodong_280');
	    
	    $all = $hd_cfg['boss'];
	    
	    if(empty($this->info[$time])){
	        $this->info[$time] = 0;
	    }
	    
	    $boss = $all - $this->info[$time];
	    
	    Master::back_data(0,"xghuodong","boss",array('boss'=>$boss));
	}
	
}
