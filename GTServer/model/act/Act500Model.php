<?php
require_once "ActBaseModel.php";
/*
 *  势力减少记录类
 */
class Act500Model extends ActBaseModel
{
	public $atype = 500;//活动编号

	public $comment = "势力减少记录类";
	public $b_mol = "";//返回信息 所在模块
	public $b_ctrl = "";//返回信息 所在控制器


	/*
	 * 初始化结构体
	 */
	public $_init =  array(

	);

	public function add($data){
	    $this->info = $data;
	    $this->save();
    }
}
