<?php
require_once "ActBaseModel.php";
/*
 * 新官上任 - 商城
 */
class Act100Model extends ActBaseModel
{
	public $atype = 100;//活动编号
	
	public $comment = "新官上任-商城";
	public $b_mol = "xghuodong";//返回信息 所在模块
	public $b_ctrl = "shop";//返回信息 所在控制器
	public $hd_id = 'huodong_280';//活动配置文件关键字
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 * id=>num  商城档次id => 已购买数量
		*/
	);
	
	/**
	 * @param unknown_type $uid   玩家id
	 */
	public function __construct($uid)
	{
	    $this->uid = intval($uid);
	    Common::loadModel('HoutaiModel');
	    
	    $cfg = HoutaiModel::get_huodong_info($this->hd_id);
	    
	    parent::__construct($uid,$cfg['info']['id'].Game::day_0());//执行基类的构造函数
	}

	/*
	 * 添加
	 * */
	public function add($id,$num = 1)
	{
	    if(!is_int($num)){
	        Master::error(ACT_HD_ADD_SCORE_NO_INT);
	    }
	    $this->info[$id] +=$num;
	    $this->save();
	}

	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$outof = array();
		Common::loadModel('HoutaiModel');
		$cfg = HoutaiModel::get_huodong_info($this->hd_id);
        if(!empty($cfg)){
            $init = $cfg['shop'];
        }
        if(!empty($init)){
    		foreach ($init as $v){
    			$value['id'] = $v['id'];
    			$value['need'] = $v['need'];
    			$value['items'] = array(
    				'kind' => $v['items']['kind'] ? $v['items']['kind'] : 1,
    				'id' => $v['items']['id'],
    				'count' => $v['items']['count']
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
		$this->outf = $outof;
	}
}
