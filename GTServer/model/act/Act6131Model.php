<?php
require_once "ActBaseModel.php";
/*
 * 知己活力
 */
class Act6131Model extends ActBaseModel
{
	public $atype = 6131;//活动编号
	public $label = "jiaQi";//倒计时标记
	
	public $comment = "出游假期";
	public $b_mol = "hero";//返回信息 所在模块
	public $b_ctrl = "jiaQi";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(//政务处理
		'num' => 0,//	假日累计数量
		'time' => 0,//	假日上次恢复时间
		'stories' => array(),
	);
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out()
	{
		$UserModel = Master::getUser($this->uid);
		//获得VIP配置
		$vip_cfg_info = Game::getcfg_info('vip',$UserModel->info['vip']);
		
		//计算恢复数据
		$hf_num = Game::hf_num(
				$this->info['time'],//上次恢复时间
				1800,//CD
				$this->info['num'],//上次次数
				$vip_cfg_info['jiaqi']//次数上限
			);
		
		//保存数据
		$this->info['time'] = $hf_num['stime'];
		$this->info['num'] = $hf_num['num'];
		$this->info['stories'] = isset($this->info['stories']) ? $this->info['stories'] : array();
		
		//输出数据
		$this->outf = array(
			'next' => $hf_num['next'],//下次绝对时间
			'num' => $hf_num['num'],//剩余次数
			'label' => $this->label,
			'stories' => $this->info['stories'],
		);
	}
	
	/*
	 * XXOO 减去一次数量
	 */
	public function apao(){
		if ($this->outf['num'] <= 0){
			Master::error(WIFE_VACATION_EMPTY);
		}
		//减去次数
		$this->info['num'] -= 1;
		
		//保存
		$this->save();
	}
	
	/*
	 * 一键XO 减去全部数量
	 * 返回次数
	 */
	public function qunp(){
		if ($this->outf['num'] <= 0){
			Master::error(WIFE_VACATION_EMPTY);
		}
		//减去次数
		$num = $this->info['num'];
		$this->info['num'] = 0;
		
		//保存
		$this->save();
		return $num;
	}
	
	/*
	 * 恢复假日
	 */
	public function huifu($num){
		if ($this->outf['num'] > 0){
			Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
			return 0;
		}

		//扣除伟哥
		Master::sub_item($this->uid,KIND_ITEM,150,$num);

		//保存数据
        $this->info['num'] += $num;//恢复满假日
		$this->save();
	}

	//获得hero随机对应hero剧情
	public function randStoryId($heroId){
		$cyeventCfg = Game::getcfg_info('chuyou_event',$heroId);
		//
		//获取heroJb
		$act6001Model = Master::getAct6001($this->uid);
		$JbCount = $act6001Model->getHeroJB($heroId);
		//满足条件的
		//1.羁绊值筛选
		//2.概率
		$eventList = array();
		foreach($cyeventCfg as $v){
			if($JbCount >= $v['jiban']){
				$eventList[$v['id']] = $v;
			}
		}
		$storyId = Game::get_rand_key1($eventList,'event_pro');
		$isRand = 0;
		//之前有随到的storyid
		if(!in_array($storyId, $this->info['stories'])){
			array_push($this->info['stories'],$storyId);
			$this->save();
		}else{
			$isRand = 1;
		}
		return array("storyId" => $storyId,"isRand" => $isRand);
	}
}
