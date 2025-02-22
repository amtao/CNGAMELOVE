<?php
require_once "RedisBaseModel.php";
/**
 * 角色删除
 */
class Redis1000Model extends RedisBaseModel
{
    public $comment = "角色删除";
    public $act = 'role_del';//活动标签

    protected $_server_type = 3;

    public $b_mol = "";//返回信息 所在模块
    public $b_ctrl = "";//返回信息 所在控制器

    /**
     * 获取列表信息
     * @return mixed
     */
    public function getList(){
        return $this->zRevRange();
    }
}

