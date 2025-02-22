<?php
require_once "ActBaseModel.php";
/*
 * 双12-剩余积分不删档
 */
class Act86Model extends ActBaseModel
{
	public $atype = 86;//活动编号
	public $comment = "双12-剩余积分不删档";
	public $b_mol = "zphuodong";//返回信息 所在模块
	public $b_ctrl = "total";//子类配置
	public $hd_id = 'huodong_290';//活动配置文件关键字


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
	 * 累计数量
	 * 领奖档次
	 */
	public $_init =  array(
		'fenshu' => 0,//剩余积分
	);
	
	//构造输出函数
	public function make_out(){
	    $outof = array(
	        'fenshu' => $this->info['fenshu'],
        );
        $this->outf = $outof;
	}
	
	/**
	 * 添加积分
	 * @param $num
	 */
	public function add($num){
		//判断活动是否开启
		$this->info['fenshu'] += $num;
		$this->save();
		
		Game::cmd_flow(37, 1, $num, $this->info['fenshu']);
	}
	
	/**
	 * 添加积分
	 * @param $num
	 */
	public function sub($num){
		//判断活动是否开启
		$this->info['fenshu'] -= $num;
		if($this->info['fenshu'] < 0){
			Master::error(ACT23_INTEGRAL_SHORT);
		}
		$this->save();
		
		Game::cmd_flow(37, 1, -$num, $this->info['fenshu']);
		
	}
}







