<?php
require_once "ActBaseModel.php";
/*
 * 申请好友列表限制
 */
class Act132Model extends ActBaseModel
{
	public $atype = 132;//活动编号
	
	public $comment = "申请好友列表";
	public $b_mol = "friends";//返回信息 所在模块
	public $b_ctrl = "applyList";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(

	);

	public function add($fuid) {
		$this->info[$fuid] = time();
		$now = time();
		foreach($this->info as $uid=>$value) {
			if (($value + 86400) < $now) {
				unset($this->info[$uid]);
			}
		}
		$this->save();
	}

	public function sub($fuid) {
		$now = time();
		if ($this->info) {
			foreach($this->info as $uid=>$value) {
				if (($value + 86400) < $now) {
					unset($this->info[$uid]);
				}
			}
			unset($this->info[$fuid]);
			$this->save();
		}
	}

	public function make_out(){
		$outof = array();
		if ($this->info) {
			foreach($this->info as $uid=>$value) {
				$outof[] = $uid;
			}
		}
		$this->outf = $outof;
	}
}





