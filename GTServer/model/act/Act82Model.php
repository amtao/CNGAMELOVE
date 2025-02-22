<?php
require_once "ActHDBaseModel.php";
/*
 * 特惠礼包
 */
class Act82Model extends ActHDBaseModel
{
	public $atype = 82;//活动编号

	public $comment = "特惠礼包";
	public $b_mol = "shop";//返回信息 所在模块
	public $b_ctrl = "giftlist";//返回信息 所在控制器
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_82';//活动配置文件关键字

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 * id => num
		 * */

	);


	/*
	 * 特惠礼包
	 * */
	public function shopGift($id){
		$state = $this->getState();
		if(empty($state)){
			Master::error(SHOP_ACTIVITY_UNOPEN);
		}
		foreach ($this->outf['list'] as $v) {
			if($id == $v['id']){
				$arrShop = $v;
			}
		}

		//判断是否可以购买
		$UserModel = Master::getUser($this->uid);

		$vip = $UserModel->info['vip'];
		if ($arrShop['vip'] > $vip) {
			Master::error(SHOP_VIP_LEVEL_SHORT);
		}
		if (!empty($arrShop['islimit']) && ($arrShop['limit'] < 1)) {
			Master::error(SHOP_BUY_NUM_GT_MAX);
		}
		if(empty($this->info[$id])){
			$this->info[$id] = 0;
		}
		$this->info[$id] += 1;

		//扣除砖石
		Master::sub_item($this->uid,KIND_ITEM,1,$arrShop['need']);

		$this->save();

		//添加items
		foreach ($arrShop['items'] as $val){
			Master::add_item($this->uid,$val['kind'],$val['id'],$val['count']);
		}

		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(32, 1);

	}
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$outof = array();
		$outof['cft']['id'] = $this->hd_cfg['info']['id'];
		$outof['cft']['title'] = $this->hd_cfg['info']['title'];
		$outof['cft']['sTime'] = $this->hd_cfg['info']['sTime'];
		$outof['cft']['eTime'] = $this->hd_cfg['info']['eTime'];
		$outof['cft']['cd']['next'] = $this->hd_cfg['info']['eTime'];
		$outof['cft']['cd']['label'] = 'giftpackscd';
		foreach ($this->hd_cfg['rwd'] as $id => $v){
			$val['id'] = $id;
			$val['name'] = $v['name'];
			$val['need'] = $v['need'];
			$val['items'] = $v['items'];
			foreach ($val['items'] as &$val1){
				if(empty($val1['kind'])){
					$val1['kind'] = 1;
				}
			}
			$val['vip'] = $v['vip'];
			$val['islimit'] = $v['islimit'];
			if(!empty($v['islimit'])){//有限购
				if(empty($this->info[$id])){
					$val['limit'] = $v['limit'];
				}else{
					$val['limit'] = $v['limit'] - $this->info[$id];
				}
			}
			$val['price'] = $v['price'];
			$outof['list'][] = $val;
		}
		//默认输出直接等于内部存储数据
		$this->outf = $outof;
	}
	
    public function back_data(){
		Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
	}

	/**
	 * 活动活动状态
	 * 返回:
	 * 0: 活动未开启
	 * 1: 活动中
	 * 2: 活动结束,展示中
	 */
	
	public function getState(){
		$state = 0;  //活动未进行
		if(!empty($this->hd_cfg)){
            $state = 1;
		}
		return $state;
	}
	
	public function get_news(){
	    return 0;
	}
}
