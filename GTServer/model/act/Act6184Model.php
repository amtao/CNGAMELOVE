<?php
require_once "ActHDBaseModel.php";
/*
 * 活动6184
 */
class Act6184Model extends ActHDBaseModel
{
    public $atype = 6184;//活动编号
    public $comment = "连续充值";
    public $b_mol = "lxczhuodong";//返回信息 所在模块
    public $b_ctrl = "continuity";//子类配置
    public $hd_id = 'huodong_6184';//活动配置文件关键字

    public function __costruct($uid)
    {
        parent::__construct($uid);
        if (empty($this->hd_cfg)) {
            return;
        }

    }
    
    /*
	 * 初始化结构体
	 */
    public $_init =  array(
        'dayGet'  => 0,
        'total'   => 0,
        'daily' => array(   //每日充值
        ),
        'series' => array(   //累计充值
        ),
    );

    /**
     * 资源消耗
     * @param $num
     */
    public function add($num){
        if( self::get_state() == 1){
            $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
            $riqi = intval($riqi);
            if (empty($this->info['daily'])){
                $this->info['daily'][$riqi]['val'] = $num;
            }else{
                $this->info['daily'][$riqi]['val'] += $num;
            }
            $this->info['total'] += $num;
            $this->save();
        }
    }

    /**
     * 连续充值获得奖励
     */
    public function get_rwd($id = 0){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);
        //奖励信息
        $rwdinfo = Game::get_key2id($this->hd_cfg['rwd'],'dc');
        if(empty($rwdinfo[$id])){
            Master::error(ACTHD_NO_REWARD);
        }
        $rwd = $rwdinfo[$id];
        if(empty($this->info['daily'][$riqi]['val']) || $this->info['daily'][$riqi]['val'] < $rwd['need']){
            Master::error(ACTHD_NO_RECEIVE);
        }
        if($this->info['daily'][$riqi]['get'][$id] > 0){
            Master::error(HD_TYPE8_HAVE_LINGQU);
        }
        //领取奖励
        Master::add_item3($rwd['items']);
        $this->info['daily'][$riqi]['get'][$id] = 1;
        $this->save();
    }

    /**
     * 连续连续充值获得奖励
     */
    public function get_totalrwd($id){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //奖励信息
        $rwdinfo = Game::get_key2id($this->hd_cfg['totalrwd'],'dc');
        if(empty($rwdinfo[$id])){
            Master::error(ACTHD_NO_REWARD);
        }
        $rwd = $rwdinfo[$id];
        $day = count($this->info['daily']);
        if(!empty($this->info['series'][$id]) || $this->info['total'] < $rwd['need'] || $day < $rwd['day']){
            Master::error(COMMON_DATA_ERROR);
        }
        //领取奖励
        Master::add_item3($rwd['items']);
        $this->info['series'][$id] = 1;
        $this->save();
    }
    
    
    public function data_out()
    {
        //构造输出
        $this->outf = array();

        if( self::get_state() == 0 ){
            Master::error(ACTHD_ACTIVITY_UNOPEN.__LINE__);
        }
        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        //每日充值领奖状态
        $today = $this->info['daily'][$riqi]['get'];
        foreach ($hd_cfg['rwd'] as $k=>$v){
            if (empty($today[$v['dc']])){
                $hd_cfg['rwd'][$k]['get'] = 0;
            }else{
                $hd_cfg['rwd'][$k]['get'] = 1;
            }
        }
        //累计充值领奖状态
        $totalday = $this->info['series'];
        foreach ($hd_cfg['totalrwd'] as $x => $y){
            if (empty($totalday[$y['dc']])){
                $hd_cfg['totalrwd'][$x]['get'] = 0;
            }else{
                $hd_cfg['totalrwd'][$x]['get'] = 1;
            }
        }
        $day = count($this->info['daily']);
        $this->outf['cfg'] = $hd_cfg;
        $this->outf['cons'] = empty($this->info['daily'][$riqi]['val'])?0:$this->info['daily'][$riqi]['val'];
        $this->outf['day'] = empty($day)?0:$day;
        $this->outf['total'] = $this->info['total'];
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);

    }

    /*
	 * 返回活动详细信息
	 */
    public function back_data_hd(){
        self::data_out();
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
            //奖励信息
            $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
            $riqi = intval($riqi);
            $rwd = $this->hd_cfg['rwd'];
            $totalrwd = $this->hd_cfg['totalrwd'];
            $days = empty($this->info['daily'])?0:count($this->info['daily']);
            $this->info['daily'][$riqi]['val'] = empty($this->info['daily'][$riqi]['val'])?0:$this->info['daily'][$riqi]['val'];
            $this->info['series'][$riqi]['get'] = empty($this->info['series'][$riqi]['get'])?0:$this->info['series'][$riqi]['get'];
            foreach ($rwd as $k=>$v){
                if($this->info['daily'][$riqi]['val'] >= $rwd[$k]['need'] && empty($this->info['daily'][$riqi]['get'][$v['dc']])){
                    $news = 1; //可以领取
                }
            }

            foreach ($totalrwd as $x=>$y){
                if(empty($this->info['series'][$y['dc']]) && $days>=$totalrwd[$x]['day'] && $this->info['total']>=$totalrwd[$x]['need']){
                    $news = 1; //可以领取
                }
            }

        }
        return $news;
    }


}
