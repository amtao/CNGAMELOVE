<?php
require_once "ActBaseModel.php";
/*
 * 卡牌剧情模块
 */
class Act318Model extends ActBaseModel
{
	public $atype = 318;//活动编号
	
	public $comment = "卡牌剧情模块";
	public $b_mol = "card";//返回信息 所在模块
	public $b_ctrl = "cardstory";//返回信息 所在控制器
	
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