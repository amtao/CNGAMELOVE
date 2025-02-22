<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 累计登录天数排行榜
 */
class Redis208Model extends RedisSimpleBaseModel
{
	public $comment = "累计登录天数排行榜";
	public $act = 'huodong_208';//活动标签
}

