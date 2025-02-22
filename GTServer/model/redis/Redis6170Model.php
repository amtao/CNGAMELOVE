<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 限时酒楼积分涨幅排行榜
 */
class Redis6170Model extends RedisSimpleBaseModel
{
	public $comment = "限时珍宝阁累计整理关卡次数排行榜";
	public $act = 'huodong_6170';//活动标签
}