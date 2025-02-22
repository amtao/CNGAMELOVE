<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动8016
 */
class Act8016Model extends ActHDBaseModel
{
    public $atype = 8016;//活动编号
    public $comment = "新贵人令";
    public $b_mol = "newguirenling";//返回信息 所在模块
    public $b_ctrl = "newguirenlinghuodong";//子类配置
    public $hd_id = 'huodong_8016';//活动配置文件关键字

    /*
     * 初始化结构体
     * 累计数量
     * 领奖档次
     */
    public $_init =  array(
        'level'      => 1,  //等级
        'exp'        => 0,  //经验值
        'allExp'     => 0,  //总经验值
        'levelUp'    => 0,  //进阶
        'get'        => array(),  //奖励
        'task'       => array(),  //任务
    );

    /**
     * 进阶新贵人令
     * @param int $id
     */
    // public function exchangeOrderBack(){

    //     if (!empty($this->info["itemInfo"])) {

    //         $isBack = false;
    //         $itemInfoArr= $this->info["itemInfo"];
    //         foreach($itemInfoArr as $itemInfo ){
    //             if(empty($itemInfo)){
    //                 continue;
    //             }
    //             if ( intval($itemInfo["kind"]) == 112 ) {

    //                 if ($this->info["levelUp"] > 0) {
    //                     return false;
    //                 }
    
    //                 $isBack = true;
    //                 $this->info["levelUp"] = 1;
    //             }else{
    
    //                 Master::add_item($this->uid, $itemInfo['kind'], $itemInfo['id'], $itemInfo['count']);
    //             }
    //         }
           
    //           //7日福利新贵人令进阶
    //         $Act319Model = Master::getAct319($this->uid);
    //         $Act319Model->do_act_Type(103,1);

    //         $this->info["itemInfo"] = array();
    //         $this->save();

    //         if ($isBack) {
    //             self::data_out();
    //         }

    //     }
    // }

    //进阶贵人令
    public function UpGrade(){
        if( self::get_state() != 1){
            return;
        }
        $this->info["levelUp"] = 1;
        $this->save();
        self::data_out();
    }

    /**
     * 进阶新贵人令
     * @param int $id
     */
    public function getMagnateParam(){

        $paramList = array();

        $paramInfo = Game::getCfg("magnate_param");
        foreach ($paramInfo as $k => $v) {

            $paramList[$v["name"]] = $v["param"];
        }

        return $paramList;
    }

    /**
     * 升级新贵人令
     * @param int $id
     */
    public function upLevel($num = 1){
        if( self::get_state() != 1){
            Master::error(ACTHD_OVERDUE);
        }

        $cost = 0;
        $addLv = 0;
        $paramInfo = $this->getMagnateParam();
        for ($i=0; $i < $num; $i++) { 

            $levelInfo = Game::getcfg_info("magnate_new_lv", $this->info["level"]);
            if ($levelInfo["next_id"] > 0) {

                if ($this->info["level"] == 1) {
                    $addLv++;
                }
                $addLv++;

                $this->info["level"] = $levelInfo["next_id"];
                $this->info["allExp"] += $levelInfo["exp"];
                $cost += $paramInfo["buy_lv_item_cost"];
            }
        }

        if ($cost <= 0){
            Master::error(SERVANT_EPSKILL_UP_FAIL);
        }
        Master::sub_item($this->uid,KIND_ITEM,$paramInfo["buy_lv_item"],$cost);

        //新贵人令排行
        $Redis8016Model = Master::getRedis8016($this->hd_cfg['info']['id']);
        $Redis8016Model->zIncrBy($this->uid,$addLv);

        //7日福利新贵人令升级
        // $Act319Model = Master::getAct319($this->uid);
        // $Act319Model->set_act_Type_num(104,$this->info["level"]);

        $this->save();
    }

