<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 联盟副本击杀排行榜
 */
class Redis224Model extends RedisSimpleBaseModel
{
	public $comment = "联盟副本击杀排行榜";
	public $act = 'huodong_224';//活动标签
}