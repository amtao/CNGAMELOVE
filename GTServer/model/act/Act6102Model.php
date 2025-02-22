<?php
require_once "ActBaseModel.php";
/*
 * 书院学习记录
 */
class Act6102Model extends ActBaseModel
{
	public $atype = 6102;//活动编号
	
	public $comment = "御膳房记录";
    public $b_mol = "kitchen";//返回信息 所在模块
    public $b_ctrl = "record";//返回信息 所在控制器

    public $_init = array();

    public function set($wids){
        $this->info = $wids;
        $this->save();
    }
}














