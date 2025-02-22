<?php
require_once "ActBaseModel.php";
/*
 * 私聊列表
 */
class Act135Model extends ActBaseModel
{
	public $atype = 135;//活动编号
	
	public $comment = "私聊列表";
	public $b_mol = "friends";//返回信息 所在模块
	public $b_ctrl = "fllist";//返回信息 所在控制器
	
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		
	
	);
	
	/*
	 * 添加
	 * */
	public function add($fuid)
	{
	    $this->info[$fuid] = 1;
	    $this->save();
	}
	
	/*
	 * 删除
	 * */
	public function sub($fuid)
	{
	    unset($this->info[$fuid]);
	    $this->save();
	}

	/*
	 * 构造输出
	 * */
	public function make_out() {
		
        $this->outf = $this->info;
        
	}
	
	/*
	 * 返回活动信息
	 */
	public function back_data(){
		
		
	}
	
}






