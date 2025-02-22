<?php
/*
 * 狩猎 奖励日志
 */
require_once "SevListBaseModel.php";
class Sev42Model extends SevListBaseModel
{
	protected $_use_lock = false;//是否加锁
	public $comment = "狩猎-排行奖励发放";
	public $act = 42;//活动标签
	public $_init = array(//初始化数据
		/*
		 * 'id' => 1
		 */
	);

	
	/*
	 * 添加一条奖励信息
	 */
	public function add(){
		if(!empty($this->info['id'])){
		    return 0;
		}
		$this->info['id'] = 1;
		$this->save();
		return 1;
	}
	
	/*
	 * 返回协议信息
	 */
	public function bake_data(){
		
	}
}
