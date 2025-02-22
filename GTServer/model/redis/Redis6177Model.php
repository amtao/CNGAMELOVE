<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 限时酒楼积分涨幅排行榜
 */
class Redis6177Model extends RedisSimpleBaseModel
{
	public $comment = "限时奖励-出城寻访次数排行榜";
	public $act = 'huodong_6177';//活动标签
}