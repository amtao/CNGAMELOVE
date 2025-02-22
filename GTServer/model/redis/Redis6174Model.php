<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 限时酒楼积分涨幅排行榜
 */
class Redis6174Model extends RedisSimpleBaseModel
{
	public $comment = "限时奖励-问候知己次数排行榜";
	public $act = 'huodong_6174';//活动标签
}