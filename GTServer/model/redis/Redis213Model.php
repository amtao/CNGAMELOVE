<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 经营农产次数排行榜
 */
class Redis213Model extends RedisSimpleBaseModel
{
	public $comment = "经营农产次数排行榜";
	public $act = 'huodong_213';//活动标签
}