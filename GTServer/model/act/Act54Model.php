<?php
require_once "ActBaseModel.php";
/*
 * 酒楼-兑换商店黄金刷新
 */
class Act54Model extends ActBaseModel
{
	public $atype = 54;//活动编号
	
	public $comment = "酒楼-兑换商店黄金刷新";
	public $b_mol = "boite";//返回信息 所在模块
	public $b_ctrl = "jlShopfresh";//返回信息 所在控制器
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'fnum'  => 10, //刷新商品剩余次数
		'fmax'  => 10, //刷新商品最大次数
		'fcost' => 10, //刷新商品花费黄金
	
	);
	
	/**
	 * 扣除刷新次数
	 */
	public function sub_fnum(){
		$this->info['fnum'] --;
		if($this->info['fnum'] < 0){
			Master::error(BOITE_GOODS_REFRESH_NUM_SHORT);
		}
		$this->save();
	}
	
	
}
















