<?php
/*
 * 聊天封设备
 */
require_once "SevComBaseModel.php";
class Sev27Model extends SevComBaseModel
{
	public $comment = "聊天封设备";
	public $act = 27;//活动标签
	public $openid;
	protected $_use_lock = false;//是否加锁
	public $_init = array(//初始化数据
	    /*
         *   'openid' => $time   //'openid' => 封设备时间
	     */
	);


    /**
     * 封设备
     * @param $uid  玩家uid
     */
    public function add($uid){
        $openid = Common::getOpenid($uid);
        $this->info[$openid] = time();
        $this->save();
    }

    /**
     * 解封设备
     * @param $openid  设备id
     */
    public function remove($openid){
        unset($this->info[$openid]);
        $this->save();
    }

    /**
     * 是否被封设备
     * @param $uid
     * @return array
     */
    public function isBandSb($uid){
        $openid = Common::getOpenid($uid);
        return empty($this->info[$openid]) ? array() : $this->info[$openid];
    }
}





