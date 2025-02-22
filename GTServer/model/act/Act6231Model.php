<?php
require_once "ActHDBaseModel.php";

/*
 * 活动272
 */
class Act6231Model extends ActHDBaseModel
{
    public $atype = 6231;//活动编号
    public $comment = "抢糕点";
    public $b_mol = "gaodian";//返回信息 所在模块
    public $b_ctrl = "info";//子类配置
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_6231';//活动配置文件关键字


    /*
     * 初始化结构体
     */
    public $_init =  array(
        'rwd' => -1,
        'count' => 0,
        'max' => 0,
        'lastTime' => 0,
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

    public function resetTime(){

        $hd_cfg = $this->hd_cfg;
        $day_0 = Game::day_0();
        $time = Game::get_now();
        $curData = null;
        $change = false;
        foreach ($hd_cfg['times'] as $v){
            if ($day_0 + $v['need'] * 3600 <= $time){
                $curData = $v;
            }
        }

        if (!empty($curData) && $this->info['rwd'] != $curData['need']){
            $this->info['max'] = 0;
            $change = true;
        }

        if ($this->info['lastTime'] < $day_0){
            $this->info['lastTime'] = $time;
            $this->info['rwd'] = -1;
            $this->info['max'] = 0;
            $change = true;
        }

        if ($change) {
            $this->_save();
        }
    }

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $this->resetTime();
        $news = 0;
        if( parent::get_state() == 0){
            return $news;
        }
        $hd_cfg = $this->hd_cfg;
        $day_0 = Game::day_0();
        $time = Game::get_now();
        $curData = null;
        foreach ($hd_cfg['times'] as $v){
            if ($day_0 + $v['need'] * 3600 <= $time){
                $curData = $v;
            }
        }

        $Sev6231Model = Master::getSev6231($this->hd_cfg['info']['id']);
        $need = $curData['need'];

        if (!empty($curData) && $this->info['rwd'] < $curData['need'] &&
            $Sev6231Model->info['hits'][$need] < $curData['all']){
            $news = 1;
        }
        return $news;
    }

    /**
     * 获得奖励
     * $id 兑换的门客id
     */
    public function get_rwd($count){
        if( parent::get_state() == 0){
            Master::error(ACTHD_OVERDUE.__LINE__);
        }
        if( parent::get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }
        $hd_cfg = $this->hd_cfg;
        $day_0 = Game::day_0();
        $time = Game::get_now();
        $curData = null;
        foreach ($hd_cfg['times'] as $v){
            if ($day_0 + $v['need'] * 3600 <= $time){
                $curData = $v;
            }
        }

        if (!empty($curData)) {

            if ($this->info['rwd'] == $curData['need']) {

                if ($this->info["max"] >= count($hd_cfg["cons"])) {
                    Master::error(TANG_YUAN_JIN_RI_YI_QIANG);
                }

                $cons = $hd_cfg["cons"][$this->info["max"]];
            }else{

                $this->info['max'] = 0;
            }

            $this->info['rwd'] = $curData['need'];
            $item = $curData['items'][0];
            $getmin = $item['min'];
            $getmax = $item['max'];
            $min_ = 1;
            $max = $item['randnum'];
            $r = rand($min, $max);
            $clickNum = $item['clicknum'];
            $clickCount = $count*$clickNum;
            $r = $r + $clickCount;
            if ( $r <= $getmin){
                $r = $getmin;
            }
            if ( $r >= $getmax){
                $r = $getmax;
            }
            $Sev6231Model = Master::getSev6231($this->hd_cfg['info']['id']);
            $r = $Sev6231Model->hit($r);

            if ($r > 0){

                if ($cons > 0) {
                    Master::sub_item($this->uid,KIND_ITEM,1,$cons);
                }

                Master::add_item($this->uid, KIND_ITEM, $item['id'], $r);
                $this->info['count'] = $this->info['count'] + $r;
                $this->info["max"]++;

                $Redis6231Model = Master::getRedis6231($this->hd_cfg['info']['id']);
                $Redis6231Model->zAdd($this->uid, $this->info['count']);
                if ($r >= $item['max'] && $this->hd_cfg['info']['hdtype']!=2){
                    $Sev6012Model = Master::getSev6012();
                    $msg = "#gaodian#::".$r;
                    $Sev6012Model->add_msg($this->uid, $msg, 3);
                }

                // $Act40Model = Master::getAct40($this->uid);
                // $Act40Model->chongbang_club($this->hd_id,$this->hd_cfg['info']['id'], $this->info['count']);

                $this->save();
                $this->back_data_hd();
            }else {
                Master::error(TANG_YUAN_QIANG_WAN);
            }
        }else{
            Master::error(TANG_YUAN_WEI_KAI_QI);
        }
    }

