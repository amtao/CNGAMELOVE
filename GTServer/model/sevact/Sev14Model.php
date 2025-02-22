<?php
/*
 * 战报列表(boss被击杀)  =>公会boss伤害排行
 */
require_once "SevBaseModel.php";
class Sev14Model extends SevBaseModel
{
	public $comment = "战报列表(boss被击杀)公会boss伤害排行";
	public $act = 14;//活动标签
	
	public $_init = array(//初始化数据
	/*
			'bossid' => uid
	*/
	);
	
	public function __construct($hid,$cid){
		parent::__construct($hid,$cid);
	}
	
	/**
	 * 保存击杀玩家
	 * @param unknown_type $uid
	 * @param unknown_type $cbid  bossid
	 */
	public function kill_log($cbid,$uid){
		$this->info[$cbid] = $uid;
		$this->save();
	}

	/**
	 * $cid   :  公会id
	 * $cbid  :  bossid
	 */
	public function log_outf($cid,$cbid){
		
		$this->outof = array();
		$fUserModel = Master::getUser($this->info[$cbid]);
		$this->outof['kill'] = $fUserModel->info['name'];
		$this->outof['list'] = array();
		$hlog = array();
		$Sev13Model = Master::getSev13($cid);
		if(!empty($Sev13Model->info[$cbid])){
			foreach($Sev13Model->info[$cbid] as $info){
				if(empty($hlog[$info['id']])){
					$hlog[$info['id']] = array(
						'hit' => 0,  //伤害
						'gx' => 0,   //贡献
					);
				}
				$hlog[$info['id']]['hit'] += $info['hit'];
				$hlog[$info['id']]['gx'] += $info['gx'];
			}
			foreach($hlog as $k => $v){
			    if (isset($v['uName'])) {
			        $name = $v['uName'];
	            } else {
	                $fUserModel = Master::getUser($k);
	                $name = $fUserModel->info['name'];
	            }
				$key = intval($v['hit'].$k);  //用于排序
				$this->outof['list'][$key] = array(
					'name' => $name,
					'hit' => $v['hit'],
					'gx' => $v['gx'],
					'uid' => $k,
				);
			}
			krsort($this->outof['list']);
			$this->outof['list'] = array_values($this->outof['list']);
		}

		Master::back_data(0,'club','uidLog',$this->outof);
		
	}

	/**
	 * 删除上一轮击杀记录
	 * @param $cbid
	 */
	public function delKillRecode($cbid){
		if(isset($this->info[$cbid])){
			unset($this->info[$cbid]);
			$this->save();
		}
	}
}





