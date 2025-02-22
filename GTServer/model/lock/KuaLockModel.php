<?php
require_once "BaseLockModel.php";
/**
 * 跨服锁
 * Class KuaLockModel
 */
class KuaLockModel extends BaseLockModel
{
    protected $_server_type = 2;//1：合服，2：跨服，3：全服
    protected $_key_pre = "vo_lock_kua_";
}