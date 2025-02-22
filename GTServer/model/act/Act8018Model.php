<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动8018
 */
class Act8018Model extends ActHDBaseModel
{
    public $atype = 8018;//活动编号
    public $comment = "三消";
    public $b_mol = "sanxiao";//返回信息 所在模块
    public $b_ctrl = "sanxiaohuodong";//子类配置
    public $hd_id = 'huodong_8018';//活动配置文件关键字

    /*
     * 初始化结构体
     * 累计数量
     * 领奖档次
     */
    public $_init =  array(
        'hId'           => 0,  //皇子ID
        'pId'           => 1,  //关卡ID
        'round'         => 0,  //回合数
        'blood'         => 0,  //血量
        'point'         => 0,  //体力
        'pointTime'     => 0,  //体力恢复时间
        'chess'         => array(),  //棋盘内容
        'score'         => 0,  //积分
        'hitList'         => array(),  //积分
        'get'           => array(),  //奖励
        'exchange'      => array(),  //兑换信息
    );

    /**
     * 更新钓鱼点
     * @param int $id
     */
    public function refreshPoint(){

        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        $max = $this->hd_cfg["maxPoint"];
        if ($this->info['point'] < $max) {
            $hf_num = Game::hf_num(
                $this->info['pointTime'],//上次恢复时间
                3600,//CD
                $this->info['point'],//上次次数
                $max//次数上限
            );

            $this->info['point'] = $hf_num["num"];
            $this->info['pointTime'] = $hf_num["stime"];
            if ($this->info['point'] >= $max) {
                $this->info['pointTime'] = 0;
            }

            $this->save();
        }
        return $this->info['point'];
    }

    /**
     * 获取当前关卡血量
     * @param int $id
     */
    public function getMaxBlood(){

        $addBlood = $this->hd_cfg["addBlood"];
        $baseBlood = $this->hd_cfg["baseBlood"] * ( 1 + $addBlood * ( $this->info["pId"] - 1 ) );

        return $baseBlood;
    }

    /**
     * 更新钓鱼点
     * @param int $id
     */
    public function getBlood($refresh = false){

        if ( ($this->info["blood"] <= 0 && $this->info["round"] == 0) || $refresh) {

            $this->info["blood"] = $this->getMaxBlood();
        }
    }

    /**
     * 重置
     * @param int $id
     */
    public function resetChess(){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        Master::sub_item($this->uid,KIND_ITEM,1,20);
    }

    /**
     * 开启下一关卡
     * @param int $id
     */
    public function startNext(){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        if($this->info['blood'] > 0){
            Master::error(SANXIAO_BOSS_NOT_DIE);
        }

        //关卡排行
        $Redis8018Model = Master::getRedis8018($this->hd_cfg['info']['id']);
        $Redis8018Model->zIncrBy($this->uid,1);

        $this->info["pId"]++;
        $this->info["round"] = 0;
        $this->getBlood(true);
        $this->save();
    }

    /**
     * 保存棋盘
     * @param int $id
     */
    public function saveChess($chess = array()){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        $this->info["chess"] = $chess;
        $this->save();
    }

    /**
     * 失败重置关卡
     * @param int $id
     */
    public function pveFail(){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        if($this->info['round'] < $this->hd_cfg["maxRound"] || $this->info['blood'] <= 0){
            Master::error(SANXIAO_BOSS_NOT_DIE);
        }

        $this->info["round"] = 0;
        $this->getBlood(true);
        $this->save();
    }

    /**
     * 使用体力药水
     * @param int $id
     */
    public function recovery($num = 1){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        if (empty($this->hd_cfg['need'])){
            Master::error(ITEMS_ERROR);
        }

        Master::sub_item($this->uid,KIND_ITEM,$this->hd_cfg['need'],$num);

        $point = $this->refreshPoint();
        $this->info["point"] = $point + $num * 5;
        if ($this->info["point"] >= $this->hd_cfg["maxPoint"]) {
            $this->info['pointTime'] = 0;
        }
        $this->save();
    }

