<?php
require_once "ActBaseModel.php";
/*
 *   势力达到某一值 新增红颜
 */
class Act91Model extends ActBaseModel
{
	public $atype = 91;//活动编号

	public $comment = "势力达到某一值 新增红颜";
	public $b_mol = "";//返回信息 所在模块
	public $b_ctrl = "";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
// 		'id' => 0,  //  1:势力达到某一值 新增红颜
	);
	
	public function addwife($shili){
	    //获取红颜配置
	    $wife_cfg = Game::getcfg('wife');
	    if (empty($wife_cfg)){
	        return false;
	    }
	    $i = 0;
	    foreach ($wife_cfg as $k => $val){
	        if($val['from'] == 'shili'){
	            if(!empty($this->info[$k]) || $shili < $val['condition'])continue;
	            Master::add_item($this->uid,KIND_WIFE,$k);
                $this->info[$k] = 1;
                $i++;
	        }
	    }
	    if($i > 0){
	        $this->save();
	    }
	}

	/*
	 * 返回活动信息
	 */
	public function back_data(){
		
		
	}
	
}
