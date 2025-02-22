<?php
require_once "ActBaseModel.php";
/*
 * 道具合成类
 */
class Act14Model extends ActBaseModel
{
	public $atype = 14;//活动编号
	
	public $comment = "道具合成";
	public $b_mol = "item";//返回信息 所在模块
	public $b_ctrl = "hecheng";//返回信息 所在控制器
	public $hd_id = 'hecheng';//活动配置文件关键字
	/*
	 * 初始化结构体
	 * 每日重置 合成次数
	 * 可以合成道具 读取活动配置
	 * 保存今天已经合成的道具次数
	 */
	public $_init =  array(
		//道具ID => 次数
	);
	
	public function __construct($uid){
		$this->uid = intval($uid);
		$cfg = Game::get_peizhi($this->hd_id);
		$this->hd_cfg = self::get_cfg($cfg);
		if(!empty($this->hd_cfg['id'])){
			parent::__construct($uid,$this->hd_cfg['id']);
		}
	}
	
	public function get_cfg($cfg){
		$out_cfg = array();
		if(!empty($cfg['items'])){
			$out_cfg['id'] = $cfg['id'];
			foreach($cfg['items'] as $k => $v){
				$v['outtime'] = intval(strtotime($v['outtime']));
				if(Game::is_over($v['outtime'])){
					continue;
				}
				$out_cfg['items'][$k] = $v;
			}
		}
		return $out_cfg;
	}
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		
		//遍历已经合成的次数 //构造输出
		$outf = array();
		foreach ($this->hd_cfg['items'] as $itemid => $v){
			if(empty($this->info[$itemid])){
				$this->info[$itemid] = 0;
			}
			$v['itemid'] = $itemid;
			$v['totonum'] = $v['times'];
			$v['times'] -= $this->info[$itemid];
			$outf[] = $v;
		}
		
		$this->outf = $outf;
	}
	
	/*
	 * 合成道具
	 */
	public function hecheng($itemid, $count){
		$count = empty($count)?1:$count;
		
		//特殊道具合成
		//获取配置
		if (!empty($this->hd_cfg['items'][$itemid])){
			//判断是否超出上限
			if(empty($this->info[$itemid])){
				$this->info[$itemid] = 0;
			}
			if($this->info[$itemid] + $count > $this->hd_cfg['items'][$itemid]['times']){
				Master::error(CLUB_EXCHANGE_GOODS_MAX);
			}
			$need = $this->hd_cfg['items'][$itemid]['need'];
		}else{
			//是否普通道具
			$item_hc_cfg = Game::getcfg("item_hc");
			if (isset($item_hc_cfg[$itemid])){
				//普通道具合成
				$need = $item_hc_cfg[$itemid]['need'];
			}
		}
		if(empty($need)){
			Master::error(ACT_14_CONFIGWRONG.$itemid);
		}
		//循环扣除道具
		foreach ($need as $v){
			Master::sub_item($this->uid,KIND_ITEM,$v['id'],$v['count'] * $count);
		}
		
		//加上目标道具
		Master::add_item($this->uid,KIND_ITEM,$itemid,$count);
		
		if(!empty($this->hd_cfg['items'][$itemid])){
			if(empty($this->info[$itemid])){
				$this->info[$itemid] = 0;
			}
			$this->info[$itemid] += $count;
			$this->save();
		}
	}
	
	
	
	
}
