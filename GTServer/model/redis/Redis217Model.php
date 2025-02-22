<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 惩戒犯人次数排行榜
 */
class Redis217Model extends RedisSimpleBaseModel
{
	public $comment = "惩戒犯人次数排行榜";
	public $act = 'huodong_217';//活动标签
}