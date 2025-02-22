<?php
require_once "ActBaseModel.php";
/*
 * 好友系统
 */
class Act130Model extends ActBaseModel
{
	public $atype = 130;//活动编号
	public $comment = "好友系统";
	public $b_mol = "friends";//返回信息 所在模块
	public $b_ctrl = "flist";//返回信息 所在控制器
	protected $_save_msg = true; //是否更新缓存

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'list' => array(),
	);

	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$key = $this->uid.'_friend_list';
		$cache = Common::getDftMem();
	    $outof = $cache->get($key);
	    if( $this->_save_msg || empty($outof) ){
	    	$outof = array();
	    	if( !empty($this->info['list']) ){
		    	foreach($this->info['list'] as $k => $v){

		    		$friendInfo = Master::getFriendInfo($k);
		    		$outof[] = $friendInfo;
		    	}
	    	}
			$cache->set($key,$outof);
	    }

	    if (!empty($outof)) {

	    	$FriendModel = Master::getFriend($this->uid);
		    $Act8023Model = Master::getAct8023($this->uid);
		    $sendList = empty($Act8023Model->info["send"]) ? array() : $Act8023Model->info["send"];
	    	foreach ($outof as $k => $v) {

		    	$loveInfo = $FriendModel->getLoveInfo($v["uid"]);
	    		$outof[$k]["love"] = intval($loveInfo["love"]);
	    		$outof[$k]["level"] = intval($loveInfo["level"]) <= 0 ? 1 : intval($loveInfo["level"]);
	    		$outof[$k]["isSend"] = 0;

	    		if (count($sendList) > 0 && in_array($v["uid"], $sendList)) {
	    			$outof[$k]["isSend"] = 1;
	    		}
		    }

	    	$flag=array();
	        foreach($outof as $arr2){
	            $flag[] = $arr2["shili"];
	        }
	        array_multisort($flag, SORT_DESC, $outof);
	    }

		//默认输出直接等于内部存储数据
		$this->outf = $outof;
	}

	/**
	 * 添加好友
	 * @param $uid
	 */
	public function add($uid){
		$userModel = Master::getUser($this->uid);
		$vip_cfg_info = Game::getcfg_info('vip',$userModel->info['vip']);
		if( count($this->info['list']) >= $vip_cfg_info['friendNum']){
			if ($this->uid == $uid) {
				Master::error(FRIEND_NUM_MAX);
			} else {
				Master::error(FRIEND_NUM_MAX);
			}

		}
		$this->_save_msg = true;
		$this->info['list'][$uid] = 1;

		$FriendModel = Master::getFriend($this->uid);
		$FriendModel->add_friend($uid);

		$this->save();
	}

	/**
	 * 添加好友
	 * @param $uid
	 */
	public function add1($uid){
		$this->_save_msg = true;
		$this->info['list'][$uid] = 1;

		$FriendModel = Master::getFriend($this->uid);
		$FriendModel->add_friend($uid);
		$this->save();
	}

	/**
	 * 删除好友
	 * @param $uid
	 */
	public function sub($uid){
		if(empty($this->info['list'][$uid])){
			return;
		}
		$this->_save_msg = true;
		unset($this->info['list'][$uid]);

		if (empty($this->info['list'])) {
			$this->info['list'] = array();
		}

		$FriendModel = Master::getFriend($this->uid);
		$FriendModel->del_friend($uid);

		$this->save();
	}

	public function get_news() {
		return 0;
	}

	/*
	 * 返回活动信息
	 */
	public function back_data(){
		$this->make_out();
		Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
	}
}
