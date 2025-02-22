<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动225
 */
class Act225Model extends ActHDBaseModel
{
	public $atype = 225;//活动编号
	public $comment = "限时奖励-酒楼积分涨幅";
	public $b_ctrl = "jiulouzf";//子类配置
	public $hd_id = 'huodong_225';//活动配置文件关键字
    protected $_rank_id = 225;
}
