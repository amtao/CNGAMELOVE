<?php
require_once "ActHDBaseModel.php";
/*
 * 活动6168
 */
class Act6168Model extends ActHDBaseModel
{
    public $atype = 6168;//活动编号
    public $comment = "天天充值";
    public $b_mol = "edczhuodong";//返回信息 所在模块
    public $b_ctrl = "everyday";//子类配置
    public $hd_id = 'huodong_6168';//活动配置文件关键字

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

        'dayGet'  => 0,      //连续充值领奖状态
        'Daily' => array(   //每日充值
        ),
        'total' => array(   //累计充值
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
            $this->info['Daily'][$riqi]['val'] = isset($this->info['Daily'][$riqi]['val'])?$this->info['Daily'][$riqi]['val']+$num:$num;
            if ($this->info['Daily'][$riqi]['val'] >= $this->hd_cfg['quota'] && empty($this->info['total'][$riqi])){
                $this->info['total'][$riqi] = 1;
            }
            $this->save();
        }
    }

    /**
     * 天天充值获得奖励
     */
    public function get_rwd($id = 0){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);
        if($this->info['Daily'][$riqi]['get'] > 0){
            Master::error(HD_TYPE8_HAVE_LINGQU);
        }
        if($this->info['Daily'][$riqi]['val'] < $this->hd_cfg['quota']){
            Master::error(ACTHD_NO_RECEIVE);
        }
        //奖励信息
        $Sev6168Model = Master::getSev6168($this->hd_cfg['info']['id']);
        $rinfo = $Sev6168Model->info['items'];
        if(empty($rinfo)){
            Master::error(ACTHD_NO_REWARD);
        }

        $this->info['Daily'][$riqi]['get'] = 1;
        //领取奖励
        Master::add_item3($rinfo);
        $this->save();
    }

    /**
     * 天天连续充值获得奖励
     */
    public function get_totalrwd(){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        if( $this->info['dayGet'] > 0){
            Master::error(HD_TYPE8_HAVE_LINGQU);
        }
        $days = count($this->info['total']);
        //奖励信息
        $rinfo = $this->hd_cfg['totalrwd'];
        if(empty($rinfo)){
            Master::error(ACTHD_NO_REWARD);
        }
        if($days < $this->hd_cfg['duration']){
            Master::error(ACTHD_NO_RECEIVE);
        }
        $this->info['dayGet'] = 1;
        //领取奖励
        Master::add_item3($rinfo);
        $this->save();
    }
    
    
    public function make_out()
    {
        //构造输出
        $this->outf = array();

        if( self::get_state() == 0 ){
            return ;
        }
        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);
        $this->outf['cons'] = empty($this->info['Daily'][$riqi]['val'])?0:$this->info['Daily'][$riqi]['val'];
        $this->outf['consGet'] = empty($this->info['Daily'][$riqi]['get'])?0:$this->info['Daily'][$riqi]['get'];
        $this->outf['day'] = empty($this->info['total'])?0:count($this->info['total']);
        $this->outf['dayGet'] = $this->info['dayGet'];

    }

    /*
	 * 返回活动详细信息
	 */
    public function back_data_hd(){
        $this->make_out();
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
        $Sev6168Model = Master::getSev6168($this->hd_cfg['info']['id']);
        $Sev6168Model->back_data();
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
            $quota = $this->hd_cfg['quota'];//天天充值达标值
            $duration = $this->hd_cfg['duration'];//连续天天充值达标天数
            $this->info['Daily'][$riqi]['val'] = empty($this->info['Daily'][$riqi]['val'])?0:$this->info['Daily'][$riqi]['val'];
            if(!empty($quota) && empty($this->info['Daily'][$riqi]['get']) && $this->info['Daily'][$riqi]['val'] >= $quota){
                $news = 1; //可以领取
            }
            $days = empty($this->info['total'])?0:count($this->info['total']);
            if(!empty($duration) && empty($this->info['dayGet']) && $days>=$duration){
                $news = 1; //可以领取
            }
        }
        return $news;
    }


}
