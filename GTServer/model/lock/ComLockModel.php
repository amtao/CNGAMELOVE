<?php
require_once "BaseLockModel.php";
/**
 * 全服锁
 * Class ComLockModel
 */
class ComLockModel extends BaseLockModel
{
    protected $_server_type = 3;//1：合服，2：跨服，3：全服
    protected $_key_pre = "vo_lock_com_";
}