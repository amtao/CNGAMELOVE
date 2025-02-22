<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动8009
 */
class Act8009Model extends ActHDBaseModel
{
    public $atype = 8009;//活动编号
    public $comment = "情人节活动";
    public $b_mol = "qingrenjie";//返回信息 所在模块
    public $b_ctrl = "qingrenjiehuodong";//子类配置
    public $hd_id = 'huodong_8009';//活动配置文件关键字

    /*
     * 初始化结构体
     * 累计数量
     * 领奖档次
     */
    public $_init =  array(
        'game'      => array(),  //每日游戏次数
        'score'       => array(),  //每日最高积分
        'get'      => array(),  //奖励
        'exchange'  => array(),  //兑换信息
    );

    /**
     * 情人节活动
     * @param int $id
     */
    public function play($num = 1, $score = 0){
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

        Master::sub_item($this->uid,KIND_ITEM,$this->hd_cfg['need'],$num);
        //随机奖励
        $items = array();
        $count = $score >= 15 ? 15 : $score;
        $this->hd_cfg['fixed']['count'] *= $count;
        $this->hd_cfg['fixed']['count'] *= $num;
        $items[] = $this->hd_cfg['fixed'];
        if (empty($items)){
            Master::error(ITEMS_ERROR);
        }

        //每日排行
        $Redis8009Model = Master::getRedis8009($this->_get_day_redis_id());
        $Redis8009Model->zIncrBy($this->uid,$num);

        //领取奖励
        Master::add_item3($items);

        $this->info['game'][$riqi] += $num;
        if ( !isset($this->info['score'][$riqi]) || $this->info['score'][$riqi] < $score ) {
            $this->info['score'][$riqi] = $score;
        }

        $this->save();

        //数据返回
        Master::back_data($this->uid,$this->b_mol,"rwdData",$items);
    }

    /**
     * 获得奖励
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

        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);

        $isOk = false;
        switch ($itmes["type"]) {
            case 1: //登录
                $isOk = true;
                break;
            case 2: //积分

                if (isset($this->info['score'][$riqi]) && $this->info['score'][$riqi] >= $itmes["num"] ) {
                    $isOk = true;
                }
                break;
            case 3: //单日游戏次数

                if (isset($this->info['game'][$riqi]) && $this->info['game'][$riqi] >= $itmes["num"] ) {
                    $isOk = true;
                }
                break;
        }

        if ( !empty($this->info["get"][$riqi][$id]) || !$isOk ){
            Master::error(ACTHD_NO_RECEIVE);
        }
        //领取奖励
        Master::add_item3($itmes['items']);
        $this->info["get"][$riqi][$id] = 1;
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

            $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
            $riqi = intval($riqi);
            $score = isset($this->info['score'][$riqi]) ? $this->info['score'][$riqi] : 0;
            $game = isset($this->info['game'][$riqi]) ? $this->info['game'][$riqi] : 0;
            $rwds = $this->hd_cfg['rwd'];

            foreach ($rwds as $k => $v) {

                $isOk = false;
                switch ($v["type"]) {
                    case 1: //登录
                        $isOk = true;
                        break;
                    case 2: //积分

                        if ($score >= $v["num"] ) {
                            $isOk = true;
                        }
                        break;
                    case 3: //单日游戏次数

                        if ($game >= $v["num"] ) {
                            $isOk = true;
                        }
                        break;
                }

                if ( empty($this->info["get"][$riqi][$v["id"]]) && $isOk ){
                    $news = 1;
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

        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);

        foreach ($hd_cfg['rwd'] as $k => $v){
            if (empty($this->info["get"][$riqi][$v['id']])){
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

        $hd_cfg["game"] = isset($this->info["game"][$riqi]) ? $this->info["game"][$riqi] : 0;
        $hd_cfg["score"] = isset($this->info["score"][$riqi]) ? $this->info["score"][$riqi] : 0;
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
        $Redis8009Model = Master::getRedis8009($rankId);
        $Redis8009Model->back_data();
        $Redis8009Model->back_data_my($this->uid);
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
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
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

        $giftBag = Game::getGiftBagCfg();
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

