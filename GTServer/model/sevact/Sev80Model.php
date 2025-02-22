<?php
/*
 * 转盘双十二活动-获奖情况
 */
require_once "SevListBaseModel.php";
class Sev80Model extends SevListBaseModel
{
	public $comment = "转盘双十二活动-获奖情况";
	
    public $b_mol = "zphuodong";//返回信息 所在模块
    public $b_ctrl = "zplog";//返回信息 所在控制器
	public $act = 80;//活动标签
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





