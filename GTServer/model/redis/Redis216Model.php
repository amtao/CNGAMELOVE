<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 挑战书消耗排行榜
 */
class Redis216Model extends RedisSimpleBaseModel
{
	public $comment = "挑战书消耗排行榜";
	public $act = 'huodong_216';//活动标签
}