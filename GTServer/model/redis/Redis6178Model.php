<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 限时酒楼积分涨幅排行榜
 */
class Redis6178Model extends RedisSimpleBaseModel
{
	public $comment = "限时奖励-徒弟历练次数排行榜";
	public $act = 'huodong_6178';//活动标签
}