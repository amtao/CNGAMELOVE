<?php
require_once "RedisBaseModel.php";
class RedisKuaCfgBaseModel extends RedisBaseModel
{
    protected $_server_type = 4;//1：合服，2：跨服，3：全服，4：指定跨服
}