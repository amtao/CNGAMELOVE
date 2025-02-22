<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 体力丹消耗排行榜
 */
class Redis219Model extends RedisSimpleBaseModel
{
	public $comment = "体力丹消耗排行榜";
	public $act = 'huodong_219';//活动标签
}