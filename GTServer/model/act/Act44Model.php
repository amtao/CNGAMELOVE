<?php
require_once "ActBaseModel.php";
/*
 * 我的红包
 * 根据活动id和联盟重置
 */
class Act44Model extends ActBaseModel
{
	public $atype = 44;//活动编号
	
	public $comment = "我的红包";
	public $b_mol = "hbhuodong";//返回信息 所在模块
	public $b_ctrl = "myRedList";//返回信息 所在控制器
	public $hd_id = "huodong_295";
	public $hd_cfg;
	public $cid;

	//活动id和联盟id 重置
	//字段  money  stime etime
	public function __construct($uid)
	{
		$this->uid = intval($uid);
		Common::loadModel('HoutaiModel');
		$this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
		if(!empty($this->hd_cfg)){
			$Act40Model = Master::getAct40($this->uid);
			$this->cid = $Act40Model->info['cid'];
			parent::__construct($uid, $this->hd_cfg['info']['id'].$this->cid);
		}
	}

	/**
	 * 构造输出函数
	 */
	public function make_out()
	{
		$outf = array();
		if(!empty($this->info)){
			//state 我的红包  0生效中  1已领完了  2 过期了
			foreach ($this->info as $key => $val){
				if(Game::is_over($val['etime'])){
					$state = 2;
					$num = 0;
				}else{
					$count = empty($val['list']) ? 0 : count($val['list']);
					if($val['num'] > $count){//还有剩余
						$state = 0;
						$num = $val['num'] - $count;
					}else{
						$state = 1;
						$num = 0;
					}
				}
				$outf[] = array(
					'uid' => $this->uid,
					'id' => $key,
					'num' => $num,
					'msg' => $val['msg'],
					'stime' => $val['stime'],
					'state' => $state,
				);
			}
		}
		$this->outf = $outf;
	}

	/**
	 * 添加红包
	 * @param $id
	 * @param $num
	 */
	public function add($id,$num,$money){
		$state = self::get_state();
		if($state == 0){
			Master::error(ACTHD_OVERDUE);
		}

		$hblist = $this->splitRedTicket($num,$money);
		$msg = $this->getMsg();
		$this->info[$id] = array(
			'num' => $num,
			'stime' => Game::get_now(),
			'etime' => strtotime(date('Y-m-d 23:59:59',Game::get_now())),
			'msg' => $msg,
			'hb' => $hblist
		);
		$this->save();
		$Sev16Model = Master::getSev16($this->cid);
		$Sev16Model->add($this->uid,$id);
		$Sev16Model->bake_data();

		$Redis31Model = Master::getRedis31($this->hd_cfg['info']['id']);
		$Redis31Model->zIncrBy($this->uid,$money);
		$Redis31Model->back_data();
		$Redis31Model->back_data_my($this->uid);

		Game::cmd_flow(48,"{$this->uid}_{$id}({$money})",1,1);
	}

	/**
	 * 活动活动状态
	 * 返回:
	 * 0: 活动未开启
	 * 1: 活动中
	 * 2: 活动结束,展示中
	 */
	public function get_state(){
		$state = 0;  //活动未开启
		if(!empty($this->hd_cfg) ){
			if(Game::dis_over($this->hd_cfg['info']['showTime'])){
				$state = 2;  //活动结束,展示中
			}
			if(Game::dis_over($this->hd_cfg['info']['eTime'])){
				$state = 1;  //活动中
			}
		}
		return $state;
	}

	/**
	 * 获取红包列表
	 * @param $splitNum
	 * @param $money
	 * @return array
	 */
	public function splitRedTicket($splitNum,$money)
	{
		$leftGold = $money;

		$res = array();
		for ($leftTimes = $splitNum; $leftTimes > 0; $leftTimes--) {
			$max = $leftGold - $leftTimes + 1;
			if ($leftTimes > 1) {
				$gold = mt_rand(1, ceil($max / 2.2));//2.2为期望因子
			} else {
				$gold = $leftGold;
			}
			$leftGold -= $gold;
			$res[] = $gold;
		}
		shuffle($res);

		return $res;
	}

	public function getMsg(){
		$msg = HBHD_RED_PACK_BLESS;
		return $msg;
	}

	/**
	 * 获取金额
	 */
	public function getMoney($fuid,$id){
		if(empty($this->info[$id])){
			Master::error(HBHD_RED_PACK_FAILED);
		}
		if(empty($this->info[$id]['hb'])){
			Master::error(HBHD_RED_PACK_PICKED_UP);
		}
		$key = array_rand($this->info[$id]['hb'],1);
		$money = $this->info[$id]['hb'][$key];
		unset($this->info[$id]['hb'][$key]);
		$this->info[$id]['list'][$fuid] = $money;
		$this->save();
		return $money;
	}

	/**
	 * 获取红包详情
	 */
	public function getHbInfo($id){
		if(empty($this->info[$id])){
			Master::error(HBHD_RED_PACK_NO_FIND);
		}
		$outf = array();
		if(!empty($this->info[$id])){
			$lq_list = array();
			if(!empty($this->info[$id]['list'])){
				$max_uid = array_search(max($this->info[$id]['list']),$this->info[$id]['list']);
				foreach ($this->info[$id]['list'] as $e_uid => $money){
					$UserModel = Master::getUser($e_uid);
					if(empty($UserModel->info['name'])){
						$name = '未知';
					}else{
						$name = $UserModel->info['name'];
					}
					$lucky = 0;
					$exite = empty($this->info[$id]['list']) ? 0 : count($this->info[$id]['list']);
					if($this->info[$id]['num'] <= $exite && $max_uid == $e_uid){
						$lucky = 1;
					}
					$lq_list[] = array(
						'uid' => $e_uid,
						'name' => $name,
						'money' => $money,
						'lucky' => $lucky
					);
				}
			}

			$fuserInfo = Master::fuidInfo($this->uid);
			$outf = array(
				'uid' => $this->uid,
				'id' => $id,
				'name' => $fuserInfo['name'],
				'sex' => $fuserInfo['sex'],
				'job' => $fuserInfo['job'],
				'total' =>  $this->info[$id]['num'],
				'exite' =>  empty($this->info[$id]['list']) ? 0 : count($this->info[$id]['list']),
				'msg' => $this->info[$id]['msg'],
				'stime' => $this->info[$id]['stime'],
				'lq_list' => $lq_list
			);
		}
		Master::back_data(0,$this->b_mol,'hbInfo',$outf);
	}

}














