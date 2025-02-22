<?php
/*
 * 跨服聊天禁言
 */
require_once "SevBaseModel.php";
class Sev39Model extends SevBaseModel
{
	public $comment = "跨服聊天禁言";
	public $act = 39;//活动标签
	protected $_use_lock = false;//是否加锁
	protected $_server_type = 3;//1：合服，2：跨服，3：全服
	public $_init = array(//初始化数据
	    /*
         *  'uid' => 'time'
	     */
	);

	/**
	 * 禁言
     * @param $uid  玩家uid
	 */
	public function add($uid,$status = 0){
        $time = Game::get_now();
        if($status == 1){
            $time = $time+10*365*24*3600;
        }
		$this->info[$uid] = $time;
		$this->save();
	}

    /**
     * 解禁
     * @param $uid  玩家uid
     */
    public function remove($uid){
        unset($this->info[$uid]);
        $cache = Common::getMyMem ();
        $cache->delete($uid.'_limit_chat_kuafu');
        $this->save();
    }
    /**
     * 是否被禁言
     * @param $uid
     * @return bool
     */
    public function isBanTalk($uid){
        if(empty($uid)) Master::error(SEV_23_UIDNONULL);
        $status = 0;
        if(!empty($this->info[$uid])){
            $status = 1;
            if($_SERVER['REQUEST_TIME'] - 5*60 > $this->info[$uid]){//超过一天自动解禁
                unset($this->info[$uid]);
                $cache = Common::getMyMem ();
                $cache->delete($uid.'_limit_chat_kuafu');
                $this->save();
                $status = 0;
            }
        }
        return $status;
    }
    
    /*
     * 自动禁言判断
     * */
    public function autoBanTalk($uid,$msg='') {
        $cache = Common::getMyMem ();
        $limit_chat = $cache->get($uid.'_limit_chat_kuafu');
        $len = strlen($msg);
        if($len > 60){//字数大于40个字节
            if(empty($limit_chat) || $limit_chat['msg'] != $msg || ($_SERVER['REQUEST_TIME'] - $limit_chat['ctime'] > 300)){
                $limit_chat = array(
                    'msg' => $msg,
                    'count' => 1,
                    'ctime' => $_SERVER['REQUEST_TIME'],
                );
            }else{
                $limit_chat['count'] += 1;
                if( $limit_chat['count'] >= 5){    
                    $this->add($uid);
                    $Act98Model = Master::getAct98($uid);
                    $Act98Model->add_kf_msg($limit_chat);
                }
            }
            $cache->set($uid.'_limit_chat_kuafu',$limit_chat);
        }else{
            $cache->delete($this->uid.'_limit_chat_kuafu');
        }
    }
}





