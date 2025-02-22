<?php
require_once "ActBaseModel.php";
/*
 *  兑换码
 */
class Act400Model extends ActBaseModel
{
	public $atype = 400;//活动编号

	public $comment = "兑换码";
	public $b_mol = "";//返回信息 所在模块
	public $b_ctrl = "";//返回信息 所在控制器
	protected $pre_key = 'code';

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		//'key' => 0,  //  act_key => key
	);
	
	public function save_acode($type,$act,$key){
	    $key = $this->pre_key.type;
	    switch ($type){
	        case 5://周礼包
	            if(!empty($this->info[$key][$act])){
	                $pre_year = date('Y',$this->info[$key][$act]);
	                $year = date('Y',Game::get_now());
	                $pre_week = date('W',$this->info[$key][$act]);
	                $week = date('W',Game::get_now());
	                if($pre_year == $year && $pre_week == $week){
	                    Master::error(WEEK_REWARD_GETED); 
	                }
	            }
	            $this->info[$key][$act] = Game::get_now();
	            $this->save();
	            break;
	    }
	}
	
	public function back_data() {
	    
	}
}
