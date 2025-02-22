<?php
/*
 * 招财进宝-获奖情况
 */
require_once "SevListBaseModel.php";
class Sev83Model extends SevListBaseModel
{
	public $comment = "招财进宝-获奖情况";
	
    public $b_mol = "zchuodong";//返回信息 所在模块
    public $b_ctrl = "zclog";//返回信息 所在控制器
	public $act = 83;//活动标签
	protected $_use_lock = false;//是否加锁
	
	public $chat_info_num = 5;//初始/自动 发送条数
    public $chat_history_num = 5;//每次历史滚动条数
	
	public $_init = array(//初始化数据
	
	
	);
	
	/*
     * 添加一条信息
     */
    public function add_msg($data){
		parent::list_push($data);
    }

}





