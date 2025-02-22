<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 限时酒楼积分涨幅排行榜
 */
class Redis6176Model extends RedisSimpleBaseModel
{
	public $comment = "限时奖励-皇子应援次数排行榜";
	public $act = 'huodong_6176';//活动标签
}