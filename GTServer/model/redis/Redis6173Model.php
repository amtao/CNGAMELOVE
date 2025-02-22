<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 限时酒楼积分涨幅排行榜
 */
class Redis6173Model extends RedisSimpleBaseModel
{
	public $comment = "限时奖励-知己出游次数排行榜";
	public $act = 'huodong_6173';//活动标签
}