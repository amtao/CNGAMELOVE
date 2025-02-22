<?php 
require_once "ActBaseModel.php";
/*
 * 公会-每日任务
 */

class Act761Model extends ActBaseModel{
    
    public $atype = 761;//活动编号

	public $comment = "公会每日任务";
	public $b_mol = "club";//返回信息 所在模块
    public $b_ctrl = "clubTask";//返回信息 所在控制器
    
    public $_init = array(
        'list' => array(),//今日公会完成次数
        'get' => array(),//领取状态
    );

    public function completeTask($taskType,$num = 1)
	{
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			return false;
		}

		$clubTaskCfg = Game::getcfg('club_task');
		foreach($clubTaskCfg as $k => $taskInfo){
			if($taskInfo['type'] == $taskType){
				$this->add($taskInfo['id'], $num);
			}
		}
    }
    
    
	/**
	 * 完成任务
	 * @param $uid
	 * @param $id
	 */
	public function add($id,$num = 1){

		$this->info["list"][$id] += $num;
		$this->save();
	}


	/**
	 * 领取任务奖励
	 * @param $uid
	 * @param $fuid
	 * @param $id
	 * @return array
	 */
	public function get($id){

		$this->info["get"][$id] += 1;
		$this->save();
	}


    public function make_out(){
        $this->outf = $this->info;
    }

}
