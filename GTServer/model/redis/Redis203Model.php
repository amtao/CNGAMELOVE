<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 银两消耗排行榜
 */
class Redis203Model extends RedisSimpleBaseModel
{
	public $comment = "银两消耗排行榜";
	public $act = 'huodong_203';//活动标签
}

