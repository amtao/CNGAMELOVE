<?php
require_once "ActBaseModel.php";

/*
 * 单品限购(基础配置)
 */
class Act80Model extends ActBaseModel
{
	public $atype = 80;//活动编号
	
	public $comment = "单品限购-基础配置";
	public $b_mol = "shop";//返回信息 所在模块
	public $b_ctrl = "list";//返回信息 所在控制器
	public $hd_cfg ;//活动配置
	public $hd_id = 'shop_limit';//活动配置文件关键字
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 * 'id' => 10001,
		 * 	'1' => num,
		*/
	);

	public function __construct($uid)
	{
		//获取配置
		$this->uid = $uid;
		$UserModel = Master::getUser($uid);
		$shop_cfg = Game::get_peizhi($this->hd_id);
		$id = $shop_cfg['info'][$UserModel->info['level']];
		if(!empty($id)){
			$this->hd_cfg = $shop_cfg['rwd'][$id];
			if(!empty($this->hd_cfg)){
				parent::__construct($uid, $id.'_'.Game::get_today_id());
			}
		}
	}


	/*
	 * 单品购买
	 * */
	public function shopLimit($id, $count)
	{
		if(empty($this->hd_cfg) || empty($this->hd_cfg[$id])){
			Master::error(CAN_NOT_FIND_PRODUCT);
		}

		$arrShop = $this->hd_cfg[$id];
		$UserModel = Master::getUser($this->uid);

		//判断是否可以购买
		$vip = $UserModel->info['vip'];
		if ($arrShop['vip'] > $vip) {
			Master::error(SHOP_VIP_LEVEL_SHORT);
		}

		if(empty($this->info[$id])){
			$this->info[$id] = 0;
		}

		if (!empty($arrShop['islimit']) && ($arrShop['limit'] - $this->info[$id] < $count)) {
			Master::error(SHOP_BUY_NUM_GT_MAX);
		}

		//扣除砖石
		Master::sub_item($this->uid,KIND_ITEM,1,$arrShop['need'] * $count);

		$this->info[$id] += $count;
		$this->save();
		//添加items
		Master::add_item($this->uid,$arrShop['item']['kind'],$arrShop['item']['id'],$arrShop['item']['count'] * $count);
        $item = Game::getcfg_info('item',$arrShop['item']['id']);
        //咸鱼日志
        Common::loadModel('XianYuLogModel');
        xianYuLogModel::shop($UserModel->info['platform'], $this->uid, $UserModel->info['level'], $UserModel->info['vip'], $item['name'], $count, $arrShop['need'] * $count);

        Game::flow_php_record($this->uid, 5, $arrShop['item']['id'], $count, $item['name'], $arrShop['need']);
	}

	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$outof = array();

		if(!empty($this->hd_cfg)){
			foreach ($this->hd_cfg as $id => $v){
				$value['id'] = $id;
				$value['need'] = $v['need'];
				$value['vip'] = $v['vip'];
				$value['item'] = array(
					'kind' => $v['item']['kind'] ? $v['item']['kind'] : 1,
					'id' => $v['item']['id'],
					'count' => $v['item']['count']
				);
				$value['islimit'] = $v['islimit'];
				//是否限购
				if ($v['islimit'] == 1) {
					if (empty($this->info[$id])) {
						$value['limit'] = $v['limit'];
					} else {
						$value['limit'] = $v['limit'] - $this->info[$id];
					}
				} else {
					$value['limit'] = 0;
				}
				$value['price'] = $v['price'];
				$outof[] = $value;
			}
		}
		//默认输出直接等于内部存储数据
		$this->outf = $outof;
	}

	public function back_data(){

	}
}
