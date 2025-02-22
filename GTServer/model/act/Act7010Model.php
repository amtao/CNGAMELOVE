<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动7010 新人团购
 */
class Act7010Model extends ActHDBaseModel
{
    public $atype = 7010;//活动编号
    public $comment = "新人团购";
    public $b_mol = "newPeopleBuy";//返回信息 所在模块
    public $b_ctrl = "buyinfo";//子类配置
    public $hd_id = 'huodong_7010';//活动配置文件关键字

    /*
     * 初始化结构体
     * 累计数量
     * 领奖档次
     */
    public $_init =  array(
        'rechargePeople'    => 0,  //充值人数
        'myPayMoney'        => 0, //我的充值金额
        'pickProgress'      => array(), //领取进度
    );

    /**
     * 新人团购--领取奖励
     * @param int $id
     */
    public function pickRwd($id){
        if( self::get_state() == 0 ){
            Master::error(ACTHD_ACTIVITY_UNOPEN);
        }
        foreach($this->hd_cfg['rwd'] as $v){
            if($v['id'] == $id){
                if($this->info['rechargePeople'] < $v['people'] || $this->info['myPayMoney'] < $v['need']){
                    Master::error(ACT7010_NOT_REACH_GOAL);
                }
                if(!empty($this->info['pickProgress'][$id]) && $this->info['pickProgress'][$id] == 1){
                    Master::error(REWARD_IS_GET);
                }
                Master::add_item3($v['items']);
            break;
            }
        }
        $this->info['pickProgress'][$id] = 1;
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
            //满足领奖条件
            foreach ($this->hd_cfg['rwd'] as $k => $v){
                if (!isset($this->info['pickProgress'][$v['id']])) {
                    if ($this->info['rechargePeople'] >= $v['people'] && $this->info['myPayMoney'] >= $v['need']) {
                        return 1;
                    }
                }
            }
        }
        return $news;
    }

    public function setPayMoney($diamond){
        $this->info['myPayMoney'] += $diamond;
        $this->save();
    }

    /*
     * 构造输出结构体
     */
    public function data_out(){
        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
            // Master::error(ACTHD_ACTIVITY_UNOPEN);
            return;
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        $startTime = $hd_cfg['info']['sTime'];
        $endTime = $hd_cfg['info']['eTime'];

        unset($hd_cfg['info']['no']);

        Common::loadModel('OrderModel');
        $totlePeople =  OrderModel::getOrderCount($startTime,$endTime);
        $money = OrderModel::getMyPay($startTime,$endTime,$this->uid);
   		$this->info['rechargePeople'] = $totlePeople;
        $this->save();
        $progress = array('rechargePeople' => $totlePeople,'myPayMoney' => $this->info['myPayMoney']);

        $this->outf['cfg'] = $hd_cfg;
        $this->outf['cons'] = $progress;
        $this->outf['rwd'] = isset($this->info['pickProgress']) ? $this->info['pickProgress'] : array();
        Master::back_data(0,$this->b_mol,$this->b_ctrl,$this->outf);
    }

    public function back_data_hd(){
        self::data_out();
    }
}

