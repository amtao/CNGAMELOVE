<?php
require_once "ActHDBaseModel.php";

/*
 * 植树节活动
 */
class Act6222Model extends ActHDBaseModel
{
    public $atype = 6222;//活动编号
    public $comment = "清明节";
    public $b_mol = "qingming";//返回信息 所在模块
    public $b_ctrl = "act";//子类配置
    public $hd_id = 'huodong_6222';//活动配置文件关键字-编号
    public $item_type = 'hd6222';  //活动道具类型
    public $need = array(1010,1011,1012,1013,1014,1015,1016);  //活动道具

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'cons' => 0,        //积分
        'place' => 1,       //地点ID
        'vehicle' => 0,     //交通工具
        'lun' => 0,         //第几轮
        'site' => array(),  //停留过的点
        'shop'=>array(),    //商城购买信息
        'exchange'=>array(),//兑换信息
    );

    /*
	 * 构造输出结构体
	 */
    public function make_out(){
        //默认输出直接等于内部存储数据
        $this->outf = $this->info;
    }

    /*
     * 摇骰子
     * id  道具id
     * num 道具数量
     * */
    public function play($id,$num){
        //判断活动是否结束
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        if($this->get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }

        $vehiclecfg = Game::get_key2id($this->hd_cfg['vehicle'],'id');
        if (empty($vehiclecfg[$num]) || $this->info['cons'] < $vehiclecfg[$num]['score']){
            Master::error(PARAMS_ERROR);
        }

        //减去使用的道具
        Master::sub_item($this->uid,KIND_ITEM,$id,1);
        //地图停留点
        $map = Game::getcfg('dafuweng_step');
        $max = array_search(max($map),$map);
        if ($this->info['place'] == $max){
            $this->info['place'] = 1;
        }
        //掷骰子
        $data = array();
        $diceArr = array();
        $item_dice = Game::getcfg_info('item',$id);
        if ($item_dice['type'][0] != $this->item_type){
            Master::error(PARAMS_ERROR);
        }
        if ($item_dice['type'][1] == 'rand'){
            for ($i=0;$i<$num;$i++){
                $index_num = $i+1;
                $index = Game::get_rand_key1($item_dice['type'][2],'prob');
                $diceArr['num'.$index_num] = $item_dice['type'][2][$index]['id'];
            }
        }else{
            $diceArr['num1'] = $item_dice['type'][1];
        }
        $data['dice'] = $diceArr;
        $dian = array_sum($diceArr);
        $this->info['place'] += $dian;
        //地图定点
        if ($this->info['place'] > $max){//过点返回
            $this->info['place'] = $max-($this->info['place']-$max);
        }else if($this->info['place'] == $max){//到点重置
            $this->info['lun'] += 1;
        }
        //事件触发类型
        $type = $map[$this->info['place']]['type'];
        $data['type'] = $type;
        $additional = 0;//终点额外奖励
        if ($type == 2){//触发事件
            $event = $this->hd_cfg['event'];
            $ekey = Game::get_rand_key1($event,'prob');
            Master::add_item2($event[$ekey]['item']);
            $data['typeid'] = $event[$ekey]['id'];
        }elseif ($type == 3){//达到终点
            $additional = 100;//终点额外奖励
            $event = $this->hd_cfg['erwd'];
            $ekey = Game::get_rand_key1($event,'prob');
            Master::add_item2($event[$ekey]);
            //记录日志
            $Sev6222Model = Master::getSev6222($this->hd_cfg['info']['id']);
            $Sev6222Model->add($this->uid,$event[$ekey]);
            $Sev6222Model->bake_data();
        }

        //基础奖励
        $event = $this->hd_cfg['base'];
        $this->info['cons'] += $event['baseAdd']*$dian+$additional;
        $data['add'] = $event['baseAdd']*$dian+$additional;
        Master::add_item($this->uid,$event['baseItem']['kind'],$event['baseItem']['id'],$event['baseItem']['count']);

        //排行榜
        $Redis6222Model = Master::getRedis6222($this->hd_cfg['info']['id']);
        $Redis6222Model->zIncrBy($this->uid,$event['baseAdd']*$dian+$additional);

        //停留点记录
        $this->info['site'][] = $this->info['place'];
        $this->save();
        $data['place'] = $this->info['place'];
        $data['cons'] = $this->info['cons'];
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data);
    }

    /*
	 * 商品购买
	 * id 商品列表档次 id
     * num
	 * */
    public function buyone($id,$num){
        if( parent::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        $ymd = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        //判断id是否可以兑换
        $shop_cfg = $this->hd_cfg['shop'];
        if(empty($shop_cfg)){
            Master::error(HD_TYPE8_DONT_SHOPING);
        }

        foreach ($shop_cfg as $item){
            $shop[$item['id']] = $item;
        }
        if(empty($shop[$id]) || empty($shop[$id]['need'])){
            Master::error(HD_TYPE8_SHOP_NO_FUND);
        }
        if (isset($this->info['shop'][$id]) && !is_array($this->info['shop'][$id])){
            $count = $this->info['shop'][$id];
            $this->info['shop'][$id] = array();
            $this->info['shop'][$id][$ymd] = $count;
        }
        if($shop[$id]['is_limit'] == 1 && $shop[$id]['limit'] <= $this->info['shop'][$id][$ymd]){
            Master::error(HD_TYPE8_EXCEED_LIMIT);
        }
        //扣除
        Master::sub_item($this->uid,KIND_ITEM,$shop[$id]['need']['id'],$shop[$id]['need']['count']*$num);
        //购买
        if($shop[$id]['is_limit'] == 1){
            $this->add($id,$num);
        }

        $items = $shop[$id]['items'];
        if(empty($items['kind'])){
            $items['kind'] = 1;
        }
        Master::add_item($this->uid,$items['kind'],$items['id'],$items['count']*$num);
        $shop = $this->back_data_shop();
        Master::back_data($this->uid,$this->b_mol,'shop',$shop);

    }

    /*
     * 添加
     *
     * */
    public function add($id,$num = 1)
    {
        if(!is_int($num)){
            Master::error(ACT_HD_ADD_SCORE_NO_INT);
        }
        $ymd = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        $this->info['shop'][$id][$ymd] +=$num;
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
        //活动信息
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
        unset($hd_cfg['info']['event']);
        unset($hd_cfg['info']['shop']);
        unset($hd_cfg['info']['exchange']);
        unset($hd_cfg['info']['erwd']);
        Master::back_data($this->uid,$this->b_mol,'cfg',$hd_cfg);
        //基本信息
        $map = Game::getcfg('dafuweng_step');
        $max = array_search(max($map),$map);
        $act = array();
        $act['place'] = $this->info['place'];
        $act['cons'] = $this->info['cons'];
        $act['type'] = 0;
        $act['add'] = 0;
        $act['typeid'] = 0;
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$act);
        //通关日志
        $Sev6222Model = Master::getSev6222($this->hd_cfg['info']['id']);
        $Sev6222Model->bake_data();
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
            $ItemModel = Master::getItem($this->uid);
            foreach ($this->need as $v){
                if(!empty($ItemModel->info[$v]['count'])){
                    $news = 1; //可以领取
                }
            }

        }
        return $news;
    }

    /*
     * 排行榜 和奖励
     * */
    public function paihang(){
        //个人排行榜
        $Redis6222Model = Master::getRedis6222($this->hd_cfg['info']['id']);
        $Redis6222Model->back_data();
        $Redis6222Model->back_data_my($this->uid);
    }

    /*
     * 商城列表
     * */
    public function back_data_shop() {
        $outof = array();
        if(!empty($this->hd_cfg)){
            $init = $this->hd_cfg['shop'];
        }
        $ymd = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        if(!empty($init)){
            foreach ($init as $v){
                $value['id'] = $v['id'];
                $value['need'] = $v['need'];
                $value['items'] = array(
                    'kind' => $v['items']['kind'] ? $v['items']['kind'] : 1,
                    'id' => $v['items']['id'],
                    'count' => $v['items']['count']
                );
                $value['is_limit'] = $v['is_limit'];
                //是否限购
                if($v['is_limit'] == 1){
                    //每天重置旧数据处理
                    if (isset($this->info['shop'][$v['id']]) && !is_array($this->info['shop'][$v['id']])){
                        $count = $this->info['shop'][$v['id']];
                        $this->info['shop'][$v['id']] = array();
                        $this->info['shop'][$v['id']][$ymd] = $count;
                    }
                    if (empty($this->info['shop'][$v['id']][$ymd])){
                        $value['limit'] = $v['limit'];
                    }else{
                        $Surplus = $v['limit'] - $this->info['shop'][$v['id']][$ymd];
                        $value['limit'] = $Surplus<=0?0:$Surplus;
                    }
                }else{
                    $value['limit'] = 0;
                }
                $outof[] = $value;
            }
        }
        //默认输出直接等于内部存储数据
        return $outof;
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

    public function back_data_hd(){
        self::data_out();
    }

}
