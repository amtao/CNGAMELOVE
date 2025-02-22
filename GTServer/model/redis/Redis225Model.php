<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 限时酒楼积分涨幅排行榜
 */
class Redis225Model extends RedisSimpleBaseModel
{
	public $comment = "限时酒楼积分涨幅排行榜";
	public $act = 'huodong_225';//活动标签
}