<?php
require_once "ActBaseModel.php";
/*
 *  第一次 生小孩
 */
class Act94Model extends ActBaseModel
{
	public $atype = 94;//活动编号

	public $comment = "第一次生小孩";
	public $b_mol = "wife";//返回信息 所在模块
	public $b_ctrl = "firstborn";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'id' => 0,  //  1:第一次生小孩
	);
	
	public function do_save(){
		if(empty($this->info['id'])){
		    $this->info['id'] = 1;
		    $this->save();
		}
	}
	
}
