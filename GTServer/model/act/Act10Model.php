<?php
//
require_once "ActFlBaseModel.php";
/*
 * 别人对我的提亲列表
 * 子嗣提亲列表
 */
class Act10Model extends ActBaseModel
{
	public $atype = 10;//活动编号
	
	public $comment = "子嗣提亲列表";
	public $b_mol = "son";//返回信息 所在模块
	public $b_ctrl = "qList";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(//提亲列表
		/*
		 * uid => array(
		 * 	子嗣ID1 => 1,
		 *  子嗣ID2 => 1)
		 */
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$outf = array();
		foreach ($this->info as $fuid => $fsons){
			$fSonModel = Master::getSon($fuid);
			$fuserInfo = Master::fuidInfo($fuid);//玩家信息
			foreach ($fsons as $fsonid => $ftime){
				//超时处理?
				if (!Game::is_over($ftime)){
					//获取子嗣名字 / 属性
					$outf[] = Master::getMarryDate($fuid,$fsonid);
				}
			}
		}
		$this->outf = $outf;
	}
	
	//添加一条提亲信息
	public function add($fuid,$sonid){
		$this->info[$fuid][$sonid] = $_SERVER['REQUEST_TIME']+259200;
		$this->save();
	}
	
	//撤销一条提亲信息
	public function remove($fuid,$sonid){
		unset($this->info[$fuid][$sonid]);
		$this->save();
	}
	//撤销所有提亲信息
	public function remove_all(){
		$this->info = $this->_init;
		$this->save();
	}
		
}
