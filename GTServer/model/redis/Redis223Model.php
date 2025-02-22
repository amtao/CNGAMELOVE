<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 联盟副本伤害排行榜
 */
class Redis223Model extends RedisSimpleBaseModel
{
	public $comment = "联盟副本伤害排行榜";
	public $act = 'huodong_223';//活动标签
}