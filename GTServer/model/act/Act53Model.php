<?php
require_once "ActBaseModel.php";
/*
 * 酒楼-消息-仇人信息
 */
class Act53Model extends ActBaseModel
{
	public $atype = 53;//活动编号
	
	public $comment = "酒楼-消息-仇人信息";
	public $b_mol = "boite";//返回信息 所在模块
	public $b_ctrl = "yhbad";//返回信息 所在控制器
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 * uid => 宴会时间
		 */
	);
	
	/**
	 * 添加仇人信息
	 * @param unknown_type $uid 仇人uid
	 */
	public function add_bad($uid){
		$this->info[] = array(
			'uid' => $uid,
			'btime' => $_SERVER['REQUEST_TIME'],
		);
		$this->save();
	}
	
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//输出
		$this->outf = array();
		//输出列表
		foreach($this->info as $k => $v){
			$fuidData = Master::fuidData($v['uid']);
			$this->outf[] = array(
				'id' => $v['uid'],
				'name' => $fuidData['name'],
				'level' => $fuidData['level'],
				'shili' => $fuidData['shili'],
				'ctime' => $v['btime'],
			);
		}
		return $this->outf;
	}
	
	
}
















