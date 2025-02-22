<?php
/*
 * 衙门聊天-跨服频道
 */
require_once "SevListBaseModel.php";
class Sev62Model extends SevListBaseModel
{
	public $comment = "衙门聊天-跨服频道";
	
    public $b_mol = "kuayamen";//返回信息 所在模块
    public $b_ctrl = "chat";//返回信息 所在控制器
	public $act = 62;//活动标签
	protected $_server_type = 4;//1：合服，2：跨服，3：全服 4:跨服
	protected $_use_lock = false;//是否加锁
	
	public function __construct(){
	    $Sev61Model = Master::getSev61();
		if(!empty($Sev61Model->info['list'])){
			$SevCfg = Common::getSevidCfg();
			foreach ($Sev61Model->info['list'] as $sid_arr){
				$this->_server_kua_cfg[] = array(0,0,$sid_arr);
				if(in_array($SevCfg['he'],$sid_arr)) $state = 1;
			}
			if(!empty($state)){
				parent::__construct($Sev61Model->info['id']);
			}
		}
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
	
	/*
     * 添加一条信息
     */
    public function add_msg($uid,$msg){
    	$isGM = 0;  //默认不是
    	//判断是不是官方
        $sev35Model = Master::getSev35();
        if (!empty($sev35Model->info) && in_array($uid, $sev35Model->info)){
            $isGM = 1;
        }
		
    	$data = array(
            'user' => Master::fuidInfo($uid),
			'uid' => $uid,
			'type' => 1,
			'msg' => $msg,
			'time' => Game::get_now(),
            'isGM' => $isGM,
		);
		parent::list_push($data);
        //跨服大理寺聊天流水
        Game::cmd_chat_flow(4, $uid, $data['user']['name'], $data['user']['vip'], $data['user']['level'], $msg, $data['time']);
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





