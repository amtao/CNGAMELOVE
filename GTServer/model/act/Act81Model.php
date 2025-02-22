<?php
require_once "ActHDBaseModel.php";

/*
 * 单品限购
 */
class Act81Model extends ActHDBaseModel
{
	public $atype = 81;//活动编号
	
	public $comment = "单品限购";
	public $b_mol = "shop";//返回信息 所在模块
	public $b_ctrl = "list";//返回信息 所在控制器
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_81';//活动配置文件关键字
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 * 'id' => 10001,
		 * 	'1' => num,
		*/
	);

	/*
	 * 单品购买
	 * */
	public function shopLimit($id, $count)
	{

	    if ($count <= 0)return;
		if($id >=10000){
			$Act80Model = Master::getAct80($this->uid);
			$Act80Model->shopLimit($id, $count);
			$this->back_data();
		}else{
            $UserModel = Master::getUser($this->uid);
			$cfg = $this->hd_cfg['rwd'];
			if(empty($cfg) || empty($cfg[$id])){
				Master::error(CAN_NOT_FIND_PRODUCT);
			}
			$arrShop = $cfg[$id];

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

		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(32, $count);

        //御花园
        // $Act6190Model = Master::getAct6190($this->uid);
        // $Act6190Model->addType(5, $count);
	}

	/*
	 * 构造输出结构体
	 */
	public function get_outf(){
		$outof = array();
		if(!empty($this->hd_cfg['rwd'])) {
			foreach ($this->hd_cfg['rwd'] as $id => $v) {
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
				$value['type'] = $v['type'];
				$outof[] = $value;
			}
		}

		$Act80Model = Master::getAct80($this->uid);
		if(!empty($Act80Model->outf)){
			$outof = array_merge($outof,$Act80Model->outf);
		}

		//默认输出直接等于内部存储数据
		return $outof;
	}

	public function back_data(){
		$outf = $this->get_outf();
		Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$outf);
	}

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
