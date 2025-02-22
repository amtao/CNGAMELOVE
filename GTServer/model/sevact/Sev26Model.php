<?php
/*
 * 聊天封号
 */
require_once "SevBaseModel.php";
class Sev26Model extends SevBaseModel
{
	public $comment = "聊天封号";
	public $act = 26;//活动标签
	protected $_use_lock = false;//是否加锁
	public $_init = array(//初始化数据
	    /*
         *   'uid' => $time   //'uid' => 封号时间
	     */
	);

    /**
     * 封号
     * @param $uid  玩家uid
     */
    public function add($uid){
        $he_id = Common::getSevCfgObj(Game::get_sevid($uid))->getHE();
        $SevCfg = Common::getSevidCfg();
       /* if($he_id != $SevCfg['he']){
            return false;
        }*/
        $this->info[$uid] = time();
        $this->save();
        return true;
    }

    /**
     * 解封
     * @param $uid  玩家uid
     */
    public function remove($uid){
        unset($this->info[$uid]);
        $this->save();
    }

    /**
     * 是否被封号
     * @param $uid
     * @return bool
     */
    public function isClosure($uid){
        if(empty($uid)) Master::error(SEV_26_IDNONULL);
        return empty($this->info[$uid]) ? array() : $this->info[$uid];
    }
}





