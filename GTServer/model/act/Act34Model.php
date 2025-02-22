<?php
require_once "ActBaseModel.php";
/*
 * 皇宫-个人宣言
 */
class Act34Model extends ActBaseModel
{
	public $atype = 34;//活动编号
	public $comment = "皇宫-个人宣言";
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
	    //id => msg   王爷id  =>  宣言
	);
	/*
	 * 添加宣言
	 * */
	public function set($cid,$msg){
	    //敏感字符判定
	    $msg = Game::str_feifa($msg);
	    $msg = Game::str_mingan($msg);
	    //敏感词汇
	    $Sev28Model = Master::getSev28();
	    if($Sev28Model->isSensitify($msg)){
	        Master::error(ACT_34_MINGAN);
	    }
	    $Act25Model = Master::getAct25($this->uid);
	    $Act25Model->is_effect($cid);
	    $this->info[$cid] = $msg;
	    $this->save();
	}
	
	/*
	 * 不返回数据
	 * */
	public function back_data(){
	    
	}
}
















