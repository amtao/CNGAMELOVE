<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 黄金消耗排行榜
 */
class Redis201Model extends RedisSimpleBaseModel
{
	public $comment = "黄金消耗排行榜";
	public $act = 'huodong_201';//活动标签
}

