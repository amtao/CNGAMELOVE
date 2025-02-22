<?php
require_once "ActHDBaseModel.php";

/*
 * 活动297
 */
class Act297Model extends ActHDBaseModel
{
    public $atype = 297;//活动编号
    public $comment = "情人节";
    public $b_mol = "lovehuodong";//返回信息 所在模块
    public $b_ctrl = "love";//子类配置
    public $hd_cfg ;//活动配置G
    public $hd_id = 'huodong_297';//活动配置文件关键字

    protected $_send_rank_id = 125;
    protected $_get_rank_id = 126;

    public $tip = 0;// 0:没有红点 1:触发红点

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'cons' => 0, //情人花
        'step' => 1,  //踩中的格子id
        'get'  => array(), //目标收花榜-领取奖励记录
        'send'  => array(), //目标送花榜-领取奖励记录
        'tip' => 1, //0:不在提示,1:提示
        'time' => 0, //每天刷新
    );


    /**
     * @param unknown_type $uid   玩家id
     * @param unknown_type $id    活动id
     */
    public function __construct($uid)
    {
        $this->uid = intval($uid);
        //获取活动配置
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        if(!empty($this->hd_cfg['info']['id'])){
            parent::__construct($uid,$this->hd_cfg['info']['id']);//执行基类的构造函数
        }
    }


    /*
     * 构造输出结构体
     */
    public function make_out(){

        if(!Game::is_today($this->info['time'])){
            $this->info['tip'] = 1; //0:不提示 1:提示
            $this->info['time'] = Game::get_now();
        }

        $this->tip = 0;  //0:没有红点 1:触发红点

        $send_hua = $this->send_score();  //送出去花的数量
        $get_hua = $this->get_score();   //收到花的数量

        //构造输出
        $this->outf = array();
        if( parent::get_state() == 0 ){
            Master::error(ACTHD_ACTIVITY_UNOPEN.__LINE__);
        }

        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        $this->outf['cfg']['info'] = $hd_cfg['info'];  //活动展示时间
        $this->outf['cfg']['msg'] = $hd_cfg['msg'];  //活动说明

        //圈圈配置
        $this->outf['cfg']['rwd'] = $hd_cfg['rwd'];

        //目标奖励--送花
        $this->outf['cfg']['send'] = array();
        foreach ($hd_cfg['send'] as  $k => $v ){

            $v['isGet'] = 0;  //是否已领取 0:不可领取 1:可领取 2:已领取
            if(in_array($v['id'],$this->info['send'])){
                $v['isGet'] = 2;
            }elseif($v['max'] <= $send_hua){
                $v['isGet'] = 1;
                $this->tip = 1;
            }
            $this->outf['cfg']['send'][] = $v;
        }


        //目标奖励--收花
        $this->outf['cfg']['get'] = array();
        foreach ($hd_cfg['get'] as  $k => $v ){

            $v['isGet'] = 0;  //是否已领取 0:不可领取 1:可领取 2:已领取
            if(in_array($v['id'],$this->info['get'])){
                $v['isGet'] = 2;
            }elseif($v['max'] <= $get_hua){
                $v['isGet'] = 1;
                $this->tip = 1;
            }
            $this->outf['cfg']['get'][] = $v;
        }

        //排名奖励
        $this->outf['cfg']['rank'] = $hd_cfg['rank'];

        $this->outf['cons'] = $this->info['cons']; //情人花
        $this->outf['step'] = $this->info['step']; //踩中的格子id
        $this->outf['send'] = $send_hua; //送出去花的数量
        $this->outf['get'] = $get_hua; //收花的数量
        $this->outf['tip'] = $this->info['tip']; //0:不提示 1:提示

    }


    //---------------------------- 辅助函数 ----------------------

    /**
     * 资源消耗
     * @param $num
     * @param $tip
     */
    public function yao($num,$tip){

        if( self::get_state() != 1 ){
            Master::error(ACTHD_OVERDUE);
        }

        //扣除元宝
        $need = $num == 10? $this->outf['cfg']['rwd']['needTen'] : $this->outf['cfg']['rwd']['need'];
        Master::sub_item($this->uid,KIND_ITEM,1,$need);

        $wlist = array();
        for ($i = 1; $i <= $num ; $i ++){
            $rid =  Game::get_rand_key(10000,$this->outf['cfg']['rwd']['list'],'prob_10000');
            $rwd = $this->outf['cfg']['rwd']['list'][$rid];
            Master::add_item2($rwd['items'],'','');

            $wlist[] = array(  //弹窗
                'id' => $rwd['items']['id'],
                'count' => $rwd['items']['count'],
                'kind' => $rwd['items']['kind'],
                'tip' => empty($rwd['tip'])?0:1,
            );

            $this->info['step'] = $rwd['id'];

            //比获得玫瑰花
            $roseid =  Game::get_rand_key(10000,$this->hd_cfg['rose'],'prob_10000');
            $roserwd = $this->hd_cfg['rose'][$roseid];
            if(!empty($roserwd)){
                Master::add_item2($roserwd['items'],'','');

                $wlist[] = array(  //弹窗
                    'id' => $roserwd['items']['id'],
                    'count' => $roserwd['items']['count'],
                    'kind' => $roserwd['items']['kind'],
                    'tip' => empty($roserwd['tip'])?0:1,
                );
            }
        }

        $this->info['tip'] = $tip; //0:不提示 1:提示
        $this->save();

        Master::$bak_data['a']['lovehuodong']['win'] = $wlist;
    }

    /*
     * 目标收花榜-领取奖励记录
     * @param $id 档次id
     */
    public function get_rwd($id){
        if( parent::get_state() == 0 ){
            Master::error(ACTHD_ACTIVITY_UNOPEN.__LINE__);
        }
        if(in_array($id,$this->info['get'])){
            Master::error(DAILY_IS_RECEIVE);
        }
        //获取档次
        foreach ($this->outf['cfg']['get'] as $value){
            //过滤不是对应档次
            if($id != $value['id']){
                continue;
            }
            //是否已领取 0:不可领取 1:可领取 2:已领取
            if($value['isGet'] != 1){
                Master::error(ACTHD_NO_RECEIVE);
            }
            //领取
            $this->info['get'][] = $id;
            $this->save();

            //发放奖励
            Master::add_item3($value['items']);

            return true;
        }
        Master::error(PARAMS_ERROR);

    }


    /*
    * 目标送花榜-领取奖励记录
    * @param $id 档次id
    */
    public function send_rwd($id){
        if( parent::get_state() == 0 ){
            Master::error(ACTHD_ACTIVITY_UNOPEN.__LINE__);
        }
        if(in_array($id,$this->info['send'])){
            Master::error(DAILY_IS_RECEIVE);
        }
        //获取档次
        foreach ($this->outf['cfg']['send'] as $value){
            //过滤不是对应档次
            if($id != $value['id']){
                continue;
            }
            //是否已领取 0:不可领取 1:可领取 2:已领取
            if($value['isGet'] != 1){
                Master::error(ACTHD_NO_RECEIVE);
            }
            //领取
            $this->info['send'][] = $id;
            $this->save();

            //发放奖励
            Master::add_item3($value['items']);

            return true;
        }
        Master::error(PARAMS_ERROR);

    }

    //---------------------------- 道具操作 ----------------------
    /**
     * 资源消耗
     * @param $num
     */
    public function add($num){
        if( self::get_state() == 1 ){
            $this->info['cons'] += $num;
            $this->save();
            Game::cmd_flow(6,343,$num,$this->info['cons']);
        }
    }

    /**
     * 资源消耗
     * @param $num
     */
    public function sub($num){
        if( parent::get_state() == 0 ){
            Master::error(ACTHD_ACTIVITY_UNOPEN.__LINE__);
        }
        $this->info['cons'] -= $num;
        if($this->info['cons'] < 0){
            Master::error(ITEMS_NUMBER_SHORT);
        }
        $this->save();
        Game::cmd_flow(6,343,-$num,$this->info['cons']);
    }



    /**
     * 我赠送的花  你收到花   保存
     * $fuid :谁收到的话
     * $num 花的数量
     */
    public function send_save($fuid,$num){

        //扣除赠送的花
        Master::sub_item($this->uid,11,343,$num);

        //我赠送的花
        $Redis125Model = Master::getRedis125($this->hd_cfg['info']['id']);
        $Redis125Model->zIncrBy($this->uid,$num);

        //fuid收到的花
        $Redis126Model = Master::getRedis126($this->hd_cfg['info']['id']);
        $Redis126Model->zIncrBy($fuid,$num);

    }

    /**
     * 收到花的数量
     */
    public function get_score(){

        $Redis126Model = Master::getRedis126($this->hd_cfg['info']['id']);

        return intval($Redis126Model->zScore($this->uid));
    }

    /**
     * 送出花的数量
     */
    public function send_score(){

        $Redis125Model = Master::getRedis125($this->hd_cfg['info']['id']);

        return intval($Redis125Model->zScore($this->uid));
    }



    //----------------------------u 下发----------------------
    /*
    * 下发每日任务信息前端
    */
    public function back_data_send_u(){

        $this->make_out();

        $data = array();
        $data['cons'] = $this->outf['cons']; //情人花
        $data['send'] = $this->outf['send']; //送出去花的数量
        $data['tip'] = $this->outf['tip']; //0:不提示 1:提示
        $data['step'] = $this->outf['step']; //踩中的格子id
        $data['get'] = $this->outf['get']; //收花的数量
        $data['cfg']['send'] = $this->outf['cfg']['send']; //目标奖励--送花
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);

    }

    /*
    * 下发每日任务信息前端
    */
    public function back_data_get_u(){

        $this->make_out();

        $data = array();
        $data['cfg']['get'] = $this->outf['cfg']['get']; //目标奖励--收花

        $data['cons'] = $this->outf['cons']; //情人花
        $data['send'] = $this->outf['send']; //送出去花的数量
        $data['tip'] = $this->outf['tip']; //0:不提示 1:提示
        $data['step'] = $this->outf['step']; //踩中的格子id
        $data['get'] = $this->outf['get']; //收花的数量

        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);
    }

    /*
    * 下发每日任务信息前端
    */
    public function back_data_cons_u(){
        $this->make_out();
        $data = array();
        $data['cons'] = $this->outf['cons']; //情人花
        $data['send'] = $this->outf['send']; //送出去花的数量
        $data['tip'] = $this->outf['tip']; //0:不提示 1:提示
        $data['step'] = $this->outf['step']; //踩中的格子id
        $data['get'] = $this->outf['get']; //收花的数量
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);
    }

    public function back_send_rank_data()
    {
        $RedisModel = Master::getRedis($this->_send_rank_id, $this->hd_cfg['info']['id']);
        $outf = $RedisModel->back_xs_rank($this->_send_rank_id, $this->uid);
        if (empty($outf)) {
            Master::error(ACT_HD_RANK_NO_EXISTS);
        }
        Master::back_data($this->uid,$this->b_mol,"SRank",$outf);
    }

    public function back_get_rank_data()
    {
        $RedisModel = Master::getRedis($this->_get_rank_id, $this->hd_cfg['info']['id']);
        $outf = $RedisModel->back_xs_rank($this->_get_rank_id, $this->uid);
        if (empty($outf)) {
            Master::error(ACT_HD_RANK_NO_EXISTS);
        }
        Master::back_data($this->uid,$this->b_mol,"GRank",$outf);
    }

    //----------------------------处理活动额外逻辑----------------------
    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取

        //判断活动是否还持续中
        if( parent::get_state() != 1 ){
            return $news;
        }

        if($this->tip == 1){
            $news = 1;
        }
        return $news;
    }



    /*
     * 返回活动信息--保存时不返回信息
     * 只返回当前活动在生效列表中对应的部分
     */
    public function back_data(){
        Common::loadModel('HoutaiModel');
        $outf = HoutaiModel::get_huodong_list($this->uid,$this->hd_id);
        Master::back_data($this->uid,'huodonglist','all',$outf,true);
    }

}




