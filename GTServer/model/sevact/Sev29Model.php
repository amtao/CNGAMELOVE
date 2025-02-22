<?php
/*
 * 酒楼-官宴-全服可见
 */
require_once "SevBaseModel.php";
class Sev29Model extends SevBaseModel
{
	public $comment = "酒楼-家宴-全服可见";
	public $act = 29;//活动标签
	
	public $_init = array(//初始化数据
	
	);
    protected $_save_msg = false;//保存结果集，因为这个功能调用广泛，易保存结果集
	
	public function __construct($hid){
		parent::__construct($hid);
	}
	
	/**
	 * 添加宴会  -- 酒楼-官宴-全服可见
	 * @param unknown_type $uid  玩家uid
	 */
	public function add_yh($uid){
		$this->info[$uid] = $uid;
		$this->save();
	}
	
	/**
	 * 去除宴会  -- 酒楼-家宴-全盟可见
	 * @param unknown_type $uid  玩家uid
	 */
	public function sub_yh($uid){
		if(!empty($this->info[$uid])){
			unset($this->info[$uid]);
			$this->save();
		}
	}
	
	/*
	 * 获取输出数据
	 */
	public function get_outf(){
	    return $this->mk_outf();
	}
	
	/**
	 * 构造输出
	 */
	public function mk_outf(){
		$this->outof = array();
		$full_num = 0;
		foreach($this->info as $k => $v){
			$Act170Model = Master::getAct170($k);
			if ($Act170Model->is_over()){
				unset($this->info[$k]);
				$full_num ++;
				continue;
			}
			$fUserModel = Master::getUser($k);
			$this->outof[] = array(
				'uid' => $v,
				'name' => $fUserModel->info['name'],
				'type' => 1,
			);
		}
		if ($full_num > 0){
			$this->save();
		}
		return $this->outof;
	}
}





