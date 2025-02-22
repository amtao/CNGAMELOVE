<?php
/*
 * 通服基类
 */
require_once "SevBaseModel.php";
class SevComBaseModel extends SevBaseModel
{
    protected $_server_type = 3;//1：合服，2：跨服，3：全服
}