    /**
     * 三消
     * @param int $color  1.红色  2.蓝色  3.橙色  4.紫色   5.绿色   6.白色
     * @param int $num  个数
     * @param int $combo  连击
     * 
     */
    public function play($list = array(), $combo = 1){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        //活动结算阶段
        if($this->get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }

        $point = $this->info["point"];
        if($point <= 0 && $combo == 1){
            Master::error(LOOK_FOR_POWER_SHORT);
        }

        $this->getBlood();
        if($this->info['blood'] <= 0 && $combo == 1){
            Master::error(union_killed);
        }

        if($this->info['round'] >= $this->hd_cfg["maxRound"] && $combo == 1){
            Master::error(TREASURE_LOST_TIP);
        }

        $heroLv = 0;
        $heroZz = 0;
        $ep1 = 0;
        $ep2 = 0;
        $ep3 = 0;
        $ep4 = 0;
        $TeamModel  = Master::getTeam($this->uid);
        if (isset($TeamModel->info["heros"][$this->hd_cfg['hero'][0]])) {

            $heros = $TeamModel->info["heros"][$this->hd_cfg['hero'][0]];
            $heroLv = $heros["level"];
            $heroZz = $heros["zz"]["e1"] + $heros["zz"]["e2"] + $heros["zz"]["e3"] + $heros["zz"]["e4"];
            $ep1 = $heros["aep"]["e1"];
            $ep2 = $heros["aep"]["e2"];
            $ep3 = $heros["aep"]["e3"];
            $ep4 = $heros["aep"]["e4"];
        }

        $allHit = 0;
        $this->info["hitList"] = array();
        foreach ($list as $k => $v) {

            $ep = 1;
            $hit = 100;
            $pows = pow(($v["num"] - 2), 3);
            switch ($v["color"]) {
                case 1:
                    if ($ep1 > 0) {
                        $ep += $ep1 / 100000;
                    }
                    // 红色棋子最终伤害=基础伤害100*（1+气势属性/100000）*（0.9+（消除数-2）^2*10%）*（1+COMBO数/10）
                    $hit = $hit * $ep * ( 0.9 + $pows * 0.1) * (1 + ($combo - 1) / 10);
                    break;
                case 2:
                    if ($ep2 > 0) {
                        $ep += $ep2 / 100000;
                    }
                    // 蓝色棋子最终伤害=基础伤害100*（1+智谋属性/100000）*（0.9+（消除数-2）^2*10%）*（1+COMBO数/10）
                    $hit = $hit * $ep * ( 0.9 + $pows * 0.1) * (1 + ($combo - 1) / 10);
                    break;
                case 3:
                    if ($ep3 > 0) {
                        $ep += $ep3 / 100000;
                    }
                    // 橙色棋子最终伤害=基础伤害100*（1+政略属性/100000）*（0.9+（消除数-2）^2*10%）*（1+COMBO数/10）
                    $hit = $hit * $ep * ( 0.9 + $pows * 0.1) * (1 + ($combo - 1) / 10);
                    break;
                case 4:
                    if ($ep4 > 0) {
                        $ep += $ep4 / 100000;
                    }
                    // 紫色棋子最终伤害=基础伤害100*（1+魅力属性/100000）*（0.9+（消除数-2）^2*10%）*（1+COMBO数/10）
                    $hit = $hit * $ep * ( 0.9 + $pows * 0.1) * (1 + ($combo - 1) / 10);
                    break;
                case 5:
                    if ($heroLv > 0) {
                        $ep += $heroLv / 1000;
                    }
                    // 绿色棋子最终伤害=基础伤害100*（1+等级/1000）*（0.9+（消除数-2）^2*10%）*（1+COMBO数/10）
                    $hit = $hit * $ep * ( 0.9 + $pows * 0.1) * (1 + ($combo - 1) / 10);
                    break;
                case 6:
                    if ($heroZz > 0) {
                        $ep += $heroZz / 5000;
                    }
                    // 白色棋子最终伤害=基础伤害100*（1+资质/5000）*（0.9+（消除数-2）^2*10%）*（1+COMBO数/10）
                    $hit = $hit * $ep * ( 0.9 + $pows * 0.1) * (1 + ($combo - 1) / 10);
                    break;
            }
            $allHit += ceil($hit);
            $this->info["hitList"][] = array("color" => $v["color"], "hit" => ceil($hit));
        }

        $this->info["blood"] -= intval($allHit);
        $this->info["blood"] = intval($this->info["blood"]);

        if ($combo == 1) {
            $this->info['point'] = $point - 1;
            $this->info["round"]++;

            //每日排行
            $Redis8018Model = Master::getRedis8018($this->_get_day_redis_id());
            $Redis8018Model->zIncrBy($this->uid, 1);
        }
        if ($this->info['pointTime'] == 0 && $this->info['point'] < $this->hd_cfg["maxPoint"]) {
            $this->info['pointTime'] = time();
        }

        if ($this->info["blood"] <= 0) {
            //领取奖励
            Master::add_item3($this->hd_cfg['fixed']);
        }

        $this->info['score'] += 1;
        $this->save();
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

        if ( !empty($this->info["get"][$id]) || $this->info['pId'] < $itmes["need"] ){
            Master::error(ACTHD_NO_RECEIVE);
        }

        //领取奖励
        Master::add_item3($itmes['items']);
        $this->info["get"][$id] = 1;
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

            $pId = $this->info['pId'];
            $rwds = $this->hd_cfg['rwd'];
            foreach ($rwds as $k => $v) {

                if ( empty($this->info["get"][$v["id"]]) && $pId >= $v["need"] ){
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
        unset($hd_cfg['maxRound']);
        unset($hd_cfg['baseBlood']);
        unset($hd_cfg['addBlood']);

        $this->getBlood();
        $this->refreshPoint();
        $hd_cfg["maxBlood"] = $this->getMaxBlood();
        $hd_cfg["cdTime"] = 3600;
        $hd_cfg["maxPoint"] = $this->hd_cfg["maxPoint"];
        $hd_cfg["maxRound"] = $this->hd_cfg["maxRound"];
        $hd_cfg["score"] = isset($this->info["score"]) ? $this->info["score"] : 0;
        $hd_cfg["pId"] = isset($this->info["pId"]) ? $this->info["pId"] : 0;
        $hd_cfg["round"] = isset($this->info["round"]) ? $this->info["round"] : 0;
        $hd_cfg["blood"] = isset($this->info["blood"]) ? $this->info["blood"] : 0;
        $hd_cfg["point"] = isset($this->info["point"]) ? $this->info["point"] : 0;
        $hd_cfg["pointTime"] = isset($this->info["pointTime"]) ? $this->info["pointTime"] : 0;
        $hd_cfg["chess"] = isset($this->info["chess"]) ? $this->info["chess"] : array();
        $hd_cfg["get"] = isset($this->info["get"]) ? $this->info["get"] : array();
        $hd_cfg["hitList"] = isset($this->info["hitList"]) ? $this->info["hitList"] : array();
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
        $Redis8018Model = Master::getRedis8018($rankId);
        $Redis8018Model->back_data();
        $Redis8018Model->back_data_my($this->uid);
    }

    /*
     * 排行榜 和奖励
     * */
    public function pvePaihang(){

        //关卡排行榜
        $Redis8018Model = Master::getRedis8018($this->hd_cfg['info']['id']);
        $Redis8018Model->back_data();
        $Redis8018Model->back_data_my($this->uid);
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
        $buy_count = $count == 0?1:$count;
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

