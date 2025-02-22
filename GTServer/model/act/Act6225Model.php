<?php
require_once "ActHDBaseModel.php";
/*
 * 活动6225
 */
class Act6225Model extends ActHDBaseModel
{
	public $atype = 6225;//活动编号
	public $comment = "充值活动-新累天充值";
	public $b_mol = "cjttczhuodong";//返回信息 所在模块
	public $b_ctrl = "cjttcz";//子类配置
	public $hd_id = 'huodong_6225';//活动配置文件关键字

    /*
	 * 初始化结构体
	 * 累计数量
	 * 领奖档次
	 */
    public $_init =  array(
        'cons' => array(),  //已消耗(完成)量
        'rwd' => array(),  //已领取的档次
        'continuity' => array(),  //已领取累计档次
    );

    /**
     * 资源消耗
     * @param $num
     */
    public function add($num){
        $day = intval(Game::day_dur($this->hd_cfg['info']['sTime']));//获取活动开启的天数
        if( self::get_state() == 1 ){
            $this->info['cons'][$day] += $num;
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
        if (!empty($this->info['rwd'][$id])){
            Master::error(ACT_30_YILING);
        }
        //奖励信息
        $rinfo = Game::get_key2id($this->hd_cfg['rwd'],'id');
        if(empty($rinfo[$id])){
            Master::error(ACTHD_NO_REWARD);
        }
        if(empty($this->info['cons'][$id]) || $this->info['cons'][$id] < $rinfo[$id]['need']){
            Master::error(ACTHD_NO_RECEIVE);
        }
        //领取奖励
        Master::add_item3($rinfo[$id]['items']);
        $ymd = Game::get_today_id();
        $this->info['rwd'][$id] = $ymd;
        $this->save();
    }

    /**
     * 获得奖励
     */
    public function get_totalrwd($id = 0){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        if (!empty($this->info['continuity'][$id])){
            Master::error(ACT_30_YILING);
        }
        //奖励信息
        $rinfo = Game::get_key2id($this->hd_cfg['continuity'],'id');
        if(empty($rinfo[$id])){
            Master::error(ACTHD_NO_REWARD);
        }
        $count = $this->get_day();
        if($count < $rinfo[$id]['need']){
            Master::error(ACTHD_NO_RECEIVE);
        }
        //领取奖励
        Master::add_item3($rinfo[$id]['items']);
        $ymd = Game::get_today_id();
        $this->info['continuity'][$id] = $ymd;
        $this->save();
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
            //每日充值
            $day = intval(Game::day_dur($this->hd_cfg['info']['sTime']));//获取活动开启的天数
            $rinfo = Game::get_key2id($this->hd_cfg['rwd'],'id');
            if ($this->info['cons'][$day] >= $rinfo[$day]['need'] && empty($this->info['rwd'][$day])){
                $news = 1;
            }
            //累计达到
            $count = $this->get_day();
            foreach ($this->hd_cfg['continuity'] as $y){
                if ($count >= $y['need'] && empty($this->info['continuity'][$y['id']])){
                    $news = 1;
                    break;
                }
            }
        }
        return $news;
    }

    /*
     * 返回达成目标的天数
     */
    public function get_day(){
        $count = 0;//达成目标的天数
        foreach ($this->hd_cfg['rwd'] as $x=>$y){
            if (!empty($this->info['cons'][$y['id']]) && $this->info['cons'][$y['id']]>=$y['need']){
                $count += 1;
            }
        }
        return $count;
    }

    /*
     * 构造输出结构体
     */
	public function make_out(){
		//构造输出
		$this->outf = array();
        if( self::get_state() == 0 ){
            return ;
        }
        $day = intval(Game::day_dur($this->hd_cfg['info']['sTime']));//获取活动开启的天数
		$hd_cfg = $this->hd_cfg;
		$hd_cfg['info']['id'] = $hd_cfg['info']['no'];
		unset($hd_cfg['info']['no']);
        $hd_cfg['info']['news'] = $this->get_news();
        $hd_cfg['rwd'] = Game::get_key2id($this->hd_cfg['rwd'],'id');
        $hd_cfg['rwd'] = $hd_cfg['rwd'][$day];
        $hd_cfg['rwd']['get'] = empty($this->info['rwd'][$hd_cfg['rwd']['id']])?0:1;
        foreach ($hd_cfg['continuity'] as $k=>$v){
            if (!empty($this->info['continuity'][$v['id']])){
                $hd_cfg['continuity'][$k]['get'] = 1;
                continue ;
            }
            $hd_cfg['continuity'][$k]['get'] = 0;
        }
		$this->outf['cfg'] = $hd_cfg;
		$this->outf['cons'] = empty($this->info['cons'][$day])?0:$this->info['cons'][$day];
        $this->outf['day'] = $this->get_day();
	}
}
