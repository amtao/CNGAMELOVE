<?php
/*
 * 衙门聊天-跨服频道
 */
require_once "SevListBaseModel.php";
class Sev313Model extends SevListBaseModel
{
	public $comment = "跨服势力聊天";
	
    public $b_mol = "kuacbhuodong";//返回信息 所在模块
    public $b_ctrl = "chat";//返回信息 所在控制器
	public $act = 313;//活动标签


    public function __construct($hid, $cid, $serverID)
    {
        parent::__construct($hid, $cid, $serverID);
    }


	public $_init = array(//初始化数据
        /*
         * array(
         *  'uid' => 10086,
         *  'type' => 1,//类型1 普通 2 红字 3 系统通告
         *  'msg' => ''//内容
         *  'time ' => now
         * ),
         */
	);
    /**
     * 添加聊天信息
     * @param $uid
     * @param $msg
     */
    public function add_msg($uid, $msg){
    	$data = array(
            'user' => Master::fuidInfo($uid),
			'uid' => $uid,
			'type' => 1,
			'msg' => $msg,
			'time' => Game::get_now()
		);
		parent::list_push($data);
        //跨服势力聊天流水
        Game::cmd_chat_flow(6, $uid, $data['user']['name'], $data['user']['vip'], $data['user']['level'], $msg, $data['time'], $this->cid);
    }

    /*
     * 列表构造输出
     */
    public function list_mk_outf($v_info){
    	$data = array(
            'user' => $v_info['user'],
            'type' => $v_info['type'],
            'msg' => $v_info['msg'],
            'time' => $v_info['time'],
        );
        return $data;
    }

}





