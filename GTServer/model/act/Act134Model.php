<?php
require_once "ActBaseModel.php";
/*
 * 亲家好感度
 */
class Act134Model extends ActBaseModel
{
	public $atype = 134;//活动编号
	
	public $comment = "亲家好感度";
	public $b_mol = "friends";//返回信息 所在模块
	public $b_ctrl = "qjlove";//返回信息 所在控制器
	
	public $open_send = true;//是否下发,默认下发
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'my' => array(),  //存放  我方 -> 对方  的好感度
		'f'  => array(),  //存放  对方 -> 我方  的好感度
	);
	

	/*
	 * 存放  我方 -> 对方  的好感度
	 * $uid
	 */
	public function myadd($fuid,$falg = true){
		if(empty($this->info['my'][$fuid])){
			$this->info['my'][$fuid] = array(
				'num' => 0,
				'time' => 0,
			);
		}
		//是否已拜访
		if(Game::is_today($this->info['my'][$fuid]['time'])){
			if($falg){
				Master::error(FRIEND_QJ_BAIFANG);
			}else{
				return 0;
			}
			
		}
		$this->info['my'][$fuid]['num'] += 1;
		$this->info['my'][$fuid]['time'] = $_SERVER['REQUEST_TIME']; //拜访时间  $_SERVER['REQUEST_TIME']
		$this->save();

		
        //主线任务 ---  拜访亲友	拜访次数X次
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(40, 1);


		return 1;
		
	}
	
	/*
	 * 存放  对方 -> 我方  的好感度
	 * $uid
	 */
	public function fadd($fuid){
		
		$this->open_send = false;
		
		if(empty($this->info['f'][$fuid])){
			$this->info['f'][$fuid] = array(
				'num' => 0,
				'time' => 0,
			);
		}
		$this->info['f'][$fuid]['num'] += 1;
		$this->info['f'][$fuid]['time'] = $_SERVER['REQUEST_TIME']; //拜访时间
		$this->save();
	}
	
	
	/*
	 * 获取好感度
	 * $fuid
	 * $flag 0:我方  1:对方
	 */
	public function get_love($fuid,$flag){
		$all = $this->info['my'];
		if($flag){
			$all = $this->info['f'];
		}
		
		if(empty($all[$fuid]['num'])){
			return 0;
		}
		return $all[$fuid]['num'];
	}
	
	
	/*
	 * 返回活动信息
	 */
	public function back_data(){
		
		
	}
	
}






