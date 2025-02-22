<?php
require_once "ActBaseModel.php";
/*
 * 充值-充值档次
 */
class Act70Model extends ActBaseModel
{
	public $atype = 70;//活动编号
	
	public $comment = "充值-充值档次";
	public $b_mol = "order";//返回信息 所在模块
	public $b_ctrl = "rshop";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		

	);
	
	

	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out(){
		
		$UserModel = Master::getUser($this->uid);
		$channel = $UserModel->info['channel_id'];
		$platform = $UserModel->info['platform'];
		Common::loadModel('OrderModel');
		$list = OrderModel::recharge_list($platform,$channel);
		
		$outf = array();
		$Act72Model = Master::getAct72($this->uid);
		$cz_out = $Act72Model->do_out();
		foreach($list as $k => $v){
			$beishu = 1;
			if($cz_out != -1 && $cz_out[$v['ormb']] != 1 && $v['type'] == 1){
				$beishu = $Act72Model->cfg['rwd']['beishu'];
			}
			$outf[] = array(
				'dc' => $v['dc'],
				'rmb' => $v['rmb'],
				'ormb' => $v['ormb'],
				'diamond' => $v['diamond'],
				'type' => $v['type'],
				'beishu' => $beishu,
				'dollar'=> $v['dollar'],
				'krw'=> $v['krw'],
				'cpId'=> $v['cpId']
			);
		}
		
		$this->outf = $outf;
	}
	
	
	
	
	
}