    /*
     * 构造输出结构体
     */
    public function make_out(){
        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
            Master::error(GAME_LEVER_UNOPENED);
        }

        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];

        $exchangeTime = $hd_cfg["exchangeTime"];
        $idList = $exchangeTime[0]["idList"];
        $hd_cfg["exchangeStartTime"] = strtotime($exchangeTime[0]["startTime"]);
        $hd_cfg["exchangeEndTime"] = strtotime($exchangeTime[0]["endTime"]);
        $hd_cfg["exchangeTitle"] = $exchangeTime[0]["title"];
        foreach ($exchangeTime as $key => $value) {

            if ($_SERVER['REQUEST_TIME'] >= strtotime($value['startTime']) && $_SERVER['REQUEST_TIME'] <= strtotime($value['endTime'])) {
                $idList = $value["idList"];
                $hd_cfg["exchangeStartTime"] = strtotime($value["startTime"]);
                $hd_cfg["exchangeEndTime"] = strtotime($value["endTime"]);
                $hd_cfg["exchangeTitle"] = $value["title"];
                break;
            }
        }

        unset($hd_cfg['info']['no']);
        unset($hd_cfg['shop']);
        unset($hd_cfg['exchangeTime']);
        unset($hd_cfg['exchange']);

        $hd_cfg['info']['news'] = $this->get_news();
        $this->outf = $hd_cfg;

        Master::back_data($this->uid,$this->b_mol,'base', $this->getBase());
    }

    private function getBase(){
        $max = isset($this->info['max']) ? $this->info['max'] : 0;
        $d = array('rwd'=>$this->info['rwd'], 'count'=>$this->info['count'], 'max'=>$max);
        $Sev6231Model = Master::getSev6231($this->hd_cfg['info']['id']);
        $d['damage'] = array();
        foreach ($Sev6231Model->info['hits'] as $k => $v){
            $d['damage'][] = array('id'=>$k, 'count'=>$v);
        }
        return $d;
    }

    /*
     * 兑换列表
     * */
    public function back_data_exchange() {
        //构造输出
        if( self::get_state() == 0 ){
            Master::error(GAME_LEVER_UNOPENED);
        }
        $info = $this->info['exchange'];
        $exchange = $this->hd_cfg["exchange"];
        $exchangeTime = $this->hd_cfg["exchangeTime"];
        $idList = $exchangeTime[0]["idList"];
        foreach ($exchangeTime as $key => $value) {

            if ($_SERVER['REQUEST_TIME'] >= strtotime($value['startTime']) && $_SERVER['REQUEST_TIME'] <= strtotime($value['endTime'])) {
                $idList = $value["idList"];
                break;
            }
        }

        $list = array();
        foreach ($exchange as $key => $value) {

            if (in_array($value["id"], $idList)) {
                $value["isPay"] = 1;

                $list[$value["id"]] = $value;
            }
        }

        $giftBag = Game::getcfg('gift_bag');
        foreach ($giftBag as $key => $value) {
            if ($value["actid"] == $this->atype && in_array($value["id"], $idList) ) {

                $value["isPay"] = 2;
                $list[$value["id"]] = $value;
            }
        }

        $newExchange = array();
        foreach ($idList as $k => $v) {

            if (isset($list[$v])) {
                array_push($newExchange, $list[$v]);
            }
        }

        $rwds = array();
        foreach($newExchange as $rwd){
            $rwd['buy'] = empty($info[$rwd['id']])?0:$info[$rwd['id']];
            $rwds[] = $rwd;
        }
        return $rwds;
    }

    /*
     * 排行榜 和奖励
     * */
    public function paihang(){

        $Redis6231Model = Master::getRedis6231($this->hd_cfg['info']['id']);
        $Redis6231Model->back_data();
        $Redis6231Model->back_data_my($this->uid);//我的排名
    }


}
