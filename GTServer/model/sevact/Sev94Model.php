<?php
/*
 * 许愿池
 */
require_once "SevListBaseModel.php";
class Sev94Model extends SevListBaseModel
{
    public $comment = "许愿池";
    public $act = 94;
    public $b_mol = "wishingWell";//返回信息 所在模块
    public $b_ctrl = "bigRwdLog";//子类配置
    public $hd_id = 'huodong_8003';//活动配置文件关键字
    public $hd_cfg;
    protected $_server_type = 1;//1：合服，2：跨服，3：全服

    protected $_use_lock = false;//是否加锁
    public $chat_info_num = 20;//初始/自动 发送条数
    public $chat_history_num = 20;//每次历史滚动条数

    public function __construct($hid,$cid, $servid)
    {

        //获取活动配置
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        if (empty($this->hd_cfg)){
            return ;
        }

        parent::__construct($hid,$cid, $servid);

    }

    /*
     * 添加一条信息
     */
    public function add_msg($uid,$item, $itemNum){

        //判断是不是官方
        $data = array(
            'user' => Master::fuidInfo($uid),
            'uid' => $uid,
            'item' => $item,
            'itemNum' => $itemNum,
            'time' => Game::get_now(),
        );
        parent::list_push($data);
    }
}