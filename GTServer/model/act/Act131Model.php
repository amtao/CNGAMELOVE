<?php
require_once "ActBaseModel.php";
/*
 * 申请好友列表
 */
class Act131Model extends ActBaseModel
{
	public $atype = 131;//活动编号
	
	public $comment = "申请好友列表";
	public $b_mol = "friends";//返回信息 所在模块
	public $b_ctrl = "fapplylist";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'list' => array(),  //申请列表
		'uplimit' => 30,   //被申请人数上限20人,超过人数不能申请
	);
	


	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$outof = array();
		if(!empty($this->info['list'])){

			$list = array();
			$flag = 0;  //是否进行保存操作   1:保存   0:不保存
			if( !empty($this->info['list']) ){
				foreach($this->info['list'] as $k => $v){

					if($_SERVER['REQUEST_TIME'] - intval($v["applyTime"]) > 86400 * 7 ){
		                $flag = 1;
		                continue;
		            }

		            $list[$k] = $v;
					$outof[] = $v;
				}

				//保存
		        if($flag == 1){
		            $this->info['list'] = $list;
		            $this->save();
		        }
			}

			$outof = array_values($this->info['list']);
		}
		
		//默认输出直接等于内部存储数据
		$this->outf = $outof;
	}
	
	/*
	 * 申请
	 * $uid
	 */
	public function apply($uid){
		if( count($this->info['list']) >= $this->info['uplimit'] ){
			Master::error(FRIEND_APPLY_MAX);
		}

		$userInfo = Master::getFriendInfo($uid);
		$userInfo["applyTime"] = $_SERVER['REQUEST_TIME'];
		$this->info['list'][$uid] = $userInfo;
		$this->save();
	}
	
	/*
	 * 删除申请
	 * $uid 0 :全部拒绝  >0  拒绝该uid
	 */
	public function sub($uid){
		
		if(empty($uid)){
			$this->info['list'] = array();
		}else{
			unset($this->info['list'][$uid]);
		}
		$this->save();
		
	}

	public function redPoint() {
		if ($this->info['list']) {
			Master::back_data($this->uid, $this->b_mol, 'redPoint', array('status'=>1));
		} else {
			Master::back_data($this->uid, $this->b_mol, 'redPoint', array('status'=>0));
		}
	}
}





