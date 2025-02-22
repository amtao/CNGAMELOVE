<?php
require_once "ActHDBaseModel.php";
/*
 * 活动6226
 */
class Act6226Model extends ActHDBaseModel
{
	public $atype = 6226;//活动编号
	public $comment = "单笔连续充值";
	public $b_mol = "dblchuodong";//返回信息 所在模块
	public $b_ctrl = "cfg";//子类配置
	public $hd_id = 'huodong_6226';//活动配置文件关键字
    public $hd_dc = array();

    /*
	 * 初始化结构体
	 * 累计数量
	 * 领奖档次
	 */
    public $_init =  array(
        'wave' => 1,  //当前第几波
        'cons' => array(),  //已消耗(完成)量
        'rwd'  => array(),   //已领取的档次
    );

    /**
     * 获得奖励
     * $wave 第几波
     * $id   档次id
     */
    public function get_wave_rwd($wave,$id){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        if ($wave <= 0 || $wave > $this->info['wave'])return;
        if(!empty($this->info['rwd'][$wave][$id])){
            Master::error(KUAYAMEN_HAVE_RECEIVED_REWARD);
        }
        //奖励信息
        $rinfo = Game::get_key2id($this->hd_cfg['rwds'],'id');
        if(empty($rinfo[$wave])){
            Master::error(ACTHD_NO_REWARD);
        }
        $rwd = Game::get_key2id($rinfo[$wave]['rwd'],'dc');
        if(empty($rwd[$id])){
            Master::error(ACTHD_NO_REWARD);
        }
        if($this->info['cons'][$wave] < $rwd[$id]['need']){
            Master::error(ACTHD_NO_RECEIVE);
        }
        //领取奖励
        $ymd = Game::get_today_id();
        $this->info['rwd'][$wave][$id] = $ymd;
        Master::add_item3($rwd[$id]['items']);
        $this->save();
    }

    /**
     * 资源消耗
     * @param $num
     */
    public function add($num){
        if( self::get_state() == 1 ){
            $old = empty($this->info['cons'][$this->info['wave']])?0:$this->info['cons'][$this->info['wave']];
            $this->info['cons'][$this->info['wave']] = $num<=$old?$old:$num;
            //如果冲到最大档次 解锁下波
            if ($num >= 6480){
                $this->info['wave'] += 1;
            }
            $this->save();
        }
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
            //是否有充值
            if (!empty($this->info['cons'])){
                $rwds = Game::get_key2id($this->hd_cfg['rwds'],'id');
                //id:第几波    cons:充值黄金
                foreach ($this->info['cons'] as $id=>$cons){
                    if(!empty($rwds[$id]['rwd'])){
                        //对应波数的奖励信息
                        $rwd = $rwds[$id]['rwd'];
                        foreach ($rwd as $k=>$v){
                            if ($cons >= $v['need'] && empty($this->info['rwd'][$id][$v['dc']])){
                                $news = 1;
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $news;
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
		$hd_cfg = $this->hd_cfg;
		$hd_cfg['info']['id'] = $hd_cfg['info']['no'];
		unset($hd_cfg['info']['no']);
        $hd_cfg['info']['news'] = $this->get_news();
        foreach ($hd_cfg['rwds'] as $k=>$v){
            $id = $v['id'];
            $cons = empty($this->info['cons'][$id])?0:$this->info['cons'][$id];
            foreach ($v['rwd'] as $x=>$y){
                $dc = $y['dc'];
                $hd_cfg['rwds'][$k]['rwd'][$x]['cons'] = $cons;
                $hd_cfg['rwds'][$k]['rwd'][$x]['get'] = empty($this->info['rwd'][$id][$dc])?0:1;
            }
        }
        $hd_cfg['wave'] = $this->info['wave'];
		$this->outf = $hd_cfg;
	}
}
