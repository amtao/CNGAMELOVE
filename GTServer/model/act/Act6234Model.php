<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6234
 */
class Act6234Model extends ActHDBaseModel
{
    public $atype = 6234;//活动编号
    public $comment = "荷诞日";
    public $b_mol = "fanghedeng";//返回信息 所在模块
    public $b_ctrl = "hedenginfo";//子类配置
    public $hd_id = 'huodong_6234';//活动配置文件关键字

    /*
     * 初始化结构体
     * 累计数量
     * 领奖档次
     */
    public $_init =  array(
        'cons'    => 0,       //积分
        'log'     =>array(),  //领奖记录
        "hdtype"  => 1,       //"特殊情况 1:放荷灯 2:春节活动 "
        'shop'    =>array(),  //商城购买信息
        'exchange'=>array(),  //兑换信息
    );

    /**
     * 荷诞日
     * @param int $id
     */
    public function play($num = 1){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        if( self::get_state() == 2){
            Master::error(ACTHD_OVERDUE);
        }
        if (empty($this->hd_cfg['need']) && empty($this->hd_cfg['list'])){
            Master::error(ITEMS_ERROR);
        }
        //扣除道具
        Master::sub_item($this->uid,KIND_ITEM,$this->hd_cfg['need'],$num);

        //随机奖励
        $items = array();
        $list = $this->hd_cfg['list'];
        for ($i = 0; $i < $num; $i++){
            $rid =  Game::get_rand_key(10000,$list,'prob');
            if (empty($items[$rid])){
                $items[$rid] = array('id'=>$list[$rid]['id'],'kind'=>$list[$rid]['kind'],'count'=>$list[$rid]['count']);
            }else{
                $items[$rid]['count'] += $this->hd_cfg['list'][$rid]['count'];
            }
        }

        //积分
        $score = $num*10;
        $this->info['cons'] += $score;
        $this->save();

        $this->hd_cfg['fixed']['count'] *= $num;
        $items[] = $this->hd_cfg['fixed'];
        if (empty($items)){
            Master::error(ITEMS_ERROR);
        }

        //排行榜
        $Redis6234Model = Master::getRedis6234($this->hd_cfg['info']['id']);
        $Redis6234Model->zIncrBy($this->uid,$score);

        //领取奖励
        Master::add_item3($items);
    }

    /**
     * 获得奖励
     * @param int $id
     */
    public function get_rwd($id = 0){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //奖励信息
        $rwds = Game::get_key2id($this->hd_cfg['total'],'id');
        $itmes = $rwds[$id];
        if (empty($itmes)){
            Master::error(ACTHD_NO_REWARD);
        }
        if (!empty($this->info[$id]) || $this->info['cons'] < $itmes['need']){
            Master::error(ACTHD_NO_RECEIVE);
        }
        //领取奖励
        Master::add_item3($itmes['items']);
        $this->info[$id] = $_SERVER['REQUEST_TIME'];
        $this->save();
    }

    /*
 * 排行榜 和奖励
 * */
    public function paihang(){
        //个人排行榜
        $Redis6234Model = Master::getRedis6234($this->hd_cfg['info']['id']);
        $Redis6234Model->back_data();
        $Redis6234Model->back_data_my($this->uid);
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
            $rwds = $this->hd_cfg['total'];
            $need = $this->hd_cfg['need'];
            $ItemModel = Master::getItem($this->uid);
            if(!empty($ItemModel->info[$need]['count']))return 1;
            foreach ($rwds as $v){
                if ($this->info['cons'] > $v['need']){
                    $news = 1;
                    break;
                }
            }
        }
        return $news;
    }



    /*
     * 构造输出结构体
     */
    public function data_out(){
        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
            Master::error(ACTHD_ACTIVITY_UNOPEN);
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        $hd_cfg['hdtype'] = $this->info['hdtype'];
        unset($hd_cfg['info']['no']);
        unset($hd_cfg['list']);
        unset($hd_cfg['fixed']);
        unset($hd_cfg['shop']);
        unset($hd_cfg['exchange']);
        foreach ($hd_cfg['total'] as $k => $v){
            if (empty($this->info[$v['id']])){
                $hd_cfg['total'][$k]['get'] = 0;
            }else{
                $hd_cfg['total'][$k]['get'] = 1;
            }
        }
        $hd_cfg['cons'] = $this->info['cons'];
        $this->outf = $hd_cfg;
        Master::back_data(0,$this->b_mol,$this->b_ctrl,$this->outf);
        //获取商城列表
        $shop = $this->back_data_shop();
        Master::back_data($this->uid,$this->b_mol,'shop',$shop);
        //兑换商城
        $exchange = $this->back_data_exchange();
        Master::back_data($this->uid,$this->b_mol,'exchange',$exchange);
    }

    public function back_data_hd(){
        self::data_out();
    }

}

