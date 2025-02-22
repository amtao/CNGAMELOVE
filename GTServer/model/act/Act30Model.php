<?php
require_once "ActBaseModel.php";
/*
 * 称号
 */
class Act30Model extends ActBaseModel
{
	public $atype = 30;//活动编号
	
	public $comment = "王爷领取200黄金";
	public $b_mol = "chenghao";//返回信息 所在模块
	public $b_ctrl = "wyrwd";//返回信息 所在控制器
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		'get' => 0,  //是否领取奖励   0:不能领取1:可领取2:已领取
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//过滤过期
		$outf = array();
		
		if($this->info['get'] < 2){
			//称号
			$Act25Model = Master::getAct25($this->uid);
			$is_true = $Act25Model->has_wangye();
			if($is_true){
				$this->info['get'] = 1;
			}
		}
		$outf['get'] = $this->info['get'];
		//构造输出
		$this->outf = $outf;

	}
	
	/*
	 * 领取王爷奖励
	 */
	public function get_rwd(){
		
		if($this->info['get'] == 0){
			Master::error(ACT_30_BUKELING);
		}
		if($this->info['get'] == 2){
			Master::error(ACT_30_YILING);
		}
		
		//称号
		$this->info['get'] = 2;
		$this->save();
		//加 200黄金
		Master::add_item($this->uid,KIND_ITEM,1,200);
	}
	
}




