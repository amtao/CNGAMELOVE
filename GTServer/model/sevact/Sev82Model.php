<?php
/*
 * 双旦-走马灯
 */
require_once "SevListBaseModel.php";
class Sev82Model extends SevListBaseModel
{
	public $comment = "双旦-走马灯";
	
    public $b_mol = "sdhuodong";//返回信息 所在模块
    public $b_ctrl = "sdzmd";//返回信息 所在控制器
	public $act = 82;//活动标签
	protected $_use_lock = false;//是否加锁
	
	public $chat_info_num = 30;//初始/自动 发送条数
    public $chat_history_num = 10;//每次历史滚动条数
    
	public $_init = array(//初始化数据

	);
	
	/*
     * 添加一条信息
     */
    public function add_msg($data){

		parent::list_push($data);
    }


}





