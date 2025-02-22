<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6183
 */
class Act6183Model extends ActHDBaseModel
{
    public $atype = 6183;//活动编号
    public $comment = "堆雪人活动";
    public $b_mol = "dxrhuodong";//返回信息 所在模块
    public $b_ctrl = "snowman";//子类配置
    public $hd_id = 'huodong_6183';//活动配置文件关键字

    /*
     * 初始化结构体
     * 累计数量
     * 领奖档次
     */
    public $_init =  array(
        'hurt'      => 0,  //堆雪人次数
        'lv'      => 1,  //堆雪人等级
        'get'       => array(),  //领奖状态
        'shop'      => array(),  //商城购买信息
        'exchange'  => array(),  //兑换信息
    );

    /**
     * 堆雪人
     * @param int $id
     */
    public function play($num = 1){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //活动结算阶段
        if($this->get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }
        if (empty($this->hd_cfg['need']) && empty($this->hd_cfg['list'])){
            Master::error(ITEMS_ERROR);
        }
        Master::sub_item($this->uid,KIND_ITEM,$this->hd_cfg['need'],$num);
        //随机奖励
        $items = array();
        $list = $this->hd_cfg['list'];
        for ($i = 0;$i < $num;$i++){
            $rid =  Game::get_rand_key(10000,$list,'prob');
            if (empty($items[$rid])){
                $items[$rid] = array('id'=>$list[$rid]['id'],'kind'=>$list[$rid]['kind'],'count'=>$list[$rid]['count']);
            }else{
                $items[$rid]['count'] += $this->hd_cfg['list'][$rid]['count'];
            }
        }
        $this->hd_cfg['fixed']['count'] *= $num;
        $items[] = $this->hd_cfg['fixed'];
        if (empty($items)){
            Master::error(ITEMS_ERROR);
        }
        $Sev6183Model = Master::getSev6183($this->hd_cfg['info']['id']);
        foreach ($items as $v){
            if ($v['id'] == 908 || $v['id'] == 912){
                $flg = 1;
                $Sev6183Model->add($this->uid,$v);
            }
        }
        if ($flg){
            $Sev6183Model->back_log_data();
        }

        //排行榜
        $Redis6183Model = Master::getRedis6183($this->hd_cfg['info']['id']);
        $Redis6183Model->zIncrBy($this->uid,$num);

        //领取奖励
        Master::add_item3($items);

        $this->info['hurt'] += $num;
        $hurt = $this->info['hurt'];
        foreach ($this->hd_cfg['boss'] as $k=>$v){
            if($hurt >= $v['hp']){
                $hurt -= $v['hp'];
                continue;
            }
            $this->info['lv'] = $v['lv'];
            break;
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
        //奖励信息
        // $Sev6183Model = Master::getSev6183($this->hd_cfg['info']['id']);
        // $lv = $Sev6183Model->info['lv'];

        $lv = $this->info['lv'];
        $rwds = Game::get_key2id($this->hd_cfg['rwd'],'lv');
        $itmes = $rwds[$id];
        if (empty($itmes)){
            Master::error(ACTHD_NO_REWARD);
        }

        if (!empty($this->info["get"][$id]) || $lv < $itmes['lv']){
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
            // $Sev6183Model = Master::getSev6183($this->hd_cfg['info']['id']);
            // $lv = $Sev6183Model->info['lv'];
            $lv = $this->info['lv'];
            $section = range(1,$lv);
            $rwds = Game::get_key2id($this->hd_cfg['rwd'],'lv');
            foreach ($section as $v){
                if (!empty($rwds[$v]) && $lv >= $rwds[$v]['lv'] && empty($this->info["get"][$lv])){
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

        $lv = $this->info['lv'];
        $hurt = $this->info['hurt'];
        $bossList = $hd_cfg['boss'];
        $max_lv = count($bossList)-1;
        foreach ($bossList as $k=>$v){
            if($hurt >= $v['hp']){
                $hurt -= $v['hp'];
                continue;
            }
            break;
        }
        $hd_cfg["bossinfo"]['lv'] = $lv;
        $hd_cfg["bossinfo"]['val'] = $hurt;
        $hd_cfg["bossinfo"]['hp'] = empty($bossList[$lv]['hp'])?$bossList[$max_lv]['hp']:$bossList[$lv]['hp'];
        $hd_cfg["bossinfo"]['skin'] = empty($bossList[$lv]['skin'])?$bossList[$max_lv]['skin']:$bossList[$lv]['skin'];

        foreach ($hd_cfg['rwd'] as $k => $v){
            if (empty($this->info["get"][$v['lv']])){
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
        unset($hd_cfg['boss']);
        unset($hd_cfg['list']);
        unset($hd_cfg['exchangeTime']);
        unset($hd_cfg['shop']);
        unset($hd_cfg['exchange']);

        $this->outf = $hd_cfg;
        Master::back_data(0,$this->b_mol,$this->b_ctrl,$this->outf);
    }

    public function back_data_hd(){
        self::data_out();
    }

    /*
     * 排行榜 和奖励
     * */
    public function paihang(){
        //个人排行榜
        $Redis6183Model = Master::getRedis6183($this->hd_cfg['info']['id']);
        $Redis6183Model->back_data();
        $Redis6183Model->back_data_my($this->uid);
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

