<?php
/*
 * 跨服大理寺战击败20榜
 */
require_once "SevListBaseModel.php";
class Sev60Model extends SevListBaseModel
{
	public $comment = "跨服大理寺战击败20榜";
	public $act = 60;//活动标签
	
	public $b_mol = "kuayamen";//返回信息 所在模块
    public $b_ctrl = "kill20log";//返回信息 所在控制器
    protected $_use_lock = false;//是否加锁
    protected $_server_type = 4;
    public $hd_id = "huodong_300";
	
	public $_init = array(//初始化数据
		/*
		array(
			'uid' => $this->uid,	//进攻方
			'fuid' => $this->uid,	//防守方
			'hid' => $hid,	//使用门客打我
			'kill' => $kill_num,	//杀了我几个人
			'lkill' => $Act60Model->info['lkill'],	//连杀次数
			'win' => $is_win,	//是不是全歼了
			'ftype' => $this->info['ftype'],	//战斗类型 / (是否追杀)
			'is_play' => 1, //是否可以打 本服是不可以打的 
		));
		time 时间
		 */
	);
	public function __construct($hid){
		$Sev61Model = Master::getSev61();
		if(!empty($Sev61Model->info['list'])){
			foreach ($Sev61Model->info['list'] as $sid_arr){
				$this->_server_kua_cfg[] = array(0,0,$sid_arr);
			}
			parent::__construct($hid);
		}
	}
	
    /*
     * 列表构造输出
     */
    public function list_mk_outf($v_info){
        return array(
            'user' => isset($v_info['uInfo']) ? $v_info['uInfo'] : Master::fuidInfo($v_info['uid']),
            'fuser' => isset($v_info['fUInfo']) ? $v_info['fUInfo'] : Master::fuidInfo($v_info['fuid']),
			'hid' => $v_info['hid'],
			'kill' => $v_info['kill'],
			'win' => $v_info['win'],
			'lkill' => empty($v_info['lkill'])?3:$v_info['lkill'],
			'ftype' => $v_info['ftype'] == 5? 1 : 0,
			'ktime' => $v_info['time'],
        );
    }
    
	/*
	 * 添加一条击杀记录
	 */
	public function add_msg($data){
        $data['uInfo'] = Master::fuidInfo($data['uid']);
        $data['fUInfo'] = Master::fuidInfo($data['fuid']);
		$data['time'] = Game::get_now();
		parent::list_push($data);
	}
}
