<?php
require_once "BaseLockModel.php";
/**
 * 合服锁
 * Class DftVoComModel
 */
class DftLockModel extends BaseLockModel
{
    protected $_server_type = 1;//1：合服，2：跨服，3：全服
}