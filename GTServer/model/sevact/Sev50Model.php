<?php
/**
 * 帮会战-匹配列表
 */
require_once "SevKuaCfgBaseModel.php";
class Sev50Model extends SevKuaCfgBaseModel
{
    public $comment = "帮会战-匹配列表";
    public $act = 50;//活动标签
    public $_server_kua_key = 'clubpk';//指定跨服配置对应的key
    
    /*
	 * 初始化结构体
	 */
	public $_init = array(
		//  我方cid => 敌方公会信息
	);
    
	
	
	/**
	 * 添加一条信息
	 * @param $cid  我方公会id
	 * @param $fcid 敌方公会id
	 */
    public function add($cid,$fcid){
    	
    	$fcinfo = array();
    	if(!empty($fcid)){
    		$ClubModel = Master::getClub($fcid);
	    	$fname = empty($ClubModel->info['name'])?'':$ClubModel->info['name'];
	    	//敌方公会信息
	    	$fcinfo = array(
	    		'fcid' => $fcid,
	    		'fname' => $fname,
	    		'msevid' => Game::get_sevid_club($fcid),
	    	);
    	}
    	
    	$this->info[$cid] = $fcinfo;
    	$this->save();
    	return $fcinfo;
    }
    
	/**
	 * 检测是否已匹配
	 */
    public function check_match($cid){
    	
    	if( !empty($this->info[$cid]) ){
    		return true;
    	}
    	return false;
    }
    
}



