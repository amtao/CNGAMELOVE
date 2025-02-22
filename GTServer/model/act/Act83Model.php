<?php
require_once "ActHDBaseModel.php";
/*
 * 双十一单品限购
 */
class Act83Model extends ActBaseModel
{
    public $atype = 83;//活动编号

    public $comment = "双十一单品限购";
    public $b_mol = "doubleEleven";//返回信息 所在模块
    public $b_ctrl = "list";//返回信息 所在控制器
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_285';//活动配置文件关键字
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 * 'id' => num,
		*/
	);
    public function __construct($uid)
    {
        $this->uid = intval($uid);
        //获取活动配置
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        if(!empty($this->hd_cfg['info']['id'])){
            parent::__construct($uid,$this->hd_cfg['info']['id'].Game::get_today_id());//执行基类的构造函数
        }
    }
	/*
	 * 单品购买
	 * */
	public function shopLimit($id)
	{
		foreach ($this->outf['items'] as $v) {
			if($id == $v['id']){
				$arrShop = $v;
				break;
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
		//扣除砖石
		Master::sub_item($this->uid,KIND_ITEM,1,$arrShop['need']);

		$this->info[$id] =$this->info[$id] + 1;
		$this->save();
		
		//添加items
		Master::add_item($this->uid,$arrShop['item']['kind'],$arrShop['item']['id'],$arrShop['item']['count']);


		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(32, 1);
	}

	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$outof = array();
        $outof['cft']['id'] = $this->hd_cfg['list_info']['id'];
        $outof['cft']['title'] = $this->hd_cfg['list_info']['title'];
        $outof['cft']['sTime'] = strtotime($this->hd_cfg['list_info']['startTime']);
        $outof['cft']['eTime'] = strtotime($this->hd_cfg['list_info']['endTime']);
        $outof['cft']['cd']['next'] = strtotime($this->hd_cfg['list_info']['endTime']);
        $outof['cft']['cd']['label'] = 'hdlistcd';
		foreach ($this->hd_cfg['shoplist'] as $id => $v){
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
			if($v['islimit'] == 1){
				if (empty($this->info[$id])){
					$value['limit'] = $v['limit'];
				}else{
					$value['limit'] = $v['limit'] - $this->info[$id];
				}
			}else{
				$value['limit'] = 0;
			}
			$outof['items'][] = $value;
		}
		//默认输出直接等于内部存储数据
		$this->outf = $outof;
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
        $startTime = strtotime($this->hd_cfg['list_info']['startTime']);
        $endTime = strtotime($this->hd_cfg['list_info']['endTime']);
        if(!empty($this->hd_cfg)){
 			if($_SERVER['REQUEST_TIME'] >= $startTime && $_SERVER['REQUEST_TIME'] <= $endTime){
 				$state = 1;  //活动中
 			}
        }
        return $state;
    }

    public function test($id){
        foreach ($this->outf as $v) {
            if($id == $v['id']){
                $arrShop = $v;
                break;
            }
        }
        var_dump($this->outf);
        var_dump($arrShop);
    }
}
