<?php
require_once "ActBaseModel.php";
/*
 * 四海奇珍模块
 */
class Act319Model extends ActBaseModel
{
	public $atype = 319;//活动编号
	
	public $comment = "四海奇珍模块";
	public $b_mol = "baowu";//返回信息 所在模块
	public $b_ctrl = "baowusys";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		//'num' => 0,//充值最高金额
		'poolstate' => array(
			'1'=>array(
				'freetime'=>0,
			)
		),
		'cloth' => array(
		),
		'drawCount'=>0,
	);
	
	
	public function draw_card($drawtype,$Poolid){

	}
	public function check_free($Poolid){
		//目前只有奖池1免费，通用表格确定后再加
		if($Poolid !=1 ){
			return false;
		}
			
		if(empty( $this->info['poolstate'][$Poolid])){
			return true;
		}
			
		if(Game::is_over($this->info['poolstate'][$Poolid]['freetime']))
		{
			return true;
		}
		return false;
	}
	public function set_freeuse($Poolid,$time){
		if(empty($this->info['poolstate'][$Poolid])){
			$this->info['poolstate'][$Poolid] = array();
		}
		$this->info['poolstate'][$Poolid]['freetime'] = Game::get_over($time);
		$this->save();
	}
	public function has_clothe($cardid){
		return !empty($this->info['cloth'][$cardid]);
	}
	public function unlock_clothe($cardid){
		$this->info['cloth'][$cardid] = 1;
		$this->save();
	}
	public function getIsFirst(){
		if ($this->info['drawCount']<=0){
			$this->info['drawCount'] = 1;
			$this->save();
			return true;
		}
		return false;
	}
}