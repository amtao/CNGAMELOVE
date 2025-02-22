<?php
require_once "ActBaseModel.php";
/*
 * 发配-桌子个数
 */
class Act128Model extends ActBaseModel
{
	public $atype = 128;//活动编号
	
	public $comment = "发配-桌子个数";
	public $b_mol = "banish";//返回信息 所在模块
	public $b_ctrl = "base";//返回信息 所在控制器
	public $hd_id = 'banish';//活动配置文件关键字
	public $hd_cfg;


	/*
	 * 初始化结构体
	 */
	public $_init =  array(//书桌数量
		'open' => 0,
		'desk' => 1, //当前数量
	);

	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	//略 因为输出信息 跟 保存信息一致

	/*
	 * 检查书桌ID 范围合法
	 */
	public function click_id($id = 1){
		if ($id <= 0 || $id > $this->info['desk']){
			Master::error("DESK_ID_ERR_".$id);
		}
	}

	/*
	 * 加上书桌数量
	 * 只能一个个加
	 */
	public function add_desk(){
		$this->info['desk'] += 1;
		//所需元宝
		if(empty($this->hd_cfg['desk'][$this->info['desk']])){
			if(empty($this->hd_cfg['desk']['default'])){
				Master::error(BANISH_008);
			}else{
				$cash  = $this->hd_cfg['desk']['default'];
			}
		}else{
			$cash = $this->hd_cfg['desk'][$this->info['desk']];
		}
		//直接在这里扣钱
		Master::sub_item($this->uid,KIND_ITEM,1,$cash);
		$this->save();

	}

	public function make_out()
	{
		$this->hd_cfg = Game::get_peizhi($this->hd_id);//发配
		if(empty($this->hd_cfg)){
			Master::error(BANISH_008);
		}

		if(empty($this->info['open'])){
			$HeroModel = Master::getHero($this->uid);
			$hnum = empty($HeroModel->info) ? 0 : count($HeroModel->info);
			$max = isset($this->hd_cfg['base']['hnum']) ? $this->hd_cfg['base']['hnum'] : 30;
			if($hnum < $max){
				Master::error(BANISH_007);
			}
			$this->info['open'] = 1;
			$this->save();
		}
		$this->outf['desk'] = $this->info['desk'];
	}

	public function back_data_desk(){
		$desk_cash = array();
		if(!empty($this->hd_cfg['desk'])){
			foreach ($this->hd_cfg['desk'] as $id => $cash){
				if($id == 'default'){
					continue;
				}
				$desk_cash[] = array(
					'id' => $id,
					'cash' => $cash,
				);
			}
		}
		Master::back_data($this->uid,$this->b_mol,'deskCashList',$desk_cash);
		$day = 100;
		if(!empty($this->hd_cfg['base']['day'])){
			$day = $this->hd_cfg['base']['day'];
		}
		Master::back_data($this->uid,$this->b_mol,'days',array('day'=>$day));
        $cash = 100;
        if(!empty($this->hd_cfg['base']['recall'])){
            $cash = $this->hd_cfg['base']['recall'];
        }
        Master::back_data($this->uid,$this->b_mol,'recall',array('cash'=>$cash));
	}

}
