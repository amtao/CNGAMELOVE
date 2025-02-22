<?php
require_once "ActHDBaseModel.php";
/*
 * 活动6187
 */
class Act6187Model extends ActHDBaseModel
{
    public $atype = 6187;//活动编号
    public $comment = "国力庆典";
    public $b_mol = "glqdhuodong"; //返回信息 所在模块
    public $b_ctrl = "celebration";//子类配置
    public $hd_id = 'huodong_6187';//活动配置文件关键字
    public $hd_redis = array(0=>'6200',1=>'6201',2=>'6202',3=>'6203',4=>'6204',5=>'6205',6=>'6206',7=>'6207',8=>'6208',9=>'6187');//redis排行榜信息

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
        'total' => array(),     //总国力
        'get'   => array(),     //总国力档次领奖状态
    );

    /**
     * 资源消耗
     * @param $num
     */
    public function add_total($type,$num){
        if( self::get_state() == 1 ){

            $this->info['total'][$type] = empty($this->info['total'][$type])?$num:$this->info['total'][$type]+$num;
            $this->save();
            $total = array_sum($this->info['total']);
            //保存到排行榜中
            $Redis6187Model = Master::getRedis6187($this->hd_cfg['info']['id']);
            $paihang = $Redis6187Model->zScore($this->uid);
            if ($total > $paihang){
                $Redis6187Model->zAdd($this->uid, $total);
            }
        }
    }

    /**
     * 获得奖励
     */
    public function get_rwd($id = 0){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        if (!empty($this->info['get'][$id])){
            Master::error(HD_TYPE8_HAVE_LINGQU);
        }
        $total = array_sum($this->info['total']);
        //奖励信息
        $rinfo = Game::get_key2id($this->hd_cfg['score'],'dc');
        if(empty($rinfo[$id])){
            Master::error(ACTHD_NO_REWARD);
        }
        if($total < $rinfo[$id]['need']){
            Master::error(ACTHD_NO_RECEIVE);
        }
        //领取奖励
        Master::add_item3($rinfo[$id]['items']);
        $this->info['get'][$id] = 1;
        $this->save();
    }

    /**
     * 活动 初始化信息
     */
    public function data_out(){
        $this->outf = array();
        if( self::get_state() == 0 ){
//            error_log($this->hd_cfg['info']['no']);
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        $riqi = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        $Act6200Model = Master::getAct6200($this->uid);
        $total = $this->info['total'];
        $day  = $Act6200Model->info[$riqi];
        foreach ($hd_cfg['score'] as $a=>$b){
            $id = $b['dc'];
            $hd_cfg['score'][$a]['get'] = empty($this->info['get'][$id])?0:$this->info['get'][$id];
        }
        foreach ($hd_cfg['rule'] as $k=>$v){
            $id = $v['id'];
            $hd_cfg['rule'][$k]['day'] = empty($day[$id])?0:$day[$id];      //每日国力
            $hd_cfg['rule'][$k]['total'] = empty($total[$id])?0:$total[$id];//累计国力
        }
        $rule = $hd_cfg['rule'];
        unset($hd_cfg['rule']);
        unset($hd_cfg['exchange']);
        //今日积分总榜个人信息
        $Redis6200Model = Master::getRedis6200($this->_get_day_redis_id());
        //累计积分总榜个人信息
        $Redis6187Model = Master::getRedis6187($this->hd_cfg['info']['id']);
        $hd_cfg['daygl'] = intval($Redis6200Model->zScore($this->uid));
        $hd_cfg['dayRank'] = $Redis6200Model->get_rank_id($this->uid);
        $hd_cfg['allgl'] = $Redis6187Model->zScore($this->uid);
        $hd_cfg['totalRank'] = intval($Redis6187Model->get_rank_id($this->uid));

        Master::back_data($this->uid,$this->b_mol,'rule',$rule);
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$hd_cfg);
        Master::back_data($this->uid,$this->b_mol,'exchange',$this->back_data_exchange());
        Master::back_data($this->uid,$this->b_mol,'shop',$this->back_data_shop());
    }

    /*
	 * 每日排行榜总览
	 */
    public function Paihang_day(){
        $paihangZl = array();
        foreach ($this->hd_cfg['rule'] as $v){
            $id = $v['id'];
            $RedisName = 'getRedis'.$this->hd_redis[$id];
            $RedisModel = Master::$RedisName($this->_get_day_redis_id());
            $rid = $RedisModel->get_rank_id($this->uid);
            $paihangZl[] = array('id'=>$id,'rid'=>$rid);
        }
        Master::back_data($this->uid,$this->b_mol,'paihangZl',$paihangZl);
    }

    //排行榜信息
    public function paihang($type){
        if (empty($this->hd_redis[$type])){
            Master::error(PARAMS_ERROR);
        }
        $modeName = 'getAct'.$this->hd_redis[$type];
        $RedisName = 'getRedis'.$this->hd_redis[$type];
        switch ($type){
            case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7: case 8:
                $ActModel = Master::$modeName($this->uid);
                $ActModel->back_hdcfg();
                $RedisModel = Master::$RedisName($this->_get_day_redis_id());
                $RedisModel->back_data();
                $RedisModel->back_data_my($this->uid);
                break;
            case 9:
                //奖励信息
                if (!empty($this->hd_cfg['total'])){
                    Master::back_data($this->uid,'glqdhuodong','cbrwd',$this->hd_cfg['total']);
                }
                //累计积分总榜个人信息
                $Redis6187Model = Master::getRedis6187($this->hd_cfg['info']['id']);
                $Redis6187Model->back_data();
                $Redis6187Model->back_data_my($this->uid);
                break;
            default:
                Master::error(PARAMS_ERROR);
                break;
        }

    }

    //刷新排行榜信息
    public function flush($type){
        if (empty($this->hd_redis[$type])){
            Master::error(PARAMS_ERROR);
        }
        $RedisName = 'getRedis'.$this->hd_redis[$type];
        $hid = $type == 9?$this->hd_cfg['info']['id']:$this->_get_day_redis_id();
        $RedisModel = Master::$RedisName($hid);
        $RedisModel->back_data_flush();
        $RedisModel->back_data_my($this->uid);
    }

    /*
	 * 构造输出结构体
	 */
    public function make_out(){
        $this->outf = array();
        if( self::get_state() == 0 ){
//            error_log($this->hd_cfg['info']['no']);
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        unset($hd_cfg['rule']);
        foreach ($hd_cfg['score'] as $a=>$b){
            $id = $b['id'];
            $hd_cfg['score'][$a]['get'] = empty($this->info['get'][$id])?0:$this->info['get'][$id];
        }
        $this->outf =$hd_cfg;
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
            $rinfo = $this->hd_cfg['score'];
            $total = array_sum($this->info['total']);
            if(!empty($rinfo) && !empty($total)){
                foreach ($rinfo as $k=>$v){
                    if ($total > $v['need'] && empty($this->info['get'][$v['dc']])){
                        $news = 1; //可以领取
                    }
                }
            }
        }
        return $news;
    }

}
