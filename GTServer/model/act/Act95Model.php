<?php
require_once "ActBaseModel.php";
/*
 *  第一次 科举
 */
class Act95Model extends ActBaseModel
{
	public $atype = 95;//活动编号

	public $comment = "第一次科举";
	public $b_mol = "son";//返回信息 所在模块
	public $b_ctrl = "firstkeju";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'id' => 0,  //  1:第一次科举
	);
	
	public function do_save(){
		if(empty($this->info['id'])){
		    $this->info['id'] = 1;
		    $this->save();
		}
	}
	
}
