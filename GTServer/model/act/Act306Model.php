<?php
require_once "ActBaseModel.php";
/*
 * 跨服宫斗预选赛门票
 */
class Act306Model extends ActBaseModel
{
	public $atype = 306;//活动编号
	public $b_mol = "kuayamen";//返回信息 所在模块
	public $b_ctrl = "yuxuan";//返回信息 所在控制器
	public $comment = "跨服宫斗-预选赛门票";
	public $hd_id = "huodong_300";
	public $hd_cfg;
	
	public function __construct($uid){
	    $this->uid = $uid;
	    Common::loadModel('HoutaiModel');
	    $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
	    if(!empty($this->hd_cfg['info']['id'])){
	        parent::__construct($uid,$this->hd_cfg['info']['id']);
	    }
	}
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'state' => 0
	);
	//构造输出函数
	public function make_out(){
//		$this->info['state'] = 1;
	    $this->outf = $this->info;
	}
	/*
	 * 添加门票
	 * */
	public function add(){
	    if(empty($this->info['state'])){
	        $this->info['state'] = 1;
	        $this->save();
	    }
	}
}
