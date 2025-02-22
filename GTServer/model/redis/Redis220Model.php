<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 活力丹消耗排行榜
 */
class Redis220Model extends RedisSimpleBaseModel
{
	public $comment = "活力丹消耗排行榜";
	public $act = 'huodong_220';//活动标签
}