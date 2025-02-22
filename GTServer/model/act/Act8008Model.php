<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动8008
 */
class Act8008Model extends ActHDBaseModel
{
    public $atype = 8008;//活动编号
    public $comment = "新春活动";
    public $b_mol = "xinchun";//返回信息 所在模块
    public $b_ctrl = "xinchungame";//子类配置
    public $hd_id = 'huodong_8008';//活动配置文件关键字

    public $map = array(
        0 => array("n" => 1),
        1 => array("n" => 2),
        2 => array("n" => 3),
        3 => array("n" => 4),
        4 => array("n" => 5),
        5 => array("n" => 6),
        6 => array("n" => 7, 'l' => 21),
        7 => array("n" => 8),
        8 => array("n" => 9),
        9 => array("n" => 10),
        10 => array("n" => 11),
        11 => array("n" => 12, 'l' => 26),
        12 => array("n" => 13),
        13 => array("n" => 14),
        14 => array("n" => 15),
        15 => array("n" => 16),
        16 => array("n" => 17, 'l' => 17),
        17 => array("n" => 18),
        18 => array("n" => 19),
        19 => array("n" => 20),
        20 => array("n" => 31),
        21 => array("n" => 22),
        22 => array("n" => 23),
        23 => array("n" => 24, 'l' => 29),
        24 => array("n" => 25),
        25 => array("n" => 16),
        26 => array("n" => 27),
        27 => array("n" => 28),
        28 => array("n" => 29),
        29 => array("n" => 30),
        30 => array("n" => 31)
    );

    /*
     * 初始化结构体
     * 累计数量
     * 领奖档次
     */
    public $_init =  array(
        'move'      => array(),  //移动数据
        'qizi'      => array("Q1" => 0, "Q2" => 0, "Q3" => 0, "Q4" => 0),  //旗子数据
        'dianshu'   => array(),  //点数数据
        'play'      => array(),  //奖励
        'maxCons'      => array(),  //奖励
        'cons'      => array(),  //奖励
        'shake'      => array(),  //奖励
        'rank'      => 0,  //奖励
        'taozhuang'      => 0,  //奖励
        'get'      => array(),  //奖励
        'exchange'  => array(),  //兑换信息
    );

    /**
     * 新春活动
     * @param int $id
     */
    public function play(){
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

        if (count($this->info["dianshu"]) <= 0) {

            Master::sub_item($this->uid,KIND_ITEM,$this->hd_cfg['need'], 1);

            $this->info["dianshu"] = array();
            $dianshu = rand(1, 5);
            $this->info["dianshu"][] = $dianshu;

            //每日排行
            $Redis8008Model = Master::getRedis8008($this->_get_day_redis_id());
            $Redis8008Model->zIncrBy($this->uid, 1);

            //单次游戏消耗
            $this->info['cons'][$riqi] += 1;
            if ( !isset($this->info['maxCons'][$riqi]) || $this->info['maxCons'][$riqi] < $this->info['cons'][$riqi] ) {
                $this->info['maxCons'][$riqi] = $this->info['cons'][$riqi];
            }
        }else{

            if ( $this->info["dianshu"][count($this->info["dianshu"]) - 1] < 4 ) {
                Master::error(XINCHUN_MOVE_MAX_ERROR);
            }

            $dianshu = rand(1, 5);
            if (count($this->info["dianshu"]) == 3) {
                $dianshu = rand(1, 3);
            }
            $this->info["dianshu"][] = $dianshu;

            //连续3次额外摇点
            if (count($this->info["dianshu"]) > $this->info['shake'][$riqi]) {
                $this->info['shake'][$riqi] = count($this->info["dianshu"]) - 1;
            }
        }

        $this->save();
    }

