<?php
require_once "RedisBaseModel.php";
/*
 * 活动关卡冲榜
 */
class Redis102Model extends RedisBaseModel
{
	public $comment = "关卡冲榜排行";
	public $act = 'huodong_251';//活动标签
	
	public $b_mol = "cbhuodong";//返回信息 所在模块
	public $b_ctrl = "guanqialist";//返回信息 所在控制器
	protected $_with_decimal_sort = true;//加小数排序
	
    public function __construct($key = '')
    {
        parent::__construct($key);
        $this->_with_decimal_denominator = time() - 1505232000;
    }
	/*
	 * 初始化结构体
	 */
	public $_init = array(
	/*
		uid => 关卡数
	*/
	);
	
	
	/**
	 * 获取单个关卡的信息
	 * @param $member  关卡id
	 * @param $rid   排名id
	 */
	public function getMember($member,$rid){
		
		$UserModel = Master::getUser($member);
		//获取头像
		$Act6141Model = Master::getAct6141($member);
		//获取公共基础信息
		$cinfo = array(
			'uid' => $member,
			'name' => $UserModel->info['name'],
			'rid' => $rid,
			'score' => intval(parent::zScore($member)),
			'level' => $UserModel->info['level'],
			'vip' => $UserModel->info['vip'],
			'job' => $UserModel->info['job'],
			'headavatar' => $Act6141Model->info,
		);
		return $cinfo;
	}
	
	/*
	 * 返回我的排行信息
	 */
	public function back_data_my($member){
		$name = '无';
		$rid = 100001;
		$score = 0;
		$Act6141Model = Master::getAct6141($member);
		if(!empty($member)){
			$rid = parent::get_rank_id($member);
			$score = intval(parent::zScore($member));
			$UserModel = Master::getUser($member);
			$name = $UserModel->info['name'];
			$level = $UserModel->info['level'];
			$vip = $UserModel->info['vip'];
			$job = $UserModel->info['job'];
		}
		
		Master::back_data(0,$this->b_mol,"myguanqiaRid",array(
			'uid' => $member,
			"rid"=>$rid,
			'score' => $score,
			'name' => $name,
			'level' => $level,
			'vip' => $vip,
			'job' => $job,
			'headavatar' => $Act6141Model->info,
		));
	}
	
}

