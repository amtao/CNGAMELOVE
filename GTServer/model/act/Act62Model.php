<?php
require_once "ActBaseModel.php";
/*
 * 衙门-防守信息
 */
class Act62Model extends ActBaseModel
{
	public $atype = 62;//活动编号
	
	public $comment = "衙门-防守信息";
	public $b_mol = "yamen";//返回信息 所在模块
	public $b_ctrl = "deflog";//返回信息 所在控制器
	
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
		$Redis6Model = Master::getRedis6();
		foreach ($this->info as $k => $v){
			$fUser = Master::fuidInfo($v['uid']);
			$fUser['id'] = $k;//序列号
			$fUser['fscore'] = $Redis6Model->zScore($v['uid']);//对方衙门分数
			$fUser['kill'] = $v['kill'];//击败我方几个门客
			$fUser['win'] = $v['win'];//是否全歼
			$fUser['hid'] = $v['hid'];//对方使用的门客
			$fUser['mscore'] = $v['mscore'];//我的衙门分数增减
			$fUser['dtime'] = $v['dtime'];//时间
			$out[] = $fUser;
		}
		$this->outf = $out;
	}

	public function list_init(){
		//遍历所有的信息去掉过期的 保留一个月
		$mem_db = Common::getMyMem();
		$up = false;
		if(!empty($this->info)){
			$res = $mem_db->get($this->init_key());
			if($res === false || $res['day'] != Game::get_today_long_id()){//今天还没删过
				$limit_time = strtotime(date('Ymd',strtotime('-1 month')));
				foreach ($this->info as $key =>  $value){
					if($value['dtime'] < $limit_time){
						unset($this->info[$key]);
						$up = true;
					}
				}
				$this->info = array_values($this->info);
			}
		}
		$res= array(
			'day' => Game::get_today_long_id(),
		);
		$mem_db->set($this->init_key(),$res,2*24*60*60);
		if($up){
			$this->save();
		}else{
			$this->back_data();
		}
	}


	protected function init_key(){
		return $this->uid.'_yamen_init';
	}



}


