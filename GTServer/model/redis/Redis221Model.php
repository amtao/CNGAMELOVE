<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 魅力值涨幅排行榜
 */
class Redis221Model extends RedisSimpleBaseModel
{
	public $comment = "魅力值涨幅排行榜";
	public $act = 'huodong_221';//活动标签
}