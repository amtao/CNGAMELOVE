<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 招募士兵次数排行榜
 */
class Redis214Model extends RedisSimpleBaseModel
{
	public $comment = "招募士兵次数排行榜";
	public $act = 'huodong_214';//活动标签
}