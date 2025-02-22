<?php
require_once "ActHDBaseModel.php";

/*
 * 活动8022
 */
class Act8022Model extends ActHDBaseModel
{
    public $atype = 8022;//活动编号
    public $comment = "豆腐女孩活动";
    public $b_mol = "doufu";//返回信息 所在模块
    public $b_ctrl = "doufuhuodong";//子类配置
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_8022';//活动配置文件关键字

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'maxBuy'    => -1,
        'buy'       => array(),
        'num'       => array(),
        'max'       => 0,
        'get'       => array(),  //奖励
        'exchange'  => array(),  //兑换信息
    );

    /**
     * 获取用户购买的次数
     */
    public function getMaxBuy(){

        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);

        $maxBuy = 0;
        foreach ($this->info["buy"] as $key => $value) {

            if ($riqi == $key) {
                continue;
            }
            if ($this->info["num"][$key] > $this->hd_cfg["playNum"]) {
                $maxBuy += $value - ($this->info["num"][$key] - $this->hd_cfg["playNum"]);
            }else{
                $maxBuy += $value;
            }
        }
        return $maxBuy;
    }

    /**
     * 豆腐女孩活动
     */
    public function play($jump = 0){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        //活动结算阶段
        if($this->get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }

        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);

        $max = $this->info["max"];
        $num = isset($this->info["num"][$riqi]) ? $this->info["num"][$riqi] : 0;
        $buy = isset($this->info["buy"][$riqi]) ? $this->info["buy"][$riqi] : 0;
        $maxBuy = $this->getMaxBuy();

        if( $num >= ($maxBuy + $buy + $this->hd_cfg["playNum"]) ){
            Master::error(JINGYING_COUNT_LIMIT);
        }

        $this->info["num"][$riqi]++;
        if ( $this->info["num"][$riqi] > $this->hd_cfg["playNum"] ){
            $this->info["maxBuy"]--;
        }
        if ($jump >= 100) {
            $jump = 100;
        }

        if ($jump > $max) {
            $this->info['max'] = $jump;
        }

        //每日排行
        $Redis8022Model = Master::getRedis8022($this->_get_day_redis_id());
        $Redis8022Model->zIncrBy($this->uid, $jump);

        $this->save();
        if ($jump >= 10) {

            $this->hd_cfg['fixed']['count'] *= intval($jump / 10);
            $items[] = $this->hd_cfg['fixed'];
            if (empty($items)){
                Master::error(ITEMS_ERROR);
            }
            //领取奖励
            Master::add_item3($items);

            //数据返回
            Master::back_data($this->uid,$this->b_mol,"rwdData",$items);
        }
    }

    /**
     * 购买次数
     * @param int $id
     */
    public function recovery(){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        //活动结算阶段
        if($this->get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }

        if (empty($this->hd_cfg['need'])){
            Master::error(ITEMS_ERROR);
        }

        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);
        $buy = isset($this->info["buy"][$riqi]) ? $this->info["buy"][$riqi] : 0;

        if ($buy >= count($this->hd_cfg['need'])) {
            Master::error(SHOP_BUY_NUM_GT_MAX);
        }

        if ($buy < count($this->hd_cfg['need'])) {

            $diamond = $this->hd_cfg['need'][$buy];
        }else{
            $diamond = $this->hd_cfg['need'][count($this->hd_cfg['need']) - 1];
        }
        Master::sub_item($this->uid,KIND_ITEM,1,$diamond);

        if ($this->info["maxBuy"] < 0) {
            $this->info["maxBuy"] = 0;
        }
        $this->info["buy"][$riqi]++;
        $this->info["maxBuy"]++;
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

            $max = $this->info['max'];
            $rwds = $this->hd_cfg['rwd'];
            foreach ($rwds as $k => $v) {

                if ( empty($this->info["get"][$v["id"]]) && $max >= $v["need"] ){
                    $news = 1;
                }
            }
        }

        return $news;
    }

    /**
     * 获得积分奖励
     * @param int $id
     */
    public function get_rwd($id = 0){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        $rwds = Game::get_key2id($this->hd_cfg['rwd'],'id');
        $itmes = $rwds[$id];
        if (empty($itmes)){
            Master::error(ACTHD_NO_REWARD);
        }

        if ( !empty($this->info["get"][$id]) || $this->info['max'] < $itmes["need"] ){
            Master::error(ACTHD_NO_RECEIVE);
        }

        //领取奖励
        Master::add_item3($itmes['items']);
        $this->info["get"][$id] = 1;
        $this->save();
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

        foreach ($hd_cfg['rwd'] as $k => $v){

            if (empty($this->info["get"][$v['id']])){
                $hd_cfg['rwd'][$k]['get'] = 0;
            }else{
                $hd_cfg['rwd'][$k]['get'] = 1;
            }
        }

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
        unset($hd_cfg['exchangeTime']);
        unset($hd_cfg['shop']);
        unset($hd_cfg['exchange']);

        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);

        $hd_cfg["maxBuy"] = $this->getMaxBuy();
        $hd_cfg["max"] = $this->info["max"];
        $hd_cfg["num"] = isset($this->info["num"][$riqi]) ? $this->info["num"][$riqi] : 0;
        $hd_cfg["buy"] = isset($this->info["buy"][$riqi]) ? $this->info["buy"][$riqi] : 0;

        $hd_cfg["num"] -= $hd_cfg["maxBuy"];
        $this->outf = $hd_cfg;
        Master::back_data(0,$this->b_mol,$this->b_ctrl,$this->outf);
    }

    public function back_data_hd(){
        self::data_out();
    }

    /*
     * 排行榜 和奖励
     * */
    public function paihang($type){

        $rankId = $this->hd_cfg['info']['id'].'_'.Game::get_today_long_id();
        if ($type == 2) {
            $rankId = $this->hd_cfg['info']['id'].'_'.Game::get_today_long_id(-1);
        }

        //个人排行榜
        $Redis8022Model = Master::getRedis8022($rankId);
        $Redis8022Model->back_data();
        $Redis8022Model->back_data_my($this->uid);
    }

    /**
     * 兑换
     * $id
     */
    public function exchange($id = 0,$count){
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        // $buy_count = floor($id / 10000);
        // if ($buy_count <= 0)return;
        // $id = $id % 10000;
        // if ($buy_count == 0)Master::error();
        $buy_count = $count==0?1:$count;
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

}
