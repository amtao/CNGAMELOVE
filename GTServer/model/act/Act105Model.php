<?php
require_once "ActBaseModel.php";
/*
 * 惩戒来福 - 兑换
 */
class Act105Model extends ActBaseModel
{
	public $atype = 105;//活动编号
	
	public $comment = "惩戒来福-兑换";
	public $b_mol = "penalize";//返回信息 所在模块
	public $b_ctrl = "exchange";//返回信息 所在控制器
	public $hd_id = 'huodong_282';//活动配置文件关键字
	public $hd_cfg;

	/**
	 * @param unknown_type $uid   玩家id
	 * @param unknown_type $id    活动id
	 */
	public function __construct($uid)
	{
	    $this->uid = intval($uid);
	    //获取活动配置
	    Common::loadModel('HoutaiModel');
	    $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
	    if(!empty($this->hd_cfg['info']['id'])){
	        parent::__construct($uid,$this->hd_cfg['info']['id']);//执行基类的构造函数
	    }
	}
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 *  array('id' => num,)
		 * */
	);

	/*
	 * 记录兑换数量
	 * */
	public function add_items($id){
	    if(empty($this->info[$id])){
	        $this->info[$id] = 0;
	    }
	    $this->info[$id] +=1;
	    $this->save();
	}

	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$outof = array();

        if(!empty($this->hd_cfg['exchange'])){
            $init = $this->hd_cfg['exchange'];
            foreach ($init as $v){
                $value['id'] = $v['id'];
                $value['need'] = $v['need'];
                $value['items'] = array(
                    'kind' => $v['item']['kind'] ? $v['item']['kind'] : 1,
                    'id' => $v['item']['id'],
                    'count' => $v['item']['count']
                );
                $value['is_limit'] = $v['is_limit'];
                //是否限购
                if($v['is_limit'] == 1){
                    if (empty($this->info[$v['id']])){
                        $value['limit'] = $v['limit'];
                    }else{
                        $value['limit'] = $v['limit'] - $this->info[$v['id']];
                    }
                }else{
                    $value['limit'] = 0;
                }
                $outof[] = $value;
            }
        }

		//默认输出直接等于内部存储数据
		$this->outf = array('list'=>$outof);
	}
}
