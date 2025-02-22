<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 限时酒楼积分涨幅排行榜
 */
class Redis6171Model extends RedisSimpleBaseModel
{
	public $comment = "限时奖励-祈福次数排行榜";
	public $act = 'huodong_6171';//活动标签
}