<?php
/*
 * 狩猎
 */
require_once "SevBaseModel.php";
class Sev40Model extends SevBaseModel
{
	public $comment = "狩猎";
	public $act = 40;//活动标签
	public $hd_id = 'hunt';
	public $hd_cfg;
	public $_init = array(//初始化数据
	   'score' => 0,
	);
	
	/*
	 * 添加积分
	 */
	public function add($score){
	    $this->info['score'] += $score;
	    $this->save();
	}
	
	public function back_data(){
	    $score = $this->info['score'];
	    Master::back_data(0,"hunt","allScore",array('score'=>$score));
	}
	
}
