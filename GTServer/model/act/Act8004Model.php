<?php
require_once "ActHDBaseModel.php";

/*
 * 8004
 */
class Act8004Model extends ActHDBaseModel
{
    public $atype = 8004;//活动编号
    public $comment = "购物狂欢";
    public $b_mol = "shopping";//返回信息 所在模块
    public $b_ctrl = "shoppingSpree";//子类配置
    public $hd_id = 'huodong_8004';//活动配置文件关键字-编号

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'cons'      => 0,  //够买次数
        'get'       => array(),  //领奖状态
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
        $hd_cfg["exchangeStartTime"] = strtotime($exchangeTime[0]["startTime"]);
        $hd_cfg["exchangeEndTime"] = strtotime($exchangeTime[0]["endTime"]);
        foreach ($exchangeTime as $key => $value) {

            if ($_SERVER['REQUEST_TIME'] >= strtotime($value['startTime']) && $_SERVER['REQUEST_TIME'] <= strtotime($value['endTime'])) {
                $idList = $value["idList"];
                $hd_cfg["exchangeStartTime"] = strtotime($value["startTime"]);
                $hd_cfg["exchangeEndTime"] = strtotime($value["endTime"]);
                break;
            }
        }
        $hd_cfg['cons'] = $this->info['cons'];

        unset($hd_cfg['exchangeTime']);
        unset($hd_cfg['info']['no']);
        unset($hd_cfg['shop']);
        unset($hd_cfg['exchange']);
        $this->outf = $hd_cfg;
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

        $cons = $this->info['cons'];
        $consRwdList = $this->hd_cfg['consRwd'];
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

        if ( $cons < $consRwdInfo["cons"]) {
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

            $cons = $this->info['cons'];
            $rwds = Game::get_key2id($this->hd_cfg['consRwd'],'cons');
            foreach ($this->hd_cfg['consRwd'] as $v){
                if (!empty($this->info["get"][$v["cons"]]) && $cons >= $v['cons'] ){
                    $news = 1;
                }
            }
        }
        return $news;
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

    public function exchangeItem($id, $zc_item){

        $c = empty($this->info['exchange'][$id])?0:$this->info['exchange'][$id];
        $this->info['exchange'][$id] = $c + 1;
        $this->info['cons'] += 1;
        $this->info["itemInfo"] = $zc_item['items'][0];
        $this->save();

        $exchange = $this->back_data_exchange();
        Master::back_data($this->uid,$this->b_mol,'exchange',$exchange);
        $this->make_out();
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
    }
}
