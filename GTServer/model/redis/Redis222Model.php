<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 赴宴次数排行榜
 */
class Redis222Model extends RedisSimpleBaseModel
{
	public $comment = "赴宴次数排行榜";
	public $act = 'huodong_222';//活动标签
}