<?php
/*
 * 衙门战击败20榜
 */
require_once "SevListBaseModel.php";
class Sev6Model extends SevListBaseModel
{
	public $comment = "衙门战击败20榜";
	public $act = 6;//活动标签
	
	public $b_mol = "yamen";//返回信息 所在模块
    public $b_ctrl = "kill20log";//返回信息 所在控制器
    protected $_use_lock = false;//是否加锁
	
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
		));
		time 时间
		 */
	);
	
    /*
     * 列表构造输出
     */
    public function list_mk_outf($v_info){
    	$userInfo = Master::fuidInfo($v_info['uid']);
		$fuserInfo = Master::fuidInfo($v_info['fuid']);
		
        return array(
            'user' => $userInfo,
			'fuser' => $fuserInfo,
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
		$data['time'] = Game::get_now();
		parent::list_push($data);
	}
}
