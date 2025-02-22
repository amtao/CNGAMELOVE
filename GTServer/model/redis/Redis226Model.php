<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 限时粮食消耗排行榜
 */
class Redis226Model extends RedisSimpleBaseModel
{
	public $comment = "限时粮食消耗排行榜";
	public $act = 'huodong_226';//活动标签
}