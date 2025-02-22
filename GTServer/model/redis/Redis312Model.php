<?php
require_once "RedisBaseModel.php";
/*
 * 帮会亲密涨幅冲榜排行
 */
class Redis312Model extends RedisBaseModel
{
	public $comment = "帮会亲密涨幅冲榜排行";
	public $act = 'huodong_312';//活动标签
	
	public $b_mol = "cbhuodong";//返回信息 所在模块
	public $b_ctrl = "clublovelist";//返回信息 所在控制器
	protected $_with_decimal_sort = true;//加小数排序
	protected $hd_id = 'huodong_312';//活动配置文件关键字
	
    public function __construct($key = '')
    {
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
	/*
		联盟id => 联盟总经验
	*/
	);
	
	
	/**
	 * 获取单个联盟的信息
	 * @param $member  联盟id
	 * @param $rid   排名id
	 */
	public function getMember($member,$rid){
		
		$ClubModel = Master::getClub($member);
		//获取公共基础信息
		$cinfo = array(
			'name' => $ClubModel->info['name'],
			'rid' => $rid,
			'score' => intval(parent::zScore($member)),
		);
		return $cinfo;
	}
	
	/*
	 * 返回我的排行信息
	 */
	public function back_data_my($member){
		$name = RANK_NO_NAME;
		$rid = 100001;
		$score = 0;
		if(!empty($member)){
			$rid = parent::get_rank_id($member);
			$score = intval(parent::zScore($member));
			$ClubModel = Master::getClub($member);
			$name = $ClubModel->info['name'];
		}
		
		
		Master::back_data(0,$this->b_mol,"myclubLoveRid",array(
			"rid"=>$rid,
			'score' => $score,
			'name' => $name,
		));
	}
	
}

