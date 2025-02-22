<?php
/*
 * 聊天-公共频道
 */
require_once "SevListBaseModel.php";
class Sev22Model extends SevListBaseModel
{
	public $comment = "聊天-公共频道";
	
    public $b_mol = "chat";//返回信息 所在模块
    public $b_ctrl = "sev";//返回信息 所在控制器
	public $act = 22;//活动标签
	protected $_use_lock = false;//是否加锁
	
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
	
	/*
     * 添加一条信息
     */
    public function add_msg($uid,$msg, $type=1){
    	$isGM = 0;  //默认不是
    	//判断是不是官方
        $sev35Model = Master::getSev35();
        if (!empty($sev35Model->info) && in_array($uid, $sev35Model->info)){
            $isGM = 1;
        }

    	$data = array(
            'user' => Master::fuidInfo($uid),
			'uid' => $uid,
			'type' => $type,
			'msg' => $msg,
			'time' => Game::get_now(),
            'isGM' => $isGM,
		);
		parent::list_push($data);
        //聊天流水
        Game::cmd_chat_flow(1, $uid, $data['user']['name'], $data['user']['vip'], $data['user']['level'], $msg, $data['time']);
    }

    /*
     * 列表构造输出
     */
    public function list_mk_outf($v_info){
    	$data = array(
            'type' => $v_info['type'],
            'msg' => $v_info['msg'],
            'time' => $v_info['time'],
            'isGM' => $v_info['isGM']?$v_info['isGM']:0,
        );
    	if (isset($v_info['user'])){
    		$data['user'] = $v_info['user'];
    	} else {
    		$data['user'] = Master::fuidInfo($v_info['uid']);
    	}
        return $data;
    }

}





