<?php
require_once "ActHDBaseModel.php";

/*
 * 植树节活动
 */
class Act6221Model extends ActHDBaseModel
{
    public $atype = 6221;//活动编号
    public $comment = "植树节活动";
    public $b_mol = "arborday";//返回信息 所在模块
    public $b_ctrl = "cfg";//子类配置
    public $hd_id = 'huodong_6221';//活动配置文件关键字-编号
    public $item_type = 'hd6221';  //活动道具类型
    public $pkIDs = array();       //阵营id

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'cons' => 0,        //已消耗(完成)量
        'get' => array(),   //已领取的档次
        'selectID' => 0,    //选择阵营的皇子id
    );

    /*
	 * 构造输出结构体
	 */
    public function make_out(){
        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
			Master::error(ACTHD_ACTIVITY_UNOPEN);
        }
        //活动信息
        $hd_cfg = $this->hd_cfg;
        $Sev6221Model = Master::getSev6221($this->hd_cfg['info']['id']);
        $Redis6219Model = Master::getRedis6219($hd_cfg['info']['id']);
        $sCamp1 = (int)$Redis6219Model->zScore($this->uid);
        if( self::get_state() == 2 && isset($Sev6221Model->info['add'])){
            $sCamp1 += $Sev6221Model->info['add'];
        }
        $Redis6220Model = Master::getRedis6220($hd_cfg['info']['id']);
        $sCamp2= (int)$Redis6220Model->zScore($this->uid);

        //阵营1总积分
        $camp1 = (int)$Redis6219Model->zSum();
        //阵营2总积分
        $camp2= (int)$Redis6220Model->zSum();

        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];

        $exchangeTime = $hd_cfg["exchangeTime"];
        $idList = $exchangeTime[0]["idList"];
        $hd_cfg["exchangeStartTime"] = strtotime($exchangeTime[0]["startTime"]);
        $hd_cfg["exchangeEndTime"] = strtotime($exchangeTime[0]["endTime"]);
        $hd_cfg["exchangeTitle"] = $exchangeTime[0]["title"];
        $hd_cfg["exchangeTitle2"] = $exchangeTime[0]["title2"];
        foreach ($exchangeTime as $key => $value) {

            if ($_SERVER['REQUEST_TIME'] >= strtotime($value['startTime']) && $_SERVER['REQUEST_TIME'] <= strtotime($value['endTime'])) {
                $idList = $value["idList"];
                $hd_cfg["exchangeStartTime"] = strtotime($value["startTime"]);
                $hd_cfg["exchangeEndTime"] = strtotime($value["endTime"]);
                $hd_cfg["exchangeTitle"] = $value["title"];
                $hd_cfg["exchangeTitle2"] = $value["title2"];
                break;
            }
        }

        unset($hd_cfg['info']['no']);
        unset($hd_cfg['exchangeTime']);
        unset($hd_cfg['exchange']);

        $this->outf['set'] = $hd_cfg['set'];
        $this->outf['set'][0]['score'] = $camp1;
        $this->outf['set'][1]['score'] = $camp2;
        $this->outf['score'] = $hd_cfg['set'];
        $this->outf['score'][0]['score'] = $sCamp1;
        $this->outf['score'][1]['score'] = $sCamp2;
        $this->outf['info'] = $hd_cfg['info'];
        $this->outf['rwd'] = $hd_cfg['rwd'];
        $this->outf['winrwd'] = $hd_cfg['finalrwd']['win'];
        $this->outf['lostrwd'] = $hd_cfg['finalrwd']['lost'];
        $this->outf['exchangeStartTime'] = $hd_cfg['exchangeStartTime'];
        $this->outf['exchangeEndTime'] = $hd_cfg['exchangeEndTime'];
        $this->outf['exchangeTitle'] = $hd_cfg['exchangeTitle'];
        $this->outf['exchangeTitle2'] = $hd_cfg['exchangeTitle2'];
        $this->outf['selectID'] = $this->info['selectID'];
        $this->outf['brwd'] = $this->changerwd($hd_cfg['brwd']);

    }

    /**
     * 获得奖励
     */
    public function get_rwd($id = 0){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        if(in_array($id,$this->info['get'])){
            Master::error(DAILY_IS_RECEIVE);
        }
        //获取积分
        $this->get_heroIds();
        if (empty($this->info['selectID'])){
            Master::error(PARAMS_ERROR);
        }
        if($this->info['selectID'] == min($this->pkIDs)){
            //皇子id小的
            $Redis6219Model = Master::getRedis6219($this->hd_cfg['info']['id']);
            $cons = (int)$Redis6219Model->zScore($this->uid);
        }else{
            //皇子id大的
            $Redis6220Model = Master::getRedis6220($this->hd_cfg['info']['id']);
            $cons= (int)$Redis6220Model->zScore($this->uid);
        }
        //奖励信息
        $hd_info = Game::get_key2id($this->hd_cfg['brwd'],'id');
        $rinfo = $hd_info[$id];
        if(empty($rinfo)){
            Master::error(ACTHD_NO_REWARD);
        }
        if($cons < $rinfo['need']){
            Master::error(ACTHD_NO_RECEIVE);
        }
        //领取奖励
        Master::add_item3($rinfo['items']);
        $this->info['get'][] = $id;
        $this->save();
    }

    /*
     * 应援
     * id  道具id
     * hid 皇子id
     * */
    public function play($id,$hid,$num = 1){
        //判断活动是否结束
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        if($this->get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }
        $this->get_heroIds();
        $check_info = Game::getcfg_info('hero',$hid);
        if (!empty($this->info['selectID']) && $this->info['selectID'] != $hid){
            Master::error(PARAMS_ERROR);
        }
        //应援道具
        $itemcfg = Game::getcfg_info('item',$id);
        //活动编号
        $type = $itemcfg['type'][0];
        //积分
        $score = $itemcfg['type'][1];
        //物品数据是否正确
        if(empty($score) || $type != $this->item_type){
            Master::error(HD_TYPE8_USE_ITEM_ERROR);
        }
        //减去使用的道具
        Master::sub_item($this->uid,KIND_ITEM,$id,1);
        $this->info['cons'] += $score;
        //排行榜数据插入    总贡献榜
        $Redis6221Model = Master::getRedis6221($this->_get_day_redis_id());
        $Redis6221Model->zIncrBy($this->uid,$score);
        $Redis6221Model->back_data_my($this->uid);

        //随机奖励
        $items = array();
        $allList = $this->hd_cfg['list'];
        $allFixed = $this->hd_cfg['fixed'];
        $list = array();
        foreach ($allList as $k => $v) {

            if ($v["type"] == $id) {
                $list[] = $v;
            }
        }

        $fixed = array();
        foreach ($allFixed as $k => $v) {

            if ($v["type"] == $id) {
                $fixed = $v;
            }
        }

        $probMax = 0;
        foreach ($list as $key => $value) {

            $probMax += $value["prob"];
        }
        for ($i = 0;$i < $num;$i++){
            $rid =  Game::get_rand_key($probMax,$list,'prob');
            if (empty($items[$rid])){
                $items[$rid] = array('id'=>$list[$rid]['id'],'kind'=>$list[$rid]['kind'],'count'=>$list[$rid]['count']);
            }else{
                $items[$rid]['count'] += $this->hd_cfg['list'][$rid]['count'];
            }
        }
        $fixed['count'] *= $num;
        $items[] = $fixed;
        if (empty($items)){
            Master::error(ITEMS_ERROR);
        }
        //领取奖励
        Master::add_item3($items);

        //单独皇子个人贡献榜
        if($hid == min($this->pkIDs)){
            //皇子id小的
            $Redis6219Model = Master::getRedis6219($this->hd_cfg['info']['id']);
            $Redis6219Model->zIncrBy($this->uid,$score);
        }else{
            //皇子id大的
            $Redis6220Model = Master::getRedis6220($this->hd_cfg['info']['id']);
            $Redis6220Model->zIncrBy($this->uid,$score);
        }

        $this->save();
        self::data_out();
    }

    /*
     * 选择阵营
     * */
    public function Select($id){
        if (!empty($this->info['selectID']) && $this->info['selectID']!=$id){
            Master::error(PARAMS_ERROR);
        }
        $this->get_heroIds();
        $check_info = Game::getcfg_info('hero',$id);
        $this->info['selectID'] = $id;
        $this->save();
    }

    /*
     * 构造输出
     */
    public function data_out(){
        $hd_state = $this->get_state();
        //活动状态
        if( $hd_state == 0){
            Master::error(ACTHD_OVERDUE);
        }
        $Sev6221Model = Master::getSev6221($this->hd_cfg['info']['id']);
        $Sev6221Model->bake_data();

        $this->get_heroIds();
        $score = 0;
        $rid = 0;
        if($this->info['selectID'] == min($this->pkIDs)){
            //皇子id小的
            $Redis6219Model = Master::getRedis6219($this->hd_cfg['info']['id']);
            $Redis6219Model->back_data_my($this->uid);
        }else{
            //皇子id大的
            $Redis6220Model = Master::getRedis6220($this->hd_cfg['info']['id']);
            $Redis6220Model->back_data_my($this->uid);
        }

        $Redis6221Model = Master::getRedis6221($this->_get_day_redis_id());
        $Redis6221Model->back_data_my($this->uid);
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
            //奖励信息
            foreach ( $this->hd_cfg['brwd'] as $k=>$v){
                if (!in_array($v['id'],$this->info['get']) && $this->info['cons']>=$v['need']){
                    $news = 1; //可以领取
                }
            }
            $ItemModel = Master::getItem($this->uid);
            if(!empty($ItemModel->info[1007]['count']) || !empty($ItemModel->info[1008]['count']) || !empty($ItemModel->info[1009]['count'])){
                $news = 1; //可以领取
            }
        }
        return $news;
    }

    /**
     * 获取对决的门客id存在heroIds里
     */
    public function get_heroIds()
    {

        foreach($this->hd_cfg['set'] as $val) {
            array_push($this->pkIDs,$val['pkID']);
        }
    }

    /**
     * 领奖状态
     * @param int $pkID
     * @return int mixed
     */
    public function changerwd($rinfo)
    {
        foreach ($rinfo as $k=>$v){
            $rinfo[$k]['get'] = 0;
            if (in_array($v['id'],$this->info['get'])){
                $rinfo[$k]['get'] = 1;
            }
        }
        return $rinfo;
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
        $Redis6221Model = Master::getRedis6221($rankId);
        $Redis6221Model->back_data();
        $Redis6221Model->back_data_my($this->uid);
    }


    public function back_data_allhd() {
        self::data_out();
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
