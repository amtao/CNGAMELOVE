<?php
require_once "ActBaseModel.php";
/*
 *  聊天 - 禁言内容
 */
class Act98Model extends ActBaseModel
{
	public $atype = 98;//活动编号

	public $comment = "聊天-禁言内容";

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		//'content' => array();
		//'kua_content' => array();
	);
	
	/*
	 * 加入内容
     */
	public function add($data){
	    if(empty($data)){
			Master::error(ACT_98_JINYAN);
		}
		$this->info['content'] = $data;
	    $this->save();
	}
	/*
	 * 加入跨服被禁言内容
	 * */
	public function add_kf_msg($data){
	    if(empty($data)){
	        Master::error(ACT_98_JINYAN);
	    }
	    $this->info['kua_content'] = $data;
	    $this->save();
	}
	
	
	/*
	 * 返回活动信息
	 */
	public function back_data(){
	
	
	}
}
