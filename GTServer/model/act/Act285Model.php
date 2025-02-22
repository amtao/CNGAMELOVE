<?php
require_once "ActHDBaseModel.php";

/*
 * 双十一活动
 */
class Act285Model extends ActHDBaseModel
{
	public $atype = 285;//活动编号
	public $comment = "双十一活动";
	public $b_mol = "doubleEleven";//返回信息 所在模块
	public $b_ctrl = "cfg";//子类配置
	public $hd_id = 'huodong_285';//活动配置文件关键字
	public $item_type = 'hd285';
	public $date;
	
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
	);


	/*
	 * 构造输出
	 */
	public function data_out(){

	    //活动状态

		//获取单品限购列表
		$Act83Model = Master::getAct83($this->uid);
	    if($Act83Model->getstate() > 0){
            $Act83Model->back_data();
        }
	    //获取特惠礼包列表
        $Act84Model = Master::getAct84($this->uid);
        if($Act84Model->getstate() > 0){
            $Act84Model->back_data_hd();
        }
		//累计充值领取档次
		$Act85Model = Master::getAct85($this->uid);
        $Act85Model->back_data();
		//活动信息
        $hd_cfg = array();
        if($this->get_state() > 0){
            $hd_cfg['sTime'] = $this->hd_cfg['info']['sTime'];
            $hd_cfg['eTime'] = $this->hd_cfg['info']['eTime'];
            $hd_cfg['info']['id'] = $this->hd_cfg['rwd_info']['id'];
            $hd_cfg['info']['title'] = $this->hd_cfg['rwd_info']['title'];
            $hd_cfg['info']['sTime'] = strtotime($this->hd_cfg['rwd_info']['startTime']);
            $hd_cfg['info']['eTime'] = strtotime($this->hd_cfg['rwd_info']['endTime']);
            $hd_cfg['info']['cd']['next'] = $this->hd_cfg['rwd_info']['endTime'];
            $hd_cfg['info']['cd']['label'] = 'hdrwdcd';
            $hd_cfg['info']['pindex'] = $this->hd_cfg['rwd_info']['pindex'];
            $hd_cfg['rwd'] = $this->hd_cfg['rwd'];
        }
	    Master::back_data($this->uid,$this->b_mol,'cfg',$hd_cfg);
	}
	
	

	
	public function back_data_hd() {
	    self::data_out();
	}
    /**
     * 活动活动状态
     * 返回:
     * 0: 活动未开启
     * 1: 活动中
     * 2: 活动结束,展示中
     */
    public function get_state(){
        $state = 0;  //活动未进行
        $startTime = strtotime($this->hd_cfg['rwd_info']['startTime']);
        $endTime = strtotime($this->hd_cfg['rwd_info']['endTime']);
        if(!empty($this->hd_cfg)){
            if($_SERVER['REQUEST_TIME'] >= $startTime && $_SERVER['REQUEST_TIME'] <= $endTime){
                $state = 1;  //活动中
            }
        }
        return $state;
    }

    public function test(){
        var_dump($this->hd_cfg);
    }
}
