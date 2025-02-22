<?php
require_once "BaseVoComModel.php";
/**
 * 跨服公共存储
 * Class KuaVoComModel
 */
class KuaVoComModel extends BaseVoComModel
{
    protected $_server_type = 2;//1：合服，2：跨服，3：全服
    protected $_key_pre = "vo_common_kua_";
}