    /**
     * 获得奖励
     * @param int $id
     */
    public function get_rwd($id = 0){

        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        $week = date('W');
        $rwds = array();
        $normal = $this->getNormal();
        $elite = isset($this->info["get"]["elite"]) ? $this->info["get"]["elite"] : array();
        $level = $this->info["level"];
        $levelUp = $this->info["levelUp"];
        if ($id > 0) {

            $idInfo = Game::getcfg_info("magnate_new_rwd", $id);
            if ($idInfo["lv"] <= $level) {
                $rwds[] = $idInfo;
            }
        }else{
            $magnate_rwd = Game::getCfg('magnate_new_rwd');
            foreach ($magnate_rwd as $k => $v) {

                if ($level >= $v["lv"]) {

                    $rwds[] = $v;
                }
            }
        }
        if (empty($rwds)) {
            Master::error(ACTHD_NO_RECEIVE);
        }

        $items = array();
        $itemId = "";
        $kind = 1;
        foreach ($rwds as $rk => $rv) {

            if ( !in_array($rv["id"], $normal) ) {

                foreach ($rv["pt_rwd"] as $k => $v) {

                    $kind = isset($v['kind']) ? $v['kind'] : 1;
                    $itemId = $v["id"] . "_" . $kind;
                    if (empty($items[$itemId])){
                        $items[$itemId] = array(
                            'id'=>$v["id"],
                            'count'=>$v['count'],
                            'kind'=> $kind
                        );
                    }else{
                        $items[$itemId]['count'] += $v['count'];
                    }
                }
                $normal[] = $rv["id"];
            }

            if ( $levelUp > 0 && !in_array($rv["id"], $elite) ) {

                foreach ($rv["jj_rwd"] as $k => $v) {

                    $kind = isset($v['kind']) ? $v['kind'] : 1;
                    $itemId = $v["id"] . "_" . $kind;
                    if (empty($items[$itemId])){
                        $items[$itemId] = array(
                            'id'=>$v["id"],
                            'count'=>$v['count'],
                            'kind'=> isset($v['kind']) ? $v['kind'] : 1
                        );
                    }else{
                        $items[$itemId]['count'] += $v['count'];
                    }
                }
                $elite[] = $rv["id"];
            }
        }

        if (empty($items)) {
            Master::error(ACTHD_NO_RECEIVE);
        }

        //领取奖励
        Master::add_item3($items);
        $this->info["get"]["normal"] = $normal;
        $this->info["get"]["elite"] = $elite;
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

            $normal = $this->getNormal();
            $elite = isset($this->info["get"]["elite"]) ? $this->info["get"]["elite"] : array();
            $level = $this->info["level"];
            $levelUp = $this->info["levelUp"];

            $rwd = array();
            $magnate_rwd = Game::getCfg('magnate_new_rwd');
            foreach ($magnate_rwd as $k => $v) {

                $v["isOpen"] = 0;
                $v["isGet"] = 0;
                if ($level >= $v["lv"]) {

                    if (!empty($v["pt_rwd"])) {
                        $v["isOpen"] = 1;
                        if (in_array($k, $normal)) {
                            $v["isGet"] = 1;
                        }
                    }

                    if ($levelUp > 0) { // 是否购买过新贵人令
                        $v["isOpen"] = 2;
                        if (in_array($k, $elite)) {
                            $v["isGet"] = 2;
                        }
                    }
                }
                if ($v["isOpen"] > 0 && $v["isOpen"] != $v["isGet"]) {
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
            // Master::error(ACTHD_ACTIVITY_UNOPEN);
            return;
        }
        $week = date('W');
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];

        $normal = $this->getNormal();
        $elite = isset($this->info["get"]["elite"]) ? $this->info["get"]["elite"] : array();
        $hd_cfg["level"] = $level = $this->info["level"];
        $hd_cfg["levelUp"] = $levelUp = $this->info["levelUp"];

        $rwd = array();
        $magnate_rwd = Game::getCfg('magnate_new_rwd');
        foreach ($magnate_rwd as $k => $v) {

            $rwdInfo = array("id" => $v["id"], "isOpen" => 0, "isGet" => 0);
            if ($level >= $v["lv"]) {

                $rwdInfo["isOpen"] = 1;
                if (in_array($k, $normal)) {
                    $rwdInfo["isGet"] = 1;
                }

                if ($levelUp > 0) { // 是否购买过新贵人令
                    $rwdInfo["isOpen"] = 2;
                    if (in_array($k, $elite)) {
                        $rwdInfo["isGet"] = 2;
                    }
                }
            }
            $rwd[] = $rwdInfo;
        }

        unset($hd_cfg['info']['no']);
        unset($hd_cfg['shop']);

        $hd_cfg["bagList"] = $this->getBagList();
        $hd_cfg['rwd'] = $rwd;
        $hd_cfg["normal"] = $normal;
        $hd_cfg["elite"] = $elite;
        $hd_cfg["exp"] = $this->info["exp"];
        $hd_cfg["weekExp"] = isset($this->info["week"][$week]) ? $this->info["week"][$week] : 0;
        $hd_cfg["taskNormal"] = isset($this->info["task"]["normal"][$week]) ? $this->info["task"]["normal"][$week] : array();
        $hd_cfg["taskElite"] = isset($this->info["task"]["elite"]) ? $this->info["task"]["elite"] : array();
        $this->outf = $hd_cfg;
        Master::back_data(0,$this->b_mol,$this->b_ctrl,$this->outf);
    }

