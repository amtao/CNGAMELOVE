<?php
require_once "ActBaseModel.php";
/*
 * 国子监-位子数量
 */
class Act75Model extends ActBaseModel
{
	public $atype = 75;//活动编号
	
	public $comment = "国子监-位子";
	public $b_mol = "gzj";//返回信息 所在模块
	public $b_ctrl = "base";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(//书桌数量
		'desk' => 1, //当前数量
	);
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	//略 因为输出信息 跟 保存信息一致 
	
	/*
	 * 检查书桌ID 范围合法
	 */
	public function click_id($id = 1){
		if ($id <= 0 || $id > $this->info['desk']){
			Master::error("DESK_ID_ERR_".$id);
		}
	}
	
	/*
	 * 加上书桌数量
	 * 只能一个个加
	 */
	public function add_desk(){
		//所需元宝
		$this->info['desk'] += 1;
		$work_cfg_info = Game::getcfg_info('gzj_seat',$this->info['desk'],GZJ_MAX_SEAT);
		//直接在这里扣钱
		Master::sub_item($this->uid,KIND_ITEM,1,$work_cfg_info['cash']);
		$this->save();
	}
}
