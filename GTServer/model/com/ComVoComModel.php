<?php
require_once "BaseVoComModel.php";
/**
 * 全服公共存储
 * Class ComVoComModel
 */
class ComVoComModel extends BaseVoComModel
{
    protected $_server_type = 3;//1：合服，2：跨服，3：全服
    protected $_key_pre = "vo_common_com_";
}