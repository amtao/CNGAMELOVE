<?php
require_once "RedisSimpleBaseModel.php";
/**
 * 击杀葛尔丹次数排行榜
 */
class Redis215Model extends RedisSimpleBaseModel
{
	public $comment = "击杀葛尔丹次数排行榜";
	public $act = 'huodong_215';//活动标签
}