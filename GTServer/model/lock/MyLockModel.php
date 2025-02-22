<?php
require_once "BaseLockModel.php";
/**
 * 本服锁
 * Class MyLockModel
 */
class MyLockModel extends BaseLockModel
{
    protected $_server_type = 4;//1：合服，2：跨服，3：全服
}