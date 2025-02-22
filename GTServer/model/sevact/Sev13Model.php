<?php
/*
 * 战报信息(boss未被击杀) 
 */
require_once "SevBaseModel.php";
class Sev13Model extends SevBaseModel
{
	public $comment = "战报信息(boss未被击杀)";
	public $act = 13;//活动标签
	
	public $_init = array(//初始化数据
		/*
		    $log[bossid][] = array(
		        id  =>   玩家id
		        hid  =>   门客id
		        hit  =>   伤害
		        gx   =>    每次攻击获得的贡献
		        time =>   打boss时间
		    );
		*/
	);
	
	public function __construct($hid,$cid){
		parent::__construct($hid,$cid);
	}
	
	
	/**
	 * 战报信息(boss未被击杀)
	 * @param unknown_type $uid  玩家id
	 * @param unknown_type $cbid  bossid
	 * @param unknown_type $hid  门客id
	 * @param unknown_type $hit  照成的伤害
	 * @param unknown_type $gx  获得的贡献
	 */
	/*public function add_hero_log($uid,$cbid,$hid,$hit,$gx){
        $UserModel = Master::getUser($uid);
		$data  = array(
			'id' => $uid,
            'uName' => Game::filter_char($UserModel->info['name']),
			'hid' => $hid,
			'hit' => $hit,
			'gx'  => $gx,
			'time' => $_SERVER['REQUEST_TIME'],
		);
		if(empty($this->info[$cbid])){
			$this->info[$cbid] = array();
		}
		array_push($this->info[$cbid],$data);
		$this->save();
	}*/

	public function add_hero_log($uid,$hit,$post){
		$UserInfo = Master::getUserInfo($uid);
		if(empty($this->info[$uid])){
			$this->info[$uid] = array(
				'name' => Game::filter_char($UserInfo['name']),
				'hit' => $hit,
				'post' => $post,
				'headavatar' => $UserInfo['headavatar'],
				'clothe' => $UserInfo['clothe'],
				'job' => $UserInfo['job'],
				'level' => $UserInfo['level'],
				'time' => $_SERVER['REQUEST_TIME'],
			);
		}else{
			$this->info[$uid]['hit'] += $hit;
		}
		$this->save();
	}
	
	//删除boss战的打击记录
	public function delLogs(){
		$this->info = $this->_init;
		$this->save();
	}

	public function getSortLog(){
		$logArr = array();
		if(!empty($this->info)){
			foreach($this->info as $uid => $info){
				$UserInfo = Master::getUserInfo($uid);
				$logArr[$info['hit'].$info['time']] = array(
					'uid' => $uid,
					'name' => $UserInfo['name'],
					'hit' => $info['hit'],
					'post' => $info['post'],
					'headavatar' => $UserInfo['headavatar'],
					'clothe' => $UserInfo['clothe'],
					'job' => $UserInfo['job'],
					'level' => $UserInfo['level'],
					'time' => $info['time'],
				);
			}
			krsort($logArr);
		}
		return $logArr;
	}
	
	/**
	 * $cbid bossid
	 */
	public function boss_log_outf(){
		$this->outof = array();
		if(!empty($this->info)){
			foreach($this->info as $uid => $info){
				$UserInfo = Master::getUserInfo($uid);
				$this->outof[] = array(
					'uid' => $uid,
					'name' => $UserInfo['name'],
					'hit' => $info['hit'],
					'post' => $info['post'],
					'headavatar' => $UserInfo['headavatar'],
					'clothe' => $UserInfo['clothe'],
					'job' => $UserInfo['job'],
					'level' => $UserInfo['level'],
					'time' => $info['time'],
				);
			}
		}
		Master::back_data(0,'club','heroLog',$this->outof);
	}
}