    public function back_data_hd(){
        self::data_out();
    }

    /*
     * 排行榜
     * */
    public function paihang(){

        //个人排行榜
        $Redis8016Model = Master::getRedis8016($this->hd_cfg['info']['id']);
        $Redis8016Model->back_data();
        $Redis8016Model->back_data_my($this->uid);
    }

    public function getBagList(){

        $giftBag = Game::getCfg('gift_bag');
        $bagList = array();
        foreach ($giftBag as $k => $v) {
            if ($v["actid"] == $this->atype) {

                $bagList[$k] = $v;
            }
        }
        return $bagList;
    }

    //通用任务设置
    public function setCurrencyTask($type,$num){
        $magnate_new_task = Game::getcfg('magnate_new_task');
        foreach($magnate_new_task as $v){
            if($v['tasktype'] == $type && $v['is_show'] == 1){
                $this->upTask($v['id'],$num);
            }
        }
    }

    /**
     * 任务新贵人令
     * @param int $id
     */
    public function upTask($id = 0, $num = 1){
        if( self::get_state() != 1){
            return false;
        }

        if ($id <= 0) {

            Game::crontab_debug($this->uid."  err2  id:".$id."  num:".$num, "act8016_upTask");
            return false;
        }

        // 1.每日宫斗  2.每日出游  3.每日种植  4.每日偷取晨露  5.每日做菜  6.每日联姻  7.每日徒弟历练  8.每日献礼  9.每日宴会  10.每日宫殿修整  11.每日许愿  12.每日喂养  13.每日赴宴  14.每日抽卡  101.累计登录  102.冲榜第一  103.雍容华贵  104.谁与争锋  105.天降福赐

        $magnate_new_task = Game::getcfg('magnate_new_task');
        if (empty($magnate_new_task[$id])){

            Game::crontab_debug($this->uid."  err3  id:".$id."  num:".$num, "act8016_upTask");
            return false;
        }

        $taskInfo = $magnate_new_task[$id];
        $task_type = intval($taskInfo["type"]);
        $need = intval($taskInfo["need"]);
        $exp = intval($taskInfo["awardexp"]);

        $week = date('W');
        $weekExp = isset($this->info["week"][$week]) ? $this->info["week"][$week] : 0;
        $normal = isset($this->info["task"]["normal"][$week]) ? $this->info["task"]["normal"][$week] : array();
        $elite = isset($this->info["task"]["elite"]) ? $this->info["task"]["elite"] : array();

        $addExp = 0;
        if ($task_type == 1) {

            if (!isset($normal[$id])) {
                $normal[$id] = array("c" => 0, "n" => 0);
            }

            $normal[$id]["n"] += $num;
            while ($normal[$id]["n"] >= $need) {
                $normal[$id]["c"] += 1;
                $normal[$id]["n"] -= $need;
                $addExp += $exp;
            }
            $this->info["task"]["normal"][$week] = $normal;

        }else{

            if (!isset($elite[$id])) {
                $elite[$id] = array("c" => 0, "n" => 0);
            }

            // 精英任务只能完成一次
            if ($elite[$id]["c"] > 0) {
                return false;
            }

            $elite[$id]["n"] += $num;
            if ($elite[$id]["n"] >= $need) {
                $elite[$id]["c"] += 1;
                $addExp = $exp;
            }
            $this->info["task"]["elite"] = $elite;
        }

        if ($addExp > 0) {

            $paramInfo = $this->getMagnateParam();
            $maxExp = $paramInfo["max_exp"];
            if ( $maxExp <= 0 || $weekExp < $maxExp) {

                if ( $maxExp > 0 && ($weekExp + $addExp) > $maxExp) {
                    $weekExp = $maxExp;
                    $addExp = $maxExp - $weekExp;
                }

                $this->info["exp"] += $addExp;
                $this->info["allExp"] += $addExp;
                $this->info["task"]["week"][$week] += $addExp;

                $addLv = 0;
                $magnate_lv = Game::getCfg('magnate_new_lv');
                foreach ($magnate_lv as $lk => $lv) {

                    if ($lv["lv"] == $this->info["level"] && $lv["exp"] <= $this->info["exp"]) {

                        if ($lv["next_id"] > 0) {

                            if ($this->info["level"] == 1) {
                                $addLv = 1;
                            }

                            $this->info["level"]++;
                            $this->info["exp"] -= $lv["exp"];
                            $addLv++;
                        }
                    }
                }

                if ($addLv > 0) {

                    //新贵人令排行
                    $Redis8016Model = Master::getRedis8016($this->hd_cfg['info']['id']);
                    $Redis8016Model->zIncrBy($this->uid, $addLv);
                }
            }
        }

        // 贵人令
        Game::cmd_other_flow($this->uid, 'act8016', 'upTask_8016', array($id => $num), 10006, $this->info["level"], $addExp, $this->info['exp']);

        $this->save();
        self::data_out();
    }

