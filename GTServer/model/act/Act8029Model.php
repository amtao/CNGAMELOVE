<?php
require_once "ActHDBaseModel.php";

/*
 * 打月亮
 */
class Act8029Model extends ActHDBaseModel
{
	public $atype = 8029;//活动编号
	public $comment = "打月亮";
	public $b_mol = "playmoon";//返回信息 所在模块
	public $b_ctrl = "playmoonhuodong";//返回信息 所在控制器
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_8029';//活动配置文件关键字

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
        'date' => 0,    // 日期
        'isOpen' => 0,    // 是否开启月亮
        'hit' => 0,    // 伤害
        'hitNum' => 0,    // 攻击次数
        'freeTimes' => 0,    // 免费开局次数
        'buyShell' => 0, //  购买炮弹
        'buyNum' => 0, //  购买次数
        'openNum' => 0, //  开启次数
        'moonNums' => 0,    // 击杀月亮数
        'friendShell'       => array(),  //好友赠送
        'friendGet'       => 0,  //领取炮弹次数
        'sendNum'       => 0,  //已赠送次数
        'getShell'       => 0,  //每日领取次数
        'get'       => array(),  //奖励
        'buy'       => array(),  //奖励
        'exchange'  => array(),  //兑换信息
        'isgetInitShell' => 0,//是否获取过初始箭矢
	);

    /**
     * 更新卡牌关卡战斗次数
     * @param int $id
     */
    public function getMoonInfo(){

        $riqi = Game::is_ymd($_SERVER['REQUEST_TIME']);
        $riqi = intval($riqi);

        if ($this->info["date"] != $riqi) {
            $this->info["date"] = $riqi;
            $this->info["isOpen"] = 0;
            $this->info["hit"] = 0;
            $this->info["hitNum"] = 0;
            $this->info["freeTimes"] = $this->hd_cfg['freeTimes'];
            $this->info["buyNum"] = 0;
            $this->info["openNum"] = 0;
            $this->info["friendGet"] = 0;
            $this->info["sendNum"] = 0;
            $this->info["getShell"] = 0;
            $this->info["buy"] = array();
            $this->info["friendShell"][] = 0;

            $this->save();
        }
        if($this->info['isgetInitShell'] == 0){
            Master::add_item($this->uid, KIND_ITEM, $this->hd_cfg['need']["id"], $this->hd_cfg['reviveCannon']);
            $this->info['isgetInitShell'] = 1;
            $this->save();
        }

        return $this->info;
    }

    /**
     * 获取免费炮弹
     * @param int $id
     */
    public function getFreeShell(){

        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //活动结算阶段
        if($this->get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }

        if ($this->info["getShell"] > 0){
            Master::error(ACTHD_NO_RECEIVE);
        }

        $this->info["getShell"] = 1;
        Master::add_item($this->uid, KIND_ITEM, $this->hd_cfg['need']["id"], $this->hd_cfg['freeShell']);
        $this->save();
    }

    /**
     * 购买炮弹
     * @param int $id
     */
    public function buyShells($id){

        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //活动结算阶段
        if($this->get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }

        $rwds = Game::get_key2id($this->hd_cfg['shopList'], 'id');
        $items = $rwds[$id];
        if (empty($items)){
            Master::error(ACTHD_NO_REWARD);
        }

        $buyNum = isset($this->info["buy"][$items["id"]]) ? $this->info["buy"][$items["id"]] : 0;
        if ( $items["is_limit"] > 0 && $buyNum >= $items["limit"] ){
            Master::error(ACT23_CREDITS_EXCHANGE_MAX);
        }

        $costItem = $items["cost"];
        if ($buyNum == 0 && !empty($items["costScale"])) {
            $costItem = $items["costScale"];
        }

        if ($costItem["count"] > 0) {
            // 扣除道具
            Master::sub_item2($costItem);
        }
        
        $addItems = $items['items'];
        if (!empty($items['sendItems'])) {
            $addItems["count"] += $items['sendItems']["count"];
        }

        // 添加道具
        Master::add_item2($addItems);

        $this->info["buy"][$items["id"]] += 1;
        $this->save();
    }

    /**
     * 开启月亮
     * @param int $id
     */
    public function openMoon(){

        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //活动结算阶段
        if($this->get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }

        if (empty($this->hd_cfg['openCost'])){
            Master::error(ITEMS_ERROR);
        }

        if ($this->info['isOpen'] > 0){
            Master::error(SANXIAO_BOSS_NOT_DIE);
        }

        if ($this->info["freeTimes"] > 0) {

            $this->info["freeTimes"] -= 1;
        }else{

            $this->info["openNum"] += 1;

            $costCount = $this->hd_cfg['openCost']["count"];
            if ($this->info["openNum"] <= 1) {
                $costCount = intval($costCount / 2);
            }

            Master::sub_item($this->uid, KIND_ITEM, $this->hd_cfg['openCost']["id"], $costCount);
        }

        $this->info["isOpen"] = 1;
        $this->info["hitNum"] = 0;
        $this->save();
    }

    /**
     * 攻击
     * @param int $id
     */
    public function play($hit){

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

        if ($this->info['isOpen'] <= 0){
            Master::error(GAME_LEVER_UNOPENED);
        }

        Master::sub_item($this->uid, KIND_ITEM, $this->hd_cfg['need']["id"], $this->hd_cfg['need']["count"]);

        $this->info["hit"] += $hit;
        $this->info["hitNum"] += 1;

        if ($this->info["hit"] >= $this->hd_cfg['moonHp']) {

            $this->info["isOpen"] = 0;
            $this->info["hit"] = 0;
            $this->info["moonNums"] += 1;

            //每日排行
            $Redis8029Model = Master::getRedis8029($this->_get_day_redis_id());
            $Redis8029Model->zIncrBy($this->uid, 1);

            //关卡排行
            $Redis8029Model = Master::getRedis8029($this->hd_cfg['info']['id']);
            $Redis8029Model->zIncrBy($this->uid, 1);

            // $Act40Model = Master::getAct40($this->uid);
            // $Act40Model->chongbang_club($this->hd_id,$this->hd_cfg['info']['id'], intval($Redis8029Model->zScore($this->uid)));

            // 奖励
            Master::add_item3($this->hd_cfg['jiangLi']);

            //数据返回
            Master::back_data($this->uid,$this->b_mol,"rwdData", $this->hd_cfg['jiangLi']);
        }

        $this->save();
    }

    /**
     * 十倍击杀
     * @param int $id
     */
    public function playTen(){

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

        if ($this->info['isOpen'] > 0){
            Master::error(SANXIAO_BOSS_NOT_DIE);
        }

        $needId = $this->hd_cfg['need']["id"];
        $needNum = $this->hd_cfg['need']["count"] * $this->info["hitNum"] + 5;
        Master::sub_item($this->uid, KIND_ITEM, $needId, $needNum * 10);

        $this->info["moonNums"] += 10;
        $this->save();

        $this->getMoonInfo();

        $jiangLi = $this->hd_cfg['jiangLi'];
        foreach ($jiangLi as $jk => $jv) {
            $jiangLi[$jk]['count'] = $jv['count'] * 10;
        }

        //每日排行
        $Redis8029Model = Master::getRedis8029($this->_get_day_redis_id());
        $Redis8029Model->zIncrBy($this->uid, 10);

        //关卡排行
        $Redis8029Model = Master::getRedis8029($this->hd_cfg['info']['id']);
        $Redis8029Model->zIncrBy($this->uid, 10);

        // $Act40Model = Master::getAct40($this->uid);
        // $Act40Model->chongbang_club($this->hd_id,$this->hd_cfg['info']['id'], intval($Redis8029Model->zScore($this->uid)));

        // 奖励
        Master::add_item3($jiangLi);

        //数据返回
        Master::back_data($this->uid,$this->b_mol,"rwdData", $jiangLi);
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

            $rwds = $this->hd_cfg['rwd'];
            $moonNums = $this->info['moonNums'];
            foreach ($rwds as $k => $v) {

                if ( $v["num"] <= $moonNums && empty($this->info["get"][$v["id"]]) ) {
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

        $moonNums = $this->info['moonNums'];
        if ($itmes["num"] > $moonNums || !empty($this->info["get"][$itmes["id"]])) {
            Master::error(ACTHD_NO_RECEIVE);
        }

        //领取奖励
        Master::add_item3($itmes['items']);

        $this->info["get"][$itmes["id"]] = 1;
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

        foreach ($hd_cfg['shopList'] as $k => $v){

            if (empty($this->info["buy"][$v['id']])){
                $hd_cfg['shopList'][$k]['buy'] = 0;
            }else{
                $hd_cfg['shopList'][$k]['buy'] = $this->info["buy"][$v['id']];
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

        $friendShellList = array();
        if (!empty($this->info["friendShell"])) {

            foreach ($this->info["friendShell"] as $fk => $fv) {

                if ($fv == 0) {
                    $friendShellList[] = array("uid" => 0);
                }else{
                    $friendInfo = Master::getFriendInfo($fv);
                    $friendShellList[] = $friendInfo;
                }
            }
        }

        unset($hd_cfg['info']['no']);
        unset($hd_cfg['exchangeTime']);

        $hd_cfg["isOpen"] = $this->info["isOpen"];
        $hd_cfg["hit"] = $this->info["hit"];
        $hd_cfg["hitNum"] = $this->info["hitNum"];
        $hd_cfg["freeTimes"] = $this->info["freeTimes"];
        $hd_cfg["buyShell"] = $this->info["buyShell"];
        $hd_cfg["buyNum"] = $this->info["buyNum"];
        $hd_cfg["openNum"] = $this->info["openNum"];
        $hd_cfg["moonNums"] = $this->info["moonNums"];
        $hd_cfg["friendGet"] = $this->info["friendGet"];
        $hd_cfg["sendNum"] = $this->info["sendNum"];
        $hd_cfg["getShell"] = $this->info["getShell"];
        $hd_cfg["friendShell"] = $friendShellList;
        $hd_cfg["isgetInitShell"] = $this->info["isgetInitShell"];
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
        $Redis8029Model = Master::getRedis8029($rankId);
        $Redis8029Model->back_data();
        $Redis8029Model->back_data_my($this->uid);
    }

    /*
     * 排行榜 和奖励
     * */
    public function allPaihang(){

        //关卡排行榜
        $Redis8029Model = Master::getRedis8029($this->hd_cfg['info']['id']);
        $Redis8029Model->back_data();
        $Redis8029Model->back_data_my($this->uid);
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

    /*
     * 赠送炮弹记录
     * */
    public function sendShell($num = 1) {
        //构造输出
        if( self::get_state() == 0 ){
            Master::error(GAME_LEVER_UNOPENED);
        }

        $Act35Model = Master::getAct35($this->uid);
        $shellMaxNum = intval($Act35Model->info['score'] / $this->hd_cfg["sendScore"]);
        if (($this->info["sendNum"] - 5) >= $shellMaxNum) {
            Master::error(BOITE_ATTEND_NUM_SHORT);
        }

        $this->info["sendNum"] += $num;
        $this->save();
        self::data_out();
    }

    /*
     * 赠送炮弹记录
     * */
    public function friendSendShell($fUid) {
        //构造输出
        if( self::get_state() == 0 ){
            Master::error(GAME_LEVER_UNOPENED);
        }

        $this->info["friendShell"][] = $fUid;

        if (count($this->info["friendShell"]) > 30) {
            array_shift($this->info["friendShell"]);
        }
        $this->save();
    }

    /**
     * 领取好友赠送的炮弹
     * @param int $id
     */
    public function get_shell_rwd($pos){

        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        if ($this->info["friendGet"] >= $this->hd_cfg['freeGet']) {
            Master::error(ACTHD_NO_RECEIVE);
        }

        $friendShell = array();
        foreach ($this->info["friendShell"] as $key => $value) {
            $friendShell[] = $value;
        }

        if (count($friendShell) <= 0) {
            Master::error(ACTHD_NO_RECEIVE);
        }

        if ($pos < 0) {

            $freeShell = 0;
            for ($i = $this->info["friendGet"]; $i < $this->hd_cfg['freeGet']; $i++) {

                if (count($friendShell) <= 0) {
                    break;
                }

                array_shift($friendShell);
                $freeShell += 1;
                $this->info["friendGet"] += 1;
            }
            Master::add_item($this->uid, KIND_ITEM, $this->hd_cfg['need']["id"], $freeShell);
        }else{

            unset($friendShell[$pos]);
            $this->info["friendGet"] += 1;
            Master::add_item($this->uid, KIND_ITEM, $this->hd_cfg['need']["id"], 1);
        }

        $this->info["friendShell"] = $friendShell;
        $this->save();
    }
}