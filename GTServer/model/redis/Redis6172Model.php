<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 限时酒楼积分涨幅排行榜
 */
class Redis6172Model extends RedisSimpleBaseModel
{
	public $comment = "限时奖励-精力丹消耗排行榜";
	public $act = 'huodong_6172';//活动标签
}