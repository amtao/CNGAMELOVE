<?php
require_once "ActHDBaseModel.php";

/*
 * 活动315 宫殿宫斗冲榜
 */

class Act315Model extends ActHDBaseModel
{
	public $atype = 315;//活动编号
	public $comment = "宫殿宫斗冲榜";
	public $b_mol = "cbhuodong";//返回信息 所在模块
	public $b_ctrl = "clubyamen";//子类配置
	public $hd_cfg;//活动配置
	public $hd_id = 'huodong_315';//活动配置文件关键字
	protected $cid;

	/**
	 * Act315Model constructor.
	 * @param $uid  用户id
	 */
	public function __construct($uid)
	{
		$this->uid = intval($uid);
		$Act40Model = Master::getAct40($uid);
		$this->cid = $Act40Model->info['cid'];
		parent::__construct($uid);//执行基类的构造函数
	}

	/*
	 * 初始化结构体
	 */
	public $_init = array(
		'score' => 0, //宫斗冲榜积分涨幅
	);

	/**
	 * 获取是否有红点  (可领取)
	 * $news 0:不可以领取   1:可以领取
	 */
	public function get_news()
	{
		$news = 0; //不可领取
		return $news;
	}

	/**
	 * 宫斗战斗数据
	 * @param $num  通过门客数
	 */
	public function do_save($num)
	{
		if (parent::get_state() != 1) {//不在活动中
			return;
		}
		if (empty($this->cid)) {//没有联盟 输赢都不做积分变更
			return;
		}
		//宫殿冲榜数据
		$this->club_score_up($this->cid, $num);
		//个人冲榜数据
		$this->info['score'] += $num;
		$this->save();

	}

	//加入宫殿
	public function join_club($cid)
	{
		if (parent::get_state() != 1) {//不在活动中
			return;
		}
		$score = $this->info['score'];
		if ($score > 0) {//进帮不带来

		} else if ($score < 0) { //负分不带来
			//不改变联盟积分 成员积分置零(预防性处理)
			$this->info['score'] = 0;
			$this->save();
		}
	}

	//退出宫殿
	public function out_club($cid)
	{
		if (parent::get_state() != 1) {//不在活动中
			return;
		}
		$score = $this->info['score'];
		if ($score > 0) {//正分带走
			//不改变成员积分 扣除联盟积分
			$this->club_score_up($cid, 0 - $score);
		}
		//退帮进行清零操作
		$this->info['score'] = 0;
		$this->save();
	}

	//解散宫殿
	public function del_club($cid)
	{
		if (parent::get_state() != 1) {//不在活动中
			return;
		}
		//活动展示或活动中 都删除排行
		$Redis315Model = Master::getRedis315($this->hd_cfg['info']['id']);
		$Redis315Model->del_member($cid);
	}

	//宫殿宫斗冲榜积分 变更
	public function club_score_up($cid, $num)
	{
		if (parent::get_state() != 1) {//不在活动中
			return;
		}
		$Redis315Model = Master::getRedis315($this->hd_cfg['info']['id']);
		$Redis315Model->zIncrBy($cid, $num);
		Game::cmd_other_flow($cid, 'club', 'hd_315_' . $this->hd_cfg['info']['id'], array($this->uid), 69, 1, $num, $Redis315Model->zScore($cid));
	}

	/**
	 * 返回排行列表
	 */
	public function get_rank($params)
	{
		$Redis315Model = Master::getRedis315($this->hd_cfg['info']['id']);
		$Redis315Model->back_data();
		$Redis315Model->back_data_my($this->cid);
	}

	/*
	 * 返回活动信息
	 */
	public function get_info($params)
	{
		$Redis315Model = Master::getRedis315($this->hd_cfg['info']['id']);
        $Redis315Model->back_data();
		$Redis315Model->back_data_my($this->cid);
		parent::back_data_hd();
	}

	/**
	 * 检查能否一键
	 */
	public function check_onkey()
	{
		if (parent::get_state() == 1 && $this->hd_cfg['info']['eTime'] - Game::get_now() < 7200) {
			Master::error(ONE_YAMEN_002);
		}
		return;
	}

	public function back_rank(){
		if( self::get_state() == 0 ){
			return array();
		}
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		$Redis315Model = Master::getRedis315($this->hd_cfg['info']['id']);
		$list['list'] = $Redis315Model->out_redis();
		$rid = $Redis315Model->get_rank_id($cid);
		$score = intval($Redis315Model->zScore($cid));
		if(!empty($cid)) {
			$ClubModel = Master::getClub($cid);
			$name = $ClubModel->info['name'];
		}
		$list['my'] = array(
			"rid"=> empty($rid) ? 100001 : $rid,
			'score' => $score,
			'name' => empty($name) ? RANK_NO_NAME : $name,
		);
		return $list;
	}
}
