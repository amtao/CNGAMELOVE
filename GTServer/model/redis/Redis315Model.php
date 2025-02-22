<?php
require_once "RedisBaseModel.php";
/*
 * 帮会宫斗冲榜
 */
class Redis315Model extends RedisBaseModel
{
	public $comment = "帮会宫斗冲榜排行";
	public $act = 'huodong_315';//活动标签
	public $b_mol = "cbhuodong";//返回信息 所在模块
	public $b_ctrl = "clubyamenlist";//返回信息 所在控制器
	protected $_with_decimal_sort = true;//加小数排序
	protected $hd_id = 'huodong_315';//活动配置文件关键字

	public function __construct($key){
		parent::__construct($key);
		//获取活动配置
		Common::loadModel('HoutaiModel');
		$hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
		$this->_with_decimal_denominator = Game::get_now() - $hd_cfg['info']['sTime'];
	}

	/*
	 * 初始化结构体
	 */
	public $_init = array(

	);

	/**
	 * 获取单个帮会的信息
	 * @param array $member  用户id
	 * @return array
	 */
	public function getMember($member){
		$ClubModel = Master::getClub($member);
		//获取公共基础信息
		$cinfo = array(
			'cid' => $member,
			'score' => intval(parent::zScore($member)),
			'name' => $ClubModel->info['name'],
			'rid' => intval(parent::get_rank_id($member)),
		);
		return $cinfo;
	}

	/*
	 * 返回我的帮会排行信息
	 */
	public function back_data_my($club_id){
		$name = RANK_NO_NAME;
		$rid = 100001;
		$score = 0;
		if($club_id){
			$score = intval(parent::zScore($club_id));
			$rid = intval(parent::get_rank_id($club_id));
			$clubModel = Master::getClub($club_id);
			$name = $clubModel->info['name'];
		}
		Master::back_data(0,$this->b_mol,'myclubyamen',array(
			'cid' => $club_id,
			'score' => $score,
			'name' => $name,
			'rid' => $rid,
		));
	}

}

