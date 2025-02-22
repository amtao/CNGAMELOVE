<?php
require_once "ActHDBaseModel.php";
/*
 * 活动6188
 */
class Act6188Model extends ActHDBaseModel
{
    public $atype = 6188;//活动编号
    public $comment = "充值翻牌";
    public $b_mol = "fphuodong";//返回信息 所在模块
    public $b_ctrl = "flop";//子类配置
    public $hd_id = 'huodong_6188';//活动配置文件关键字

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
        'cons' => 0,            //充值金额
        'reset' => 0,           //是否重置
        'draw' => array(),      //记录已领取的档次
        'desk' => array(),      //未翻牌位置信息
        'temp' => 0,            //记录翻牌次数的临时变量
        'flopNum' => array(),   //活动期间每天翻牌次数信息
    );

    /**
     * 资源消耗
     * @param $num
     */
    public function add($num){
        if( self::get_state() == 1 ){
            $this->info['cons'] += $num;
            $this->save();
        }
    }

    /**
     * 翻牌
     */
    public function get_rwd($id = 0){

        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //过滤
        $id = intval($id);
        if ($id < 0 || $id > $this->hd_cfg['num']-1){
            Master::error(PARAMS_ERROR);
        }
        //重复限制
        if(!empty($this->info['draw'][$id]) && empty($this->info['reset'])){
            Master::error(FLOP_REPEAT_RWD);
        }
        $riqi = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        $flop_total = array_sum($this->info['flopNum']);
        //翻牌次数限制
        if($this->info['flopNum'][$riqi]>=$this->hd_cfg['numMax']){
            Master::error(FLOP_CONTRIBUTION_MAX);
        }
        //翻牌次数是否充足
        $num = intval($this->info['cons']/$this->hd_cfg['need']-$flop_total);
        if ($num < 1){
            Master::error(FLOP_CONTRIBUTION_SHORT);
        }
        //初始化牌位子信息
        if(empty($this->info['desk'])){
            $this->info['desk'] = range(0,$this->hd_cfg['num']-1);
        }
        //是否重置
        if(!empty($this->info['reset'])){
            $this->info['reset'] = 0;
            $this->info['draw'] = array();
            $this->info['desk'] = range(0,$this->hd_cfg['num']-1);
        }

        $brwd = Game::get_key2id($this->hd_cfg['rwd'],'dc');
        if (!empty($this->info['draw'])){
            foreach ($this->info['draw'] as $v){
                    unset($brwd[$v]);
            }
        }
        $key = Game::get_rand_key1($brwd,'prob');
        $this->info['flopNum'][$riqi] += 1;
        $this->info['temp'] += 1;
        //奖励信息
        $rinfo = $brwd[$key];
        if(empty($rinfo)){
            Master::error(ACTHD_NO_REWARD);
        }

        //翻到1等奖或翻牌固定次数 重置标记
        if($key == 1 || $this->info['temp'] == 6){
            if ($key == 1){
                //应援日志
                $Sev6188Model = Master::getSev6188($this->hd_cfg['info']['id']);
                $Sev6188Model->add($this->uid,$rinfo['items']);
                $Sev6188Model->bake_data();
                Master::back_data($this->uid,'fphuodong','showeff',array('status'=>1),true);
            }
            $this->info['reset'] = 1;
            $this->info['temp'] = 0;
        }
        //领取奖励
        Master::add_item2($rinfo['items']);
        $this->info['draw'][$id] = $key;
        unset($this->info['desk'][$id]);
        $this->save();
    }

    /*
	 * 构造输出结构体
	 */
    public function make_out(){
        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
//            error_log($this->hd_cfg['info']['no']);
            return;
        }
        //获取活动信息
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        $hd_rwd = Game::get_key2id($hd_cfg['rwd'],'dc');
        unset($hd_cfg['rwd']);
        unset($hd_cfg['bout']);
        unset($hd_cfg['info']['no']);
        //将翻牌奖励进行处理
        $draw = array();
        $show = array();
        $desk = $this->info['desk'];
        if (empty($this->info['reset'])){//不重置状态
            foreach ($this->info['draw'] as $k=>$v){
                $draw[] = array('did'=>$k,'top'=>0,'get'=>1,'items'=>$hd_rwd[$v]['items']);
            }
        }else{//重置状态
            foreach ($this->info['draw'] as $k=>$v){
                $show[] = array('did'=>$k,'top'=>$v==1?1:0,'get'=>1,'items'=>$hd_rwd[$v]['items']);
                unset($hd_rwd[$v]);
            }
            $draw = $show;
            foreach ($hd_rwd as $x=>$z){
                shuffle($desk);
                $show[] = array('did'=>$desk[0],'top'=>$z['dc']==1?1:0,'get'=>0,'items'=>$z['items']);
                unset($desk[0]);
            }
            $this->outf['show'] = $show;                     //已领取到的档次
        }
        $flop_total = array_sum($this->info['flopNum']);
        $this->outf['cfg'] = $hd_cfg;                    //活动信息
        $this->outf['cons'] = $this->info['cons'];       //活动期间花费多少元宝
        $this->outf['flopNum'] = intval($this->info['cons']/$this->hd_cfg['need']-$flop_total); //翻牌次数
        $this->outf['reset'] = $this->info['reset']; //是否重置
        $this->outf['draw'] = $draw;

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
            $riqi = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
            $this->info['flopNum'][$riqi] = empty($this->info['flopNum'][$riqi])?0:$this->info['flopNum'][$riqi];
            $flop_total = array_sum($this->info['flopNum']);
            $num = intval($this->info['cons']/$this->hd_cfg['need']-$flop_total);
            if( $this->info['flopNum'][$riqi] < $this->hd_cfg['numMax'] && $num > 0 ){
                $news = 1; //可以领取
            }
        }
        return $news;
    }

}
