<?php
require_once "ActHDBaseModel.php";

/*
 * 活动292
 */
class Act292Model extends ActHDBaseModel
{
    public $atype = 292;//活动编号
    public $comment = "双旦-收集兑换";
    public $b_mol = "sdhuodong";//返回信息 所在模块
    public $b_ctrl = "souji";//子类配置
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_292';//活动配置文件关键字
    protected $_rank_id = 292;

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'buy' => array(), //档次 => 次数
        'cons' => 0, //铃铛个数
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
        $this->outf['cfg']['rwd'] = array();
        foreach($hd_cfg['rwd'] as $v){
            if($v['limit'] == 1){
                //已购买次数
                $buys = empty($this->info['buy'][$v['dc']])?0:$this->info['buy'][$v['dc']];
                //剩余购买次数
                $v['maxnum'] = $v['lnum'];
                $v['lnum'] = $v['lnum'] - $buys;
            }
            $this->outf['cfg']['rwd'][] = $v; //活动兑换信息
            unset($v);
        }
        $this->outf['cons'] = $this->info['cons'];
    }

    /*
     * 构造输出结构体
     * $dc : 购买的档次
     */
    public function buy($dc){
        $goods = array();
        //获取限购信息
        foreach($this->outf['cfg']['rwd'] as $value ){
            if($value['dc'] != $dc){
                continue;
            }
            $goods = $value;
        }
        if(empty($goods)){
            Master::error(PARAMS_ERROR.$dc);  //参数错误
        }

        //判断限购
        if($goods['limit'] == 1 && $goods['lnum'] <= 0){
            Master::error(CLUB_EXCHANGE_GOODS_MAX);
        }
        //扣除道具
        foreach ($goods['need'] as $v){
            Master::sub_item2($v);
        }
        //加礼包
        Master::add_item2($goods['items']);

        //加购买次数
        if(empty($this->info['buy'][$dc])){
            $this->info['buy'][$dc] = 0;
        }
        $this->info['buy'][$dc] += 1;
        $this->save();
    }

    /*
     * 返回活动信息--保存时不返回信息
     * 只返回当前活动在生效列表中对应的部分
     */
    public function back_data(){

    }

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取
        return $news;
    }

    /**
     * 活动道具产出
     * @param $type 类型
        1 => 每日任务 最后两个奖励概率掉落
        2 => 每日帮会建设一定获得活动道具
        3 => 每日赴宴每次都一定获得活动道具
        4 => 乱党每10关必获得活动道具（一键围剿和一键竞价有效）
        5 => 丝路每10关必获得活动道具（一键围剿和一键竞价有效）
        6 => (丢弃,不生效) => 衙门每成功翻牌子概率获得活动道具
        7 => 中午战场每五波必获得活动道具，其他波数概率获得活动道具
        8 => 成功击杀帮会BOSS必获得活动道具（击杀者）
        9 => 成功击杀世界BOSS必获得活动道具（击杀者）
     * @return int
     * @return $num int
     * @return $ret int  返回道具不在这函数提示
     */
    public function chanChu($type,$num = 0,$ret = 0){
        if( parent::get_state() != 1 ){
            return 0;  //未在活动区间内
        }
        $laiYuan = $this->hd_cfg['laiyuan'];
        if(empty($laiYuan[$type])){
            return 0;  //参数错误
        }
        //条件判断
        if(in_array($type,array(4,5))){
            //每10关必获得活动道具
            if($num % 10 != 0){
                return 0;  //未达到要求
            }
        }

        //条件判断
        if( $type == 7){
           //中午战场每五波必获得活动道具(写死)，其他波数概率获得活动道具(配置)
            if($num % 5 == 0){
                $laiYuan[$type]['prob_100'] = 100;
            }
        }


        if(rand(1,100) > $laiYuan[$type]['prob_100']){
            return 0;  //运气不好,没有掉落奖励
        }

        if($ret){
            return $laiYuan[$type];
        }
        //发放奖励
        Master::add_item($this->uid,$laiYuan[$type]['kind'],$laiYuan[$type]['id'],$laiYuan[$type]['count']);

    }

    /**
     * 资源消耗
     * @param $num
     */
    public function add($num){
        if( self::get_state() == 1 ){
            $this->info['cons'] += $num;
            $this->save();

            $data = array();
            $data['cons'] = $this->info['cons'];
            Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);
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
    }


}




