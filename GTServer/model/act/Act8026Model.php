<?php
require_once "ActHDBaseModel.php";

/*
 * 活动8026
 */
class Act8026Model extends ActHDBaseModel
{
    public $atype = 8026;//活动编号
    public $comment = "海滩夺宝";
    public $b_mol = "beachloot";//返回信息 所在模块
    public $b_ctrl = "beachloothuodong";//子类配置
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_8026';//活动配置文件关键字

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'pet'       => 0,
        'inGame'    => array(),
        'score'    => array(),
        'pScore'    => array(),
        'get'       => array(),  //奖励
        'taskGet'       => array(),  //奖励
        'exchange'  => array(),  //兑换信息
    );

    /**
     * 选择宠物 
     * @param int $id
     */
    public function selectPid($id = 0){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        $Act6140Model = Master::getAct6140($this->uid);
        if(!$Act6140Model->isUnlock($id)){
            Master::error(CLOTHE_PET_ERROR);
        }

        if(!in_array($id, $this->hd_cfg['petList']) && $id != 0){
            Master::error(PRINCE_GET_REWARD_SELECT);
        }

        $this->info["pet"] = $id;
        $this->save();
    }

    /**
     * 海滩夺宝
     * @param int $id
     */
    public function play($type = 1, $score = 0, $isSkill = 0){
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

        $num = 1;
        $decItem = 1;
        $addScore = $score;
        if ($type == 10) {
            $num = 10;
            $decItem = 9;
            $addScore = $score * $num;
        }
        $addScore = intval($addScore);

        Master::sub_item($this->uid,KIND_ITEM,$this->hd_cfg['need'],$decItem);
        //随机奖励
        $items = array();
        // $count = $score >= 15 ? 15 : $score;
        $this->hd_cfg['fixed']['count'] *= $addScore;
        $items[] = $this->hd_cfg['fixed'];
        if (empty($items)){
            Master::error(ITEMS_ERROR);
        }

        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);
        $this->info['inGame'][$riqi] += $num;
        if (!isset($this->info['score'][$riqi])) {
            $this->info['score'][$riqi] = 0;
        }

        if ($this->info['score'][$riqi] < $addScore) {
            $this->info['score'][$riqi] = $addScore;
        }

        //每日排行
        $Redis8026Model = Master::getRedis8026($this->_get_day_redis_id());
        $Redis8026Model->zIncrBy($this->uid,$addScore);

        // $Act40Model = Master::getAct40($this->uid);
        if (!isset($this->info["changeDay"])) {

            $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
            $riqi = intval($riqi);
            $this->info["changeDay"] = $riqi;

            $allScore = 0;
            $startTime = strtotime("2020-08-01 00:00:01");
            for ($i = 0; $i < 300; $i++) { 

                $day = Game::is_ymd($startTime);
                $redisKey = $this->hd_cfg['info']['id'].'_'.$day;
                $Redis8026Model = Master::getRedis8026($redisKey);
                $allScore += $Redis8026Model->zScore($this->uid);

                if (intval($day) == $riqi) {
                    break;
                }
                $startTime += 86400;
            }

            // $Act40Model->chongbang_club($this->hd_id,$this->hd_cfg['info']['id'], $allScore);
        }else{

            // $Act40Model->chongbang_club($this->hd_id,$this->hd_cfg['info']['id'], $addScore, false, true);
        }

        // 检测任务完成情况
        foreach ($this->hd_cfg['taskRwd'] as $k => $v) {

            if (isset($v["pet"]) && $this->info["pet"] == $v["pet"]) {

                if (!isset($this->info['pScore'][$riqi])) {
                    $this->info['pScore'][$riqi][$v["pet"]] = 0;
                }

                if ($v["type"] == 3) {
                    if ( $isSkill == 0 ){
                        $this->info['pScore'][$riqi][$v["pet"]] = 1;
                    }
                }else{
                    if ($this->info['pScore'][$riqi][$v["pet"]] < $addScore) {
                        $this->info['pScore'][$riqi][$v["pet"]] = $addScore;
                    }
                }
            }
        }

        //领取奖励
        Master::add_item3($items);

        $this->save();
        //数据返回
        Master::back_data($this->uid,$this->b_mol,"rwdData",$items);
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

            $rwds = $this->hd_cfg['rwd'];
            $gameNum = isset($this->info['inGame'][$riqi]) ? $this->info['inGame'][$riqi] : 0;
            foreach ($rwds as $k => $v) {

                if ( $v["num"] <= $gameNum && empty($this->info["get"][$riqi][$v["id"]]) ) {
                    $news = 1;
                    break;
                }
            }

            $taskRwds = self::check_task_rwd();
            foreach ($taskRwds as $k => $v) {
                if ( $v["get"] == 1 ) {
                    $news = 1;
                    break;
                }
            }
        }

        return $news;
    }

    /**
     * 获得登录奖励
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
        $gameNum = isset($this->info['inGame'][$riqi]) ? $this->info['inGame'][$riqi] : 0;
        if ($itmes["num"] > $gameNum || !empty($this->info["get"][$riqi][$itmes["id"]])) {
            Master::error(ACTHD_NO_RECEIVE);
        }

        //领取奖励
        Master::add_item3($itmes['items']);

        $this->info["get"][$riqi][$itmes["id"]] = 1;
        $this->save();
    }

    /**
     * 获得登录奖励
     * @param int $id
     */
    public function get_task_rwd($id = 0){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        $itmes = array();
        $taskRwds = self::check_task_rwd();
        foreach ($taskRwds as $k => $v) {
            if ( $v["id"] == $id ) {
                $itmes = $v;
                break;
            }
        }
        if (empty($itmes)){
            Master::error(ACTHD_NO_REWARD);
        }

        if ( $itmes["get"] != 1 ){
            Master::error(ACTHD_NO_RECEIVE);
        }

        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);
        if ($itmes["type"] == 6 || $itmes["type"] == 7) {

            $this->info["taskGet"][$itmes["id"]] = 1;
        }else{

            $this->info["taskGet"][$riqi][$itmes["id"]] = 1;
        }

        //领取奖励
        Master::add_item3($itmes['items']);
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

        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);
        foreach ($hd_cfg['rwd'] as $k => $v){

            $hd_cfg['rwd'][$k]['get'] = 0;
            if (!empty($this->info["get"][$riqi][$v["id"]])) {

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
        unset($hd_cfg['taskRwd']);

        $hd_cfg["pet"] = $this->info["pet"];
        $hd_cfg["taskRwd"] = self::check_task_rwd();
        $hd_cfg["inGame"] = isset($this->info["inGame"][$riqi]) ? $this->info["inGame"][$riqi] : 0;
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
        $Redis8026Model = Master::getRedis8026($rankId);
        $Redis8026Model->back_data();
        $Redis8026Model->back_data_my($this->uid);

        //排行信息
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

    public function check_pet($type = 1){

        $petNum = 0;
        $allPet = count($this->hd_cfg['petList']) - 1;
        $Act6140Model = Master::getAct6140($this->uid);
        foreach ($this->hd_cfg['petList'] as $k => $pId) {
            if($Act6140Model->isUnlock($pId)){
                $petNum++;
            }
        }

        if ( $type == 1 && $petNum >= 1) {
            return true;
        }else if ( $type == 2 && $petNum >= $allPet ) {
            return true;
        }

        return false;
    }

    public function check_task_rwd(){

        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);
        $taskRwds = $this->hd_cfg['taskRwd'];
        $gameNum = isset($this->info['inGame'][$riqi]) ? $this->info['inGame'][$riqi] : 0;
        $score = isset($this->info['score'][$riqi]) ? $this->info['score'][$riqi] : 0;
        $pScore = isset($this->info['pScore'][$riqi]) ? $this->info['pScore'][$riqi] : array();

        foreach ($taskRwds as $k => $v) {

            $isOk = false;
            $taskRwds[$k]["get"] = 0;
            $taskRwds[$k]["progress"] = 0;
            switch ($v["type"]) {
                case 1: //单次分数达到XX分
                    if ($score >= $v["num"] ) {
                        $taskRwds[$k]["progress"] = $score;
                        $isOk = true;
                    }
                    break;
                case 2: //挑战次数达到XX次（10倍奖励算10次）
                    if ($gameNum >= $v["num"] ) {
                        $taskRwds[$k]["progress"] = $gameNum;
                        $isOk = true;
                    }
                    break;
                case 3: //使用恒小王在不使用护盾的情况下通关
                case 4: //使用诚小王单次分数达到
                case 5: //使用太子单次分数达到
                    if (isset($pScore[$v["pet"]]) && $pScore[$v["pet"]] >= $v["num"] ) {
                        $taskRwds[$k]["progress"] = $pScore[$v["pet"]];
                        $isOk = true;
                    }
                    break;
                case 6: //拥有1个皇子宠物
                    if (self::check_pet(1)) {
                        $taskRwds[$k]["progress"] = $v["num"];
                        $isOk = true;
                    }
                    break;
                case 7: //集齐3个皇子宠物
                    if (self::check_pet(2)) {
                        $taskRwds[$k]["progress"] = $v["num"];
                        $isOk = true;
                    }
                    break;
            }

            if ($v["type"] == 6 || $v["type"] == 7) {

                if ( $isOk ) $taskRwds[$k]["get"] = 1;
                if ( !empty($this->info["taskGet"][$v["id"]]) ) $taskRwds[$k]["get"] = 2;
            }else{

                if ( $isOk ) $taskRwds[$k]["get"] = 1;
                if ( !empty($this->info["taskGet"][$riqi][$v["id"]]) ) $taskRwds[$k]["get"] = 2;
            }
        }

        return $taskRwds;
    }
}
