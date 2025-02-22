<?php
require_once "ActHDBaseModel.php";
/*
 * 女生节
 */
class Act6220Model extends ActHDBaseModel
{
	public $atype = 6220;//活动编号
	
	public $comment = "女生节";
    public $b_mol = "girlsday";//返回信息 所在模块
    public $b_ctrl = "mirror";//返回信息 所在控制器
    public $hd_id = 'huodong_6220';//活动配置文件关键字
    public $change = array();
    public $clothe = array();
    public $hero_pve = array();

    /*
     * 初始化结构体
     */
    public $_init = array(

    );

    /*
	 * 抽奖
	 */
    public function play($num = 1){

        if( self::get_state() == 0 ){
            Master::error(ACTHD_OVERDUE);
        }
        if (!in_array($num,array(1,10))){
            Master::error(PARAMS_ERROR);
        }
        $need = Game::getcfg_info('item',$this->hd_cfg['need']);  //消耗物品
        $fixed = Game::getcfg_info('item',$this->hd_cfg['fixed']);//转换物品
        //扣除道具
        Master::sub_item($this->uid,KIND_ITEM,$need['id'],$num);
        $item_win = array();//抽奖结果
        $get = false;       //是否抽到服装
        $list = $this->hd_cfg['list'];
        $Sev6220Model = Master::getSev6220($this->hd_cfg['info']['id']);
        $allrwd = array();//未处理过的抽奖结果
        for ($i = 0;$i < $num;$i++){
            $key = Game::get_rand_key1($list,'prob');
            $allrwd[] = $list[$key];
            if ($list[$key]['kind'] == 95 || $list[$key]['kind'] == 96){
                $get = true;
                //抽到服装添加日志
                $Sev6220Model->add($this->uid,$list[$key]);
                //服装和剧情处理
                switch ($list[$key]['kind']){
                    case 95://服装
                        //已拥有服装转化
                        if ($this->isHave($list[$key]['id']) || in_array($list[$key]['id'],$this->clothe)){
                            $change = array('id'=>$fixed['id'],'kind'=>$fixed['kind'],'count'=>$this->hd_cfg['change'][$list[$key]['id']]);
                            $item_win[] = $change;
                            $this->change[] = array('clothe'=>$list[$key],'item'=>$change);
                        }else{//未拥有服装转化
                            $this->clothe[] = $list[$key]['id'];
                            $item_win[] = $list[$key];
                        }
                        break;
                    case 96://剧情
                        $this->hero_pve[] = $list[$key]['id'];
                        break;
                }
            }else{
                $item_win[] = $list[$key];
            }
        }
        //领取奖励
        Master::add_item3($item_win);
        //抽到服装或剧情处理
        if ($get){
            //日志刷新
            $Sev6220Model->back_data();
            //已拥有服装转换物品
            if (!empty($change)){
                Master::back_data($this->uid,$this->b_mol,'rwd',$this->change);
            }
            //剧情id 前端专用
            if (!empty($this->hero_pve)){
                Master::back_data($this->uid,'user','plotFragments',$this->hero_pve);
            }

        }

        if (count($allrwd) == 10){
            $allrwd = $this->reverse($allrwd);
        }

        Master::back_data($this->uid,$this->b_mol,'allrwd',$allrwd);
        Master::back_data($this->uid,$this->b_mol,'res',array('TorF'=>$get==false?0:1));
    }

    /*
	 * 判断服装是否已拥有
	 */
    private function isHave($id){
        //身上
        $act6140Model = Master::getAct6140($this->uid);
        if (in_array($id,$act6140Model->info['clothes'])){
            return true;
        }
        //邮件
        $MailModel = Master::getMail($this->uid);
        if (!empty($MailModel->info)){
            foreach ($MailModel->info as $j=>$l){
                if ($l['mtype'] == 1 && $l['rts'] == 0 && $l['isdel'] == 0 && !empty($l['items'])){
                    foreach ($l['items'] as $g){
                        if ($id == $g['id'] && $g['kind'] == 95){
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    /*
	 * 服装排序(未拥有靠前)
	 */
    private function reverse($allrwd){
        $arr1 = array();
        foreach ($allrwd as $v){
            if ($v['kind'] == 95 && !$this->isHave($v['id'])){
                $arr1[] = $v;
                unset($v);
            }
        }
        foreach ($allrwd as $y){
            $arr1[] = $y;
            unset($y);
        }
        return $arr1;
    }

    /*
	 * 构造输出结构体
	 */
    public function make_out(){
        //默认输出直接等于内部存储数据
        $this->outf = array();
        if( self::get_state() == 0 ){
            Master::error(ACTHD_OVERDUE);
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        unset($hd_cfg['change']);
        foreach ($hd_cfg['list'] as $k=>$v){
            unset($hd_cfg['list'][$k]['prob']);
        }
        $this->outf = $hd_cfg;
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
            $need = $this->hd_cfg['need'];
            $ItemModel = Master::getItem($this->uid);
            if(!empty($need) && !empty($ItemModel->info[$need]['count'])){
                $news = 1; //可以领取
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
}

