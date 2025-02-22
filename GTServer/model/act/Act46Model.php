<?php
require_once "ActBaseModel.php";
/*
 * 书院学习记录
 */
class Act46Model extends ActBaseModel
{
	public $atype = 46;//活动编号
	
	public $comment = "书院学习记录";
    public $b_mol = "school";//返回信息 所在模块
    public $b_ctrl = "record";//返回信息 所在控制器

    public $_init = array();

    public function set($hids){
        $this->info = $hids;
        $this->save();
    }
}














