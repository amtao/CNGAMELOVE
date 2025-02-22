<?php
require_once "ActBaseModel.php";
/*
 * 跨服大理寺-防守信息
 */
class Act304Model extends ActBaseModel
{
	public $atype = 304;//活动编号
	
	public $comment = "跨服大理寺-防守信息";
	public $b_mol = "kuayamen";//返回信息 所在模块
	public $b_ctrl = "deflog";//返回信息 所在控制器
	public $hd_id = "huodong_300";
	public $hd_cfg;
	public function __construct($uid){
	    Common::loadModel('HoutaiModel');
	    $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
	    if(!empty($this->hd_cfg)){
	        parent::__construct($uid,$this->hd_cfg['info']['id']);
	    }
	}
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		add(array(
			'uid' => $this->uid,	//谁来打的我
			'hid' => $this->info['hid'],	//对方用什么门客打我
			'kill' => $kill_num,	//杀了我几个人
			'win' => $is_win,	//是不是全歼了
			'mscore' => $mscore,	//我的衙门分数变化情况
		));
     	 *"dtime":[0,"攻打时间"],
		 * 
		 */
	);
	
	/*
	 * 添加一条信息
	 */
	public function add($data){
		$data['dtime'] = Game::get_now();
		$this->info[] = $data;
		$this->save();
	}
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out(){
		$out = array();
		//衙门积分排行
		$Redis306Model = Master::getRedis306($this->hd_cfg['info']['id']);
		foreach ($this->info as $k => $v){
			$fUser = Master::fuidInfo($v['uid']);
			$fUser['id'] = $k;//序列号
			$fUser['fscore'] = intval($Redis306Model->zScore($v['uid']));//对方衙门分数
			$fUser['kill'] = $v['kill'];//击败我方几个门客
			$fUser['win'] = $v['win'];//是否全歼
			$fUser['hid'] = $v['hid'];//对方使用的门客
			$fUser['mscore'] = $v['mscore'];//我的衙门分数增减
			$fUser['dtime'] = $v['dtime'];//时间
			$out[] = $fUser;
		}
		$this->outf = $out;
	}
}