    /*
     * 签到
     */
    public function sign($id, $num){
        if($this->get_state() == 0){
            return false;
        }
        if(!isset($this->info['sign'])){
            $this->info['sign'] = date('Y-m-d',$_SERVER['REQUEST_TIME']);
            $this->upTask($id, $num);
        }
        $sign = strtotime($this->info['sign']);
        if(($_SERVER['REQUEST_TIME'] - $sign) > 24*3600){
            $this->info['sign'] = date('Y-m-d',$_SERVER['REQUEST_TIME']);//记录签到日期
            $this->upTask($id, $num);
        }
    }

    /*
     * 签到
     */
    public function upElite(){

        if($this->get_state() == 0){
            return false;
        }
        $this->info["levelUp"] = 1;
        $this->save();
    }

    /*
     * 签到
     */
    public function getNormal(){

        $newNormal = array();
        $normal = isset($this->info["get"]["normal"]) ? $this->info["get"]["normal"] : array();
        foreach ($normal as $week => $list) {

            if (is_array($list)) {

                foreach ($list as $k => $v) {

                    if (!in_array($v, $newNormal)) {
                        $newNormal[] = $v;
                    }
                }
            }
        }

        if (count($newNormal) <= 0) {
            $newNormal = $normal;
        }

        $this->info["get"]["normal"] = $newNormal;
        return $newNormal;
    }

    /*
     * 退款
     */
    public function resetGrl(){

        $this->info["levelUp"] = 0;
        $this->info["get"]["elite"] = array();
        $this->save();
    }
}

