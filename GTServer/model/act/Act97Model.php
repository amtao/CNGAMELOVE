<?php
require_once "ActBaseModel.php";
/*
 *  聊天 - 黑名单
 */
class Act97Model extends ActBaseModel
{
	public $atype = 97;//活动编号

	public $comment = "聊天-黑名单";
	public $b_mol = "chat";//返回信息 所在模块
	public $b_ctrl = "blacklist";//返回信息 所在控制器
	protected $_save_msg = true; //是否更新缓存

	/*
	 * 初始化结构体
	 */
	public $_init =  array();

	/*
	 * 加入黑名单
     */
	public function add($fuid){
	    if(empty($fuid) || $fuid == $this->uid){
	        Master::error(CHAT_BLACKLIST_ADD_USER_ERROR);
	    }
	    if(!empty($this->info['list']) && isset($this->info['list'][$fuid])){
	        Master::error(CHAT_BLACKLIST_IN_UID);
	    }
		$this->_save_msg = true;
	    $this->info['list'][$fuid] = $_SERVER['REQUEST_TIME'];
	    $this->save();
	}

	/*
	 * 移除黑名单
	 * */
	public function sub($fuid){
	    if(empty($fuid) || $fuid == $this->uid){
	        Master::error(CHAT_BLACKLIST_ADD_USER_ERROR);
	    }

	    if(empty($this->info['list'])){
	        Master::error(CHAT_BLACKLIST_IS_EMPTY);
	    }

	    if(!isset($this->info['list'][$fuid])){
	        Master::error(CHAT_BLACKLIST_NOFUND_UID);
	    }
		$this->_save_msg = true;

		$list = array();
		foreach ($this->info['list'] as $uid => $time) {

			if($_SERVER['REQUEST_TIME'] - $time > 86400 * 7 ){
                continue;
            }
			if ($uid == $fuid) {
				continue;
			}
			$list[$uid] = $time;
		}

	    $this->info['list'] = $list;
	    $this->save();
	}

	/*
	 *  构造输出值
	 * */
	public function make_out(){
		$key = $this->uid.'_friend_blacklist';
		$cache = Common::getDftMem();
		$outof = $cache->get($key);
		if( $this->_save_msg || empty($outof) ){

			$outof = array();
			$list = array();
			$flag = 0;  //是否进行保存操作   1:保存   0:不保存
			if( !empty($this->info['list']) ){
				foreach($this->info['list'] as $k => $v){

					if($_SERVER['REQUEST_TIME'] - $v > 86400 * 7 ){
		                $flag = 1;
		                continue;
		            }

		            $list[$k] = $v;
		            $friendInfo = Master::getFriendInfo($k);
		            $friendInfo["btime"] = $v;
					$outof[] = $friendInfo;
				}

				//保存
		        if($flag == 1){
		            $this->info['list'] = $list;
		            $this->save();
		        }
			}
			$cache->set($key,$outof);
		}
		//默认输出直接等于内部存储数据
		$this->outf = $outof;
	}
}
