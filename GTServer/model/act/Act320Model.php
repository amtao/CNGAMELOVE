<?php
require_once "ActBaseModel.php";
/*
 * 卡牌剧情模块
 */
class Act320Model extends ActBaseModel
{
	public $atype = 320;//活动编号
	
	public $comment = "四海奇珍剧情模块";
	public $b_mol = "baowu";//返回信息 所在模块
	public $b_ctrl = "baowustory";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
	);
	

	public function read_story($storyid){
		if(empty($this->info[$storyid])){
			$this->info[$storyid] = 1;
			$this->save();
		}	
	}
}