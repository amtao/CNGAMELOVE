<?php
require_once "ActHDBaseModel.php";
/*
 * 活动6189
 */
class Act6189Model extends ActHDBaseModel
{
    public $atype = 6189;           //活动编号
    public $comment = "点灯笼-春节活动";
    public $b_mol = "ddhuodong";    //返回信息 所在模块
    public $b_ctrl = "lantern";     //子类配置
    public $hd_id = 'huodong_6189'; //活动配置文件关键字
    public $hd_small = 12;          // 活动小灯笼数量
    public $hd_big = 1;            // 活动大灯笼数量

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
        'cons' => array(),      //充值金额
        'draw' => array(),      //奖励信息
        'day'  => 0,             //记录时间
        'light' => 0,           //点灯笼次数

    );

    /**
     * 资源消耗
     * @param $num
     */
    public function add($num){
        if( self::get_state() == 1 ){
            $riqi = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
            if (empty($this->info['cons'][$riqi])){
                $this->info['cons'][$riqi] = $num;
            }else{
                $this->info['cons'][$riqi] += $num;
            }
            $this->save();
        }
    }

    /**
     * 点灯
     */
    public function get_rwd($id = 0){

        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //已点开灯笼个数
        $num = count($this->info['draw']);
        $riqi = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        //重置
        if ($num == $this->hd_small+$this->hd_big-1 && $this->info['day'] != $riqi && $id < $this->hd_small){
            $this->info['draw'] = array();
        }
        if ($num >= $this->hd_small+$this->hd_big){
            Master::error(PARAMS_ERROR);
        }
        if ( $id >= $this->hd_small && $num != $this->hd_small ){
            Master::error(PARAMS_ERROR);
        }
        //点灯笼总次数
        $total = array_sum($this->info['cons']);
        if (intval($total/$this->hd_cfg['need'])-$this->info['light'] <= 0){
            Master::error(JINGYING_COUNT_LIMIT);
        }
        if (!empty($this->info['draw'][$id])){
            Master::error(HD_TYPE8_HAVE_LINGQU);
        }

        if ($num == $this->hd_small){//大灯笼
            $brwd = Game::get_key2id($this->hd_cfg['big'],'dc');
            $key = Game::get_rand_key1($brwd,'prob');
            if(empty($brwd[$key]['items']['id'])){
                Master::error(ACTHD_NO_REWARD);
            }
            //抽到元宝记录日志并公告
            if($brwd[$key]['items']['id'] == 1){
                $Sev6189Model = Master::getSev6189($this->hd_cfg['info']['id']);
                $Sev6189Model->add($this->uid,$brwd[$key]['items']);
                $Sev6189Model->bake_data();
            }
        }else{//小灯笼
            $brwd = Game::get_key2id($this->hd_cfg['small'],'dc');
            if (!empty($this->info['draw'])){
                foreach ($this->info['draw'] as $v){
                    unset($brwd[$v]);
                }
            }
            $key = Game::get_rand_key1($brwd,'prob');
        }
        $rinfo = $brwd[$key];
        if(empty($rinfo)){
            Master::error(ACTHD_NO_REWARD);
        }
        //领取奖励
        Master::add_item2($rinfo['items']);
        $this->info['day'] = empty($this->info['day'])?$riqi:($this->info['day']!=$riqi?$riqi:$this->info['day']);
        $this->info['draw'][$id] = $key;
        $this->info['light'] += 1;
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
            return;
        }
        //获取活动信息
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        $hd_small = Game::get_key2id($hd_cfg['small'],'dc');
        $hd_big = Game::get_key2id($hd_cfg['big'],'dc');
        unset($hd_cfg['info']['no']);
        unset($hd_cfg['small']);
        unset($hd_cfg['big']);

        //全开且过零点 重置
        $num = count($this->info['draw']);
        $riqi = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        if ($num == $this->hd_small+$this->hd_big && $this->info['day'] != $riqi){
            $this->info['draw'] = array();
        }

        $total = array_sum($this->info['cons']);
        $draw = range(0,$this->hd_small+$this->hd_big-1);
        $maxk = max($draw);
        foreach ($draw as $k=>$v){
            if (empty($this->info['draw'][$k])){
                $draw[$k] = array('did'=>$k);
            }else{
                if ($k == $maxk){
                    $draw[$k] = array('did'=>$k,'items'=>$hd_big[$this->info['draw'][$k]]['items']);
                }else{
                    $draw[$k] = array('did'=>$k,'items'=>$hd_small[$this->info['draw'][$k]]['items']);
                }
            }
        }
        $this->outf = $hd_cfg;                               //活动信息
        $this->outf['cons'] = array_sum($this->info['cons']);       //活动期间花费多少元宝
        $this->outf['light'] = intval($total/$hd_cfg['need'])-$this->info['light'];         //剩余点灯笼次数
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
            $draw = count($this->info['draw']);
            $total = array_sum($this->info['cons']);
            $num = intval($total/$this->hd_cfg['need']-$this->info['light']);
            if( $draw < $this->hd_small+$this->hd_big && $num > 0 ){
                $news = 1; //可以领取
            }
        }
        return $news;
    }

}
