<?php
require_once "ActHDBaseModel.php";

/*
 * 许愿池
 */
class Act8003Model extends ActHDBaseModel
{
    public $atype = 8003;//活动编号
    public $comment = "许愿池";
    public $b_mol = "wishingWell";//返回信息 所在模块
    public $b_ctrl = "well";//子类配置
    public $hd_id = 'huodong_8003';//活动配置文件关键字-编号

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'cons'      => array(),  //许愿次数
        'get'       => array(),  //领奖状态
        'shop'      => array(),  //商城购买信息
        'exchange'  => array(),  //兑换信息
    );

    /*
     * 构造输出结构体
     */
    public function make_out(){

        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
            return;
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        foreach ($hd_cfg['consRwd'] as $k => $v){

            $hd_cfg['consRwd'][$k]["isGet"] = 0;
            if (isset($this->info['get'][$v["id"]])){
                $hd_cfg['consRwd'][$k]["isGet"] = 1;
            }
        }

        $exchangeTime = $hd_cfg["exchangeTime"];
        $idList = $exchangeTime[0]["idList"];

        $sDay = strtotime(date('Ymd', $hd_cfg['info']['sTime']));
        $hd_cfg["exchangeStartTime"] = $sDay + ($exchangeTime[0]["startDay"] - 1) * 86400;
        $hd_cfg["exchangeEndTime"] = $sDay + ($exchangeTime[0]["endDay"] - 1) * 86400 + 86399;
        $hd_cfg["exchangeTitle"] = strtotime($exchangeTime[0]["title"]);
        foreach ($exchangeTime as $key => $value) {

            $sTime = $sDay + ($value["startDay"] - 1) * 86400;
            $eTime = $sDay + ($value["endDay"] - 1) * 86400 + 86399;
            if ($_SERVER['REQUEST_TIME'] >= $sTime && $_SERVER['REQUEST_TIME'] <= $eTime) {
                $idList = $value["idList"];
                $hd_cfg["exchangeStartTime"] = $sTime;
                $hd_cfg["exchangeEndTime"] = $eTime;
                $hd_cfg["exchangeTitle"] = $value["title"];
                break;
            }
        }

        $Sev8003Model = Master::getSev8003($this->hd_cfg['info']['id']);
        $hd_cfg['allCons'] = $Sev8003Model->getCount();
        $hd_cfg['cons'] = isset($this->info['cons']["count"]) ? $this->info['cons']["count"] : 0;

        unset($hd_cfg['exchangeTime']);
        unset($hd_cfg['info']['no']);
        unset($hd_cfg['shop']);
        unset($hd_cfg['exchange']);
        $this->outf = $hd_cfg;
    }

    /*
     * 摇骰子
     * id  道具id
     * num 道具数量
     * */
    public function play($num=1){

        //活动已结束
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //活动结算阶段
        if($this->get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }
        //次数限制
        if (!in_array($num,array(1,10))){
            Master::error(PARAMS_ERROR);
        }

        $consItemId = intval($this->hd_cfg['need']);
        $consItemNum = intval($this->hd_cfg['needNum']) * $num;

        //减去使用的道具
        Master::sub_item($this->uid,KIND_ITEM,$consItemId,$consItemNum);

        $list  = $this->hd_cfg['list'];
        $bigRwd  = $this->hd_cfg['bigRwd'];
        $bigRwdNum  = $this->hd_cfg['bigRwdNum'];
        $cons = 0;
        if (isset($this->info['cons']["count"]) && $this->info['cons']["count"] > 0) {
            $cons = $this->info['cons']["count"];
        }

        $isBig = false;
        for ($i = 0;$i < $num;$i++){

            $cons++;
            $rwdInfo = array();
            if ($cons > 0 && ($cons % $bigRwdNum) == 0) {

                $bigRwd["isBig"] = 1;
                $rwdInfo = $bigRwd;
                $isBig = true;
            }else{

                $key = Game::get_rand_key1($list,'prob');
                $list[$key]["isBig"] = 0;
                $rwdInfo = $list[$key];
            }
            $allrwd[] = $rwdInfo;
        }
        $this->info['cons']["count"] = $cons;
        //领取奖励
        Master::add_item3($allrwd);

        //排行榜
        $Redis8003Model = Master::getRedis8003($this->hd_cfg['info']['id']);
        $Redis8003Model->zIncrBy($this->uid,$num);

        // $Act40Model = Master::getAct40($this->uid);
        // $Act40Model->chongbang_club($this->hd_id,$this->hd_cfg['info']['id'], $Redis8003Model->zScore($this->uid));

        $Sev8003Model = Master::getSev8003($this->hd_cfg['info']['id']);
        $Sev8003Model->add($num);

        if ($isBig) {
            $Sev94Model = Master::getSev94($this->hd_cfg['info']['id'], Game::get_sevid($this->uid));
            $Sev94Model->add_msg($this->uid, $bigRwd["id"], $bigRwd["count"]);
        }

        $this->save();

        //数据返回
        Master::back_data($this->uid,$this->b_mol,"rwdData",$allrwd);
    }

    /*
     * 商城 - 添加
     *
     * */
    public function add($id,$num = 1)
    {
        if(!is_int($num)){
            Master::error(ACT_HD_ADD_SCORE_NO_INT);
        }
        $ymd = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        $this->info['shop'][$id][$ymd] +=$num;
        $this->save();
    }

    /*
     * 许愿池-领取奖励
     *
     * */
    public function get_hrwd($id)
    {
        //活动已结束
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        if (isset($this->info['get'][$id])){
            Master::error(WISHING_WELL_YILING);
        }

        $Sev8003Model = Master::getSev8003($this->hd_cfg['info']['id']);
        $allCons = $Sev8003Model->getCount();
        $cons = 0;
        $consRwdList = $this->hd_cfg['consRwd'];
        if (isset($this->info['cons']["count"]) && $this->info['cons']["count"] > 0) {
            $cons = $this->info['cons']["count"];
        }

        $consRwdInfo = array();
        foreach ($consRwdList as $key => $value) {

            if ( $value["id"] == $id) {
                $consRwdInfo = $value;
                break;
            }
        }

        if (empty($consRwdInfo)) {
            Master::error(PARAMS_ERROR);
        }

        if ( $cons < $consRwdInfo["cons"]["user"] || $allCons < $consRwdInfo["cons"]["all"] ) {
            Master::error(WISHING_WELL_COUNT_LIMIT);
        }

        $this->info['get'][$id] = 1;

        //领取奖励
        Master::add_item3($consRwdInfo["items"]);
        $this->save();
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

            $Sev8003Model = Master::getSev8003($this->hd_cfg['info']['id']);
            $allCons = $Sev8003Model->getCount();
            $cons = isset($this->info['cons']["count"]) ? $this->info['cons']["count"] : 0;

            //满足领奖条件
            foreach ($this->hd_cfg['consRwd'] as $k => $v){

                $hid = $v['id'];
                $vCons = $v['cons'];
                if (!isset($this->info['get'][$hid])) {

                    if ($vCons["user"] <= $cons && $vCons["all"] <= $allCons) {
                        return 1;
                    }
                }
            }
        }
        return $news;
    }

    /*
     * 排行榜 和奖励
     * */
    public function paihang(){
        //个人排行榜
        $Redis8003Model = Master::getRedis8003($this->hd_cfg['info']['id']);
        $Redis8003Model->back_data();
        $Redis8003Model->back_data_my($this->uid);

        $clubRedisKey = $this->hd_id."_".$this->hd_cfg['info']['id'];
        $Redis150Model = Master::getRedis150($clubRedisKey);
        $Redis150Model->back_data();
        $Redis150Model->back_data_my($this->uid);
        $Redis150Model->back_data_club($this->uid, $this->hd_id, $this->hd_cfg['info']['id']);
    }

    /**
     * 关卡分数排行保存
     * @param $cid  玩家id
     * @param $num  通过的关卡数
     */
    public function out_club($uid,$cId){

        if( parent::get_state() == 1){

            $Act40Model = Master::getAct40($uid);
            $Act40Model->chongbang_club($this->hd_id,$this->hd_cfg['info']['id'], 0, $cId, true);
        }
    }

    /**
     * 兑换
     * $id
     */
    public function exchange($id = 0){
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        $buy_count = floor($id / 10000);
        if ($buy_count <= 0)return;
        $id = $id % 10000;
        if ($buy_count == 0)Master::error();
        $exchangeList = $this->hd_cfg['exchange'];

        foreach($exchangeList as $rwd){
            if ($rwd['id'] == $id){
                $c = empty($this->info['exchange'][$id])?0:$this->info['exchange'][$id];
                if ($c + $buy_count > $rwd['count'] && $rwd['count'] != 0){
                    Master::error();
                }
                $item = $rwd['items'][0];
                $ItemModel = Master::getItem($this->uid);
                $ItemModel->sub_item($item['id'], $item['count'] * $buy_count);
                $this->info['exchange'][$id] = $c + $buy_count;
                $item = $rwd['items'][1];
                $item['count'] = $item['count'] * $buy_count;
                Master::add_item2($item);
                $this->save();
                break;
            }
        }
        $exchange = $this->back_data_exchange();
        Master::back_data($this->uid,$this->b_mol,'exchange',$exchange);
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


        $sDay = strtotime(date('Ymd', $hd_cfg['info']['sTime']));
        foreach ($exchangeTime as $key => $value) {

            $sTime = $sDay + $value["startDay"] * 86400;
            $eTime = $sDay + $value["endDay"] * 86400 + 86399;
            if ($_SERVER['REQUEST_TIME'] >= $sTime && $_SERVER['REQUEST_TIME'] <= $eTime) {
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

    /**
     * 输出获奖情况-历史消息
     * $uid : 玩家id
     * $id : 第几个
     */
    public function out_log_history($uid,$id = 0){
        $Sev8003Model = Master::getSev94($this->hd_cfg['info']['id'], Game::get_sevid($uid));
        $list = $Sev8003Model->get_outf();
        if ($id <= 0) {

            foreach ($list as $key => $value) {

                if (intval($key) > $id) {
                    $id = $key;
                }
            }
            $id += 1;
        }else{
            $id += 21;
        }

        $Sev8003Model->list_history($uid,$id);
    }
}
