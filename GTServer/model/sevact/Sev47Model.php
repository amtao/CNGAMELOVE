<?php
/*
 * 聊天-跑马灯
 */
require_once "SevListBaseModel.php";
class Sev47Model extends SevListBaseModel
{
	public $comment = "聊天-跑马灯";
	public $act = 47;//活动标签
	public $b_mol = "chat";//返回信息 所在模块
    public $b_ctrl = "pao";//返回信息 所在控制器

    protected $_delete_cache_when_save = false;
    protected $_use_lock = false;//是否加锁
    protected $_max_chat_num = 10;///最大内部保存数量
    public $chat_info_num = 10;//初始/自动 发送条数
    public $chat_history_num = 10;//每次历史滚动条数
	public $_init = array(//初始化数据
		/*
		array(
			array(
				'msg' => '', //跑马灯消息
				'outtime' => '',//过期时间
			),
		),
		 */
	);
	
	/**
	 * 添加消息
	 * @param string $msg 跑马灯信息
	 * @param unknown_type $outtime 过期时间
	 */
	public function add_msg($msg,$outtime = 3600){
		$data = array(
			'msg' => $msg,
			'outtime' => Game::get_now()+$outtime,
		);
		//
		$this->list_push($data);
	}
}
