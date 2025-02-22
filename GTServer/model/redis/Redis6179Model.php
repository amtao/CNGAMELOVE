<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 限时酒楼积分涨幅排行榜
 */
class Redis6179Model extends RedisSimpleBaseModel
{
	public $comment = "限时奖励-御膳房烹饪次数排行榜";
	public $act = 'huodong_6179';//活动标签
}