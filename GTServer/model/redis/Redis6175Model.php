<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 限时酒楼积分涨幅排行榜
 */
class Redis6175Model extends RedisSimpleBaseModel
{
	public $comment = "限时奖励-郊祀献礼次数排行榜";
	public $act = 'huodong_6175';//活动标签
}