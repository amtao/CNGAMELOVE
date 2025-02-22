<?php
require_once "ActHDBaseModel.php";
/*
 * 活动262
 */
class Act262Model extends ActHDBaseModel
{
	public $atype = 262;//活动编号
	public $comment = "充值活动-累天充值";
	public $b_mol = "czhuodong";//返回信息 所在模块
	public $b_ctrl = "leitian";//子类配置
	public $hd_id = 'huodong_262';//活动配置文件关键字
	
	
	/*
	 * 初始化结构体
	 * 累计数量
	 * 领奖档次
	 */
	public $_init =  array(
		'cons' => array(),   //已消耗(完成)量
        'day'  => array(),
        'temp' => array(),
		'rwd' => 0,  //已领取的档次
	);
	
	
	/**
	 * 充值记录日期
	 * @param $num
	 */
	public function add($num){
		//判断活动是否开启
		if( self::get_state() == 1 ){
			//获取进入的年月日
			$riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
			$riqi = intval($riqi);
			if (!is_array($this->info['cons'])){
                $this->info['cons'] = array();
            }
			//判断今天今天是否充值过了
			if(empty($this->info['cons'][$riqi])){
				//记录今天充值
				$this->info['cons'][$riqi] = $num;
                $this->info['temp'] = array();
			}else{
                $this->info['cons'][$riqi] += $num;
            }
            foreach ($this->hd_cfg['brwd'] as $k=>$v){
                if ($this->info['cons'][$riqi] >= $v['need'] && empty($this->info['temp'][$k])){
                    $this->info['day'][$k] = empty($this->info['day'][$k])?1:$this->info['day'][$k]+1;
                    $this->info['temp'][$k] = 1;
                }
            }
            $this->save();
		}
	}

    /**
     * 获得奖励
     */
    public function get_rwd($id = 0){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        $this->info['rwd'] += 1;
        $day = count($this->info['cons']);
        if (empty($this->info['day'][$this->info['rwd']])){
            Master::error(ACTHD_NO_RECEIVE);
        }
        //奖励信息
        $rinfo = $this->hd_cfg['brwd'][$this->info['rwd']];
        if(empty($rinfo)){
            Master::error(ACTHD_NO_REWARD);
        }
        if ($rinfo['day'] > $day){
            Master::error(ACTHD_NO_REWARD);
        }
        //领取奖励
        Master::add_item3($rinfo['items']);
        $this->save();
    }

    /*
	 * 构造输出结构体
	 */
    public function make_out(){
        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
            error_log($this->hd_cfg['info']['no']);
        }
        if (!is_array($this->info['cons'])){
            $this->info['cons'] = array();
        }
        $hd_cfg = $this->hd_cfg;
        $day = count($this->info['cons']);
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['brwd']);
        unset($hd_cfg['info']['no']);
        foreach ($hd_cfg['rwd'] as $k=>$v){
            if (empty($this->info['day'][$v['id']]) && $v['day']>$day){
                $hd_cfg['rwd'][$k]['rachargeDay'] = 0;
            }else{
                $hd_cfg['rwd'][$k]['rachargeDay'] = $this->info['day'][$v['id']];
            }
        }
        $this->outf['cfg'] = $hd_cfg;  //活动期间花费多少元宝
        $this->outf['rwd'] = $this->info['rwd'];  //领取到的档次
    }

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取
        if( self::get_state() == 0){
            $news = 0;
        }else{
            if (!is_array($this->info['cons'])){
                $this->info['cons'] = array();
            }
            //奖励信息
            $rinfo = $this->hd_cfg['brwd'][$this->info['rwd']+1];
            if(!empty($rinfo) && $this->info['day'][$this->info['rwd']+1] >= $rinfo['day']){
                $news = 1; //可以领取
            }
        }
        return $news;
    }
}
