<?php
require_once "ActBaseModel.php";
/*
 * 皇宫
 */
class Act18Model extends ActBaseModel
{
	public $atype = 18;//活动编号
	
	public $comment = "皇宫";
	public $b_mol = "fengxiandian";//返回信息 所在模块
	public $b_ctrl = "qingAn";//返回信息 所在控制器
    public $b_key1  = "fengxiandian_time";
    public $b_key2  = "fengxiandian_status";
    public $b_dval  = array('val'=>0);

    /*
     * 初始化结构体
     */
    public $_init =  array(//排行膜拜信息
   //type => 0 or 1
        1 => 0,     //跨服势力榜
        2 => 0,     //跨服好感榜
        3 => 0,	    //跨服宫斗榜
        4 => 0,     //跨服宫殿宫斗榜
        5 => 0,     //跨服联盟榜
    );
	
	/**
	 * 请安
	 */
	public function qingAn($type){
		if($this->info['type'] != 0){
			Master::error(PALACE_RESPECT_COMPLETE);
		}
        $Sev5Model = Master::getSev5();
        $types = $Sev5Model->get_keys();
        if (empty($types) || !in_array($type,$types)){
            Master::error(PARAMS_ERROR);
        }
        if (!empty($this->info[$type])){
            Master::error(RAKN_MOBAIED);
        }
        $this->info[$type] = 1;
		$this ->save();
        $kuafu_cfg = Game::get_peizhi('fengxiandian');
        if (empty($kuafu_cfg['qingAn']))Master::error(ACT_14_CONFIGWRONG);
        $key = Game::get_rand_key1($kuafu_cfg['qingAn'],'prob');
		Master::add_item2($kuafu_cfg['qingAn'][$key]);
	}

    /*
     * 构造输出结构体
     */
    public function make_out(){
        //默认输出直接等于内部存储数据
        $this->outf = array();
        $mem = Common::getCacheBySevId(1);
        $val1 = $mem->get($this->b_key1)==false?$this->b_dval:$mem->get($this->b_key1);
        $val2 = $mem->get($this->b_key2)==false?$this->b_dval:$mem->get($this->b_key2);
        $Sev5Model = Master::getSev5();
        $types = $Sev5Model->get_keys();
        foreach ($this->info as $k=>$v){
            if ($k==5 && Game::is_over($val1['val'])){
                $this->outf[] = array('id'=>$k,'type'=>$val2['val']);
                continue;
            }
            if (empty($types) || !in_array($k,$types)){
                $this->outf[] = array('id'=>$k,'type'=>-1);
            }else{
                $this->outf[] = array('id'=>$k,'type'=>$v);
            }

        }
    }
	
}