    /**
     * 新春活动
     * @param int $id
     */
    public function move($qizi){
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

        if ( count($this->info["dianshu"]) <= 0 ) {
            Master::error(XINCHUN_MOVE_LIMIT_ERROR);
        }

        if (!isset($this->info["qizi"][$qizi])) {
            Master::error(XINCHUN_QIZI_ERROR);
        }

        if ($this->info["qizi"][$qizi] == 31) {
            Master::error(XINCHUN_QIZI_END);
        }

        $this->info["move"] = array();
        $dianshu = array_shift($this->info["dianshu"]);

        // 处于同一位置的棋子
        $qiziList = array($qizi);
        $this->info["move"][$qizi] = array($this->info["qizi"][$qizi]);
        foreach ($this->info["qizi"] as $k => $v) {

            if ($k == $qizi) {
                continue;
            }

            if ($v == $this->info["qizi"][$qizi] && $v != 0) {
                $qiziList[] = $v;
                $this->info["move"][$k] = array($v);
            }
        }

        for ($i=0; $i <= $dianshu; $i++) {

            if ($i == 0 && $this->info["qizi"][$qizi] != 0) {
                continue;
            }

            $pIndex = $this->map[$this->info["qizi"][$qizi]]['n'];
            if ( isset($this->map[$this->info["qizi"][$qizi]]['l']) && $i == 1 ) {
                $pIndex = $this->map[$this->info["qizi"][$qizi]]['l'];
            }

            foreach ($this->info["move"] as $k => $v) {

                $this->info["qizi"][$k] = $pIndex;
                $this->info["move"][$k][] = $this->info["qizi"][$k];
            }

            if ($pIndex == 31) {
                break;
            }
        }

        // 普通
        $rwdStr = "common";
        $rwdStrs = "commonlist";
        $rate = count($qiziList);

        // 角点
        if ( isset($this->map[$this->info["qizi"][$qizi]]['l']) ) {

            $rwdStr = "corner";
            $rwdStrs = "cornerlist";
        }

        // 终点
        if ( $this->info["qizi"][$qizi] == 31 ) {

            $rwdStr = "end";
            $rwdStrs = "endlist";
        }

        if ($this->info["qizi"]["Q1"] == 31 && $this->info["qizi"]["Q2"] == 31 && $this->info["qizi"]["Q3"] == 31 && $this->info["qizi"]["Q4"] == 31) {

            //单日完成游戏次数
            $this->info['play'][$riqi] += 1;
            $this->info['cons'][$riqi] = 0;

            $this->info["qizi"] = $this->_init["qizi"];
            $this->info["dianshu"] = $this->_init["dianshu"];

            //每日排行
            $Redis8008Model = Master::getRedis8008($this->_get_day_redis_id());
            $Redis8008Model->zIncrBy($this->uid, 30);

            $rwdStr = "finish";
            $rwdStrs = "finishlist";
            $rate = 1;
        }

        // 随机奖励
        $items = array();
        $list = $this->hd_cfg[$rwdStrs];
        $probMax = 0;
        foreach ($list as $key => $value) {
            $probMax += $value["prob"];
        }
        $rid =  Game::get_rand_key($probMax,$list,'prob');
        $items[$rid] = array('id'=>$list[$rid]['id'],'kind'=>$list[$rid]['kind'],'count'=>$list[$rid]['count'] * $rate);

        foreach ($this->hd_cfg[$rwdStr] as $k => $v) {
            $v["count"] *= $rate;
            $items[] = $v;
        }

        if (empty($items)){
            Master::error(ITEMS_ERROR);
        }
        // 领取奖励
        Master::add_item3($items);

        $this->save();

        //数据返回
        Master::back_data($this->uid, $this->b_mol, "rwdData", $items);
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
            case 1: //单日完成游戏次数
                if (isset($this->info['play'][$riqi]) && $this->info['play'][$riqi] >= $itmes["num"] ) {
                    $isOk = true;
                }
                break;
            case 2: //单次游戏消耗

                if (isset($this->info['maxCons'][$riqi]) && $this->info['maxCons'][$riqi] >= $itmes["num"] ) {
                    $isOk = true;
                }
                break;
            case 3: //连续3次额外摇点

                if (isset($this->info['shake'][$riqi]) && $this->info['shake'][$riqi] >= $itmes["num"] ) {
                    $isOk = true;
                }
                break;
            case 4: //连续2天排行榜第一

                if ($this->info['rank'] > 0) {
                    $isOk = true;
                }
                break;
            case 5: //集齐任意一套春节套装

                if ($this->info['taozhuang'] > 0) {
                    $isOk = true;
                }
                break;
        }
        //一次性
        if ($itmes["type"] == 4 || $itmes["type"] == 5) {
            if ( !empty($this->info["get"][$id]) || !$isOk ){
                Master::error(ACTHD_NO_RECEIVE);
            }
            $this->info["get"][$id] = 1;
        }else{
             //每日
            if ( !empty($this->info["get"][$riqi][$id]) || !$isOk ){
                Master::error(ACTHD_NO_RECEIVE);
            }
            $this->info["get"][$riqi][$id] = 1;
        }
        //领取奖励
        Master::add_item3($itmes['items']);
        
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
            $play = isset($this->info['play'][$riqi]) ? $this->info['play'][$riqi] : 0;
            $maxCons = isset($this->info['maxCons'][$riqi]) ? $this->info['maxCons'][$riqi] : 0;
            $shake = isset($this->info['shake'][$riqi]) ? $this->info['shake'][$riqi] : 0;
            $rwds = $this->hd_cfg['rwd'];

