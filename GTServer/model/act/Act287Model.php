<?php
require_once "ActHDBaseModel.php";

/*
 * 七天签到活动
 */
class Act287Model extends ActHDBaseModel
{
	public $atype = 287;//活动编号
	public $comment = "七天签到活动";
	public $b_mol = "sevenSign";//返回信息 所在模块
	public $b_ctrl = "cfg";//子类配置
	public $hd_id = 'huodong_287';//活动配置文件关键字
	public $item_type = 'hd287';
	public $date;
	
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
	    'day' => 0, //登录天数
        'num' => array(
        ), //已领取档次
        //'sign' => '' //签到日期
	);


	/*
	 * 构造输出
	 */
	public function data_out(){
	    //活动状态
		//活动信息

        $hd_cfg = array();
        $level = array();
        for($i=1;$i<8;$i++){
            $data['day'] = (int)$i;
            if($i <= $this->info['day']){
                $data['type'] = in_array($i,$this->info['num'])?2:1;//1可领取；2已领取
            }else {
                $data['type'] = 0;//未达领取条件
            }
            $level[] = $data;
        }
        $hd_cfg['level'] = $level;
        $hd_cfg['rwd'] = $this->hd_cfg['rwd'];
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
        $startTime = $this->hd_cfg['info']['sTime'];
        $endTime = $this->hd_cfg['info']['eTime'];
        if(!empty($this->hd_cfg)){
            //判断活动是否开启：1.注册时间大于活动开始时间；2.请求时间小于活动结束时间；3.领取次数小于7次；
            if($_SERVER['REQUEST_TIME'] >= $startTime && $_SERVER['REQUEST_TIME'] <= $endTime && count($this->info['num']) < 7){
                $state = 1;  //活动中
            }
        }
        return $state;
    }
    /*
     * 签到
     */
    public function sign(){
        if($this->get_state() == 0){
            return false;
        }
        if(!isset($this->info['sign'])){
            $this->info['sign'] = date('Y-m-d',$_SERVER['REQUEST_TIME']);
            $this->info['day'] += 1;
            $this->save();
        }
        $sign = strtotime($this->info['sign']);
        if(($_SERVER['REQUEST_TIME'] - $sign) > 24*3600){
            $this->info['sign'] = date('Y-m-d',$_SERVER['REQUEST_TIME']);//记录签到日期
            $this->info['day'] += 1;//签到次数加一
            $this->info['day'] = $this->info['day'] > 7?7:$this->info['day'];//最大签到次数7次
            $this->save();
        }
        $this->data_out();
    }
    /*
     * 领取奖励
     */
    public function rwd($id){
        if($this->get_state() == 0){
            Master::error(SEVENSIGN_MSG);
        }
        if($id < 1 || $id > 7){
            Master::error(SEVENSIGN_PARAMS_ERROR.$id);
        }
        if(in_array($id,$this->info['num'])){
            Master::error(SEVENSIGN_REPEAT_RWD);
        }
        if($id > $this->info['day'])
        {
            Master::error(SEVENSIGN_MORE_DAY);
        }
        foreach ($this->hd_cfg['rwd'] as $v){
            if($id == $v['id']){
                $rwd = $v['items'];
            }
        }
        if(empty($rwd)){
            Master::error(SEVENSIGN_HD_CFG);
        }
        foreach ($rwd as $val){
            Master::add_item($this->uid,$val['kind'],$val['id'],$val['count']);
        }
        array_push($this->info['num'],(int)$id);
        $this->save();

    }
    /*
     * 红点
     */
    public function get_news(){
        $news = 0; //不可领取
        if( $this->get_state() > 0){
           if(count($this->info['num']) < $this->info['day']){
               $news = 1;
           }
        }
        return $news;
    }
}
