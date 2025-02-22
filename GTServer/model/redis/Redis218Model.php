<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 赈灾次数排行榜
 */
class Redis218Model extends RedisSimpleBaseModel
{
	public $comment = "赈灾次数排行榜";
	public $act = 'huodong_218';//活动标签
}