            foreach ($rwds as $k => $v) {

                $isOk = false;
                switch ($v["type"]) {
                    case 1: //单日完成游戏次数
                        if ($play >= $v["num"] ) {
                            $isOk = true;
                        }
                        break;
                    case 2: //单次游戏消耗

                        if ($maxCons >= $v["num"] ) {
                            $isOk = true;
                        }
                        break;
                    case 3: //连续3次额外摇点

                        if ($shake >= $v["num"] ) {
                            $isOk = true;
                        }
                        break;
                    case 4: //连续2天排行榜第一

                        if ($this->info['rank'] < 1) {

                            $rankId1 = $this->hd_cfg['info']['id'].'_'.Game::get_today_long_id(-1);
                            $Redis8008Model1 = Master::getRedis8008($rankId1);
                            $rid1 = $Redis8008Model1->get_rank_id($this->uid);

                            $rankId2 = $this->hd_cfg['info']['id'].'_'.Game::get_today_long_id(-2);
                            $Redis8008Model2 = Master::getRedis8008($rankId2);
                            $rid2 = $Redis8008Model2->get_rank_id($this->uid);

                            if ($rid1 == 1 && $rid2 == 1 ) {

                                $isOk = true;
                                $this->info['rank'] = 1;
                                $this->save();
                            }
                        }else{
                            $isOk = true;
                        }
                        break;
                    case 5: //集齐任意一套春节套装

                        if ($this->info['taozhuang'] > 0 || self::check_taozhuang()) {

                            $isOk = true;
                        }
                        break;
                }

                if ($v["type"] == 4 || $v["type"] == 5) {

                    if ( empty($this->info["get"][$v["id"]]) && $isOk ){
                        $news = 1;
                    }
                }else{

                    if ( empty($this->info["get"][$riqi][$v["id"]]) && $isOk ){
                        $news = 1;
                    }
                }
            }
        }

        return $news;
    }

    public function check_taozhuang(){

        if ($this->info['taozhuang'] < 1) {

            $rwds = $this->hd_cfg['rwd'];
            $taozhuang = array();
            foreach ($rwds as $k => $v) {
                if ($v["type"] == 5) {
                    $taozhuang = isset($v["clothe"]) ? $v["clothe"] : array();
                    break;
                }
            }

            $isOkList = array(1, 1);
            $Act6140Model = Master::getAct6140($this->uid);
            $clothes = $Act6140Model->info['clothes'];

            if (is_array($clothes) && count($clothes) > 0) {

                foreach ($taozhuang as $tk => $tv) {

                    foreach ($tv as $tkk => $tvv) {

                        if (!in_array($tvv, $clothes)){
                            $isOkList[$tk] = 0;
                            break;
                        }
                    }
                }

                if ($isOkList[0] > 0 || $isOkList[1] > 0) {

                    $this->info['taozhuang'] = 1;
                    $this->save();
                }
            }
        }
        return $this->info['taozhuang'];
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

        $hd_cfg["move"] = $this->info["move"];
        $hd_cfg["qizi"] = $this->info["qizi"];
        $hd_cfg["dianshu"] = $this->info["dianshu"];
        $hd_cfg["play"] = isset($this->info['play'][$riqi]) ? $this->info['play'][$riqi] : 0;
        $hd_cfg["maxCons"] = isset($this->info['maxCons'][$riqi]) ? $this->info['maxCons'][$riqi] : 0;
        $hd_cfg["shake"] = isset($this->info['shake'][$riqi]) ? $this->info['shake'][$riqi] : 0;
        $hd_cfg["rank"] = $this->info["rank"];
        $hd_cfg["taozhuang"] = $this->info["taozhuang"];
        $this->outf = $hd_cfg;
        Master::back_data(0,$this->b_mol,$this->b_ctrl,$this->outf);
    }

    public function back_data_hd(){
        self::check_taozhuang();
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
        $Redis8008Model = Master::getRedis8008($rankId);
        $Redis8008Model->back_data();
        $Redis8008Model->back_data_my($this->uid);
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

