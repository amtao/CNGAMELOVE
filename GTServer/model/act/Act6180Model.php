<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6180
 */
class Act6180Model extends ActHDBaseModel
{
	public $atype = 6180;//活动编号
	public $comment = "直冲礼包";
    public $b_mol = "zchuodong";//返回信息 所在模块
	public $b_ctrl = "Gift";//子类配置
	public $hd_id = 'huodong_6180';//活动配置文件关键字

    /*
	 * 初始化结构体
	 * 累计数量
	 * 领奖档次
	 */
    public $_init =  array(
        'lastId' => 0,
        'statue' => 0, //0表示没有订单 或者订单完成 1 表示到账 2 表示先扣次数
        'isClick' => 0,
        'shop' => array(),
        'refreshTime' => 0,
        'addVip' => 0,
    );

    /*
	 * 直充购买记录
	 * */
    public function Buy($id, $tradeno)
    {
        if(empty($this->info['shop'][$id])){
            $this->info['shop'][$id] = 0;
        }
        $this->info['shop'][$id] += 1;
//        foreach ($this->hd_cfg['rwd'] as $key => $v) {
//            if ($id == $key){
//                $stime = $this->hd_cfg['info']['sTime'] + ($v['startDay']-1) * 86400;
//                $etime = $this->hd_cfg['info']['sTime'] + $v['endDay'] * 86400;
//                if ($_SERVER['REQUEST_TIME'] < $stime || Game::is_over($etime)){
//                    Master::error(BOITE_ATTEND_NUM_SHORT);
//                }
//                if ($v['islimit'] == 1 && $this->info[$id] >= $v['limit']) {
//                    Master::error(BOITE_ATTEND_NUM_SHORT);
//                }
//            }
//        }
        //先到帐
        if (empty($this->info['statue']) || $this->info['statue'] == 1){
            $this->info['lastId'] = $id;
            $this->info['statue'] = 1;
        }
        //后到账
        else if ($this->info['statue'] == 2){
            $this->info['lastId'] = 0;
            $this->info['statue'] = 0;
        }

        $itemInfo = $this->resItem($id);
        Common::loadModel('OrderModel');
        OrderModel::order_gift_bag($tradeno, $itemInfo["name"]);

        $this->save();
    }

    public function click(){
        $this->info['isClick'] = 1;
        $this->info['clickTime'] = Game::get_now();
        $this->_save();
    }

    
    public function initClick(){
        if(!Game::is_today($this->info['clickTime'])){
            $this->info['isClick'] = 0;
            $this->_save();
        }
    }

    public function setTempBuy($id){
        //先到帐
        if ($this->info['statue'] == 1 && $id == $this->info['lastId'] ){
            $id = 0;
            $this->info['lastId'] = 0;
            $this->info['statue'] = 0;
            $this->_save();
        }
        //先扣次数
        else if (empty($this->info['statue'])){
            $this->info['statue'] = 2;
            $this->_save();
        }
        $outf = $this->get_outf($id);
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$outf);
    }

    /*
	 * 购买的礼包
	 * */
    public function resItem($id)
    {
        $items = array();
        if (!empty($this->hd_cfg['rwd'][$id])){
            $items = $this->hd_cfg['rwd'][$id];
        }
        return $items;
    }

    /*
     * 构造输出结构体
     */
    public function get_outf($buyId = 0){
        $outof = array();
        $isToday = false;
        if(Game::is_today($this->info['refreshTime'])){
            $isToday = true;        
        }
        if(!empty($this->hd_cfg['rwd'])) {
            foreach ($this->hd_cfg['rwd'] as $id => $v) {

                // if (isset($v["actid"]) && $v["actid"] != $this->atype) {
                //     continue;
                // }

                $stime = empty($v['startDay'])?strtotime($v['startTime']):$this->hd_cfg['info']['sTime'] + ($v['startDay']-1) * 86400;
                $etime = empty($v['endDay'])?strtotime($v['endTime']):$this->hd_cfg['info']['sTime'] + $v['endDay'] * 86400;
                if ($_SERVER['REQUEST_TIME'] < $stime || Game::is_over($etime)){
                    continue;
                }
                $value = $v;
                $value['end'] = $etime;
                $value['id'] = $id;
                //是否限购
                // if ($v['islimit'] == 1) {
                //     $c = empty($this->info['shop'][$id])?0:$this->info['shop'][$id];
                //     $value['limit'] = $v['limit'] - $c - ($id == $buyId?1:0);
                //     $value['limit'] = $value['limit'] < 0?0:$value['limit'];
                // } else {
                //     $value['limit'] = 0;
                // }
                if($isToday == false && $v['acttype'] == 1){
                    if(!empty($this->info['shop'][$id])){
                        $this->info['shop'][$id] = 0;
                    }
                    $this->info['refreshTime'] = Game::get_now();
                }
                $outof['cfg'][] = $value;
            }
        }
        $this->save();
        $outof['clickInfo'] = isset($this->info['isClick']) ? $this->info['isClick'] : 0;
        $outof['shop'] = isset($this->info['shop']) ? $this->info['shop'] : array();

        //默认输出直接等于内部存储数据
        return $outof;
    }

    public function back_data(){
        $outf = $this->get_outf();
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$outf);
    }

    public function getState(){
        $state = 0;  //活动未进行
        if(!empty($this->hd_cfg)){
            $state = 1;
        }
        return $state;
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
            if(!empty($this->hd_cfg['rwd'])){
                foreach ($this->hd_cfg['rwd'] as $v){
                    $end = strtotime($v['end']);
                    if ($end > Game::get_now()){
                        $news = 1;
                    }
                }
            }
        }
        return $news;
    }
}

