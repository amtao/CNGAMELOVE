<?php
require_once "ActHDBaseModel.php";

/*
 * 劳动节活动
 */
class Act6229Model extends ActHDBaseModel
{
    public $atype = 6229;//活动编号
    public $comment = "劳动节活动";
    public $b_mol = "laborDay";//返回信息 所在模块
    public $b_ctrl = "cfg";//子类配置
    public $hd_id = 'huodong_6229';//活动配置文件关键字-编号
    public $item_type = 'hd6229';  //活动道具类型
    public $pkIDs = array();       //阵营id

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'cons' => array(),      //已消耗(完成)量
        'get' => array(),       //已领取的档次
        'selectID' => 0,        //选择阵营的皇子id
        'level' => 1,           //种植等级
    );

    /*
	 * 构造输出结构体
	 */
    public function make_out(){
        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
			return ;
        }
        //活动信息
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        $this->outf['set'] = $hd_cfg['set'];                     //阵营信息
        $this->outf['info'] = $hd_cfg['info'];                   //活动基础信息
        $this->outf['rwd'] = $hd_cfg['rwd'];                     //排行奖励信息
        $this->outf['winrwd'] = $hd_cfg['finalrwd']['win'];      //胜方奖励
        $this->outf['lostrwd'] = $hd_cfg['finalrwd']['lost'];    //败方奖励
        $this->outf['selectID'] = $this->info['selectID'];       //选择的阵营
        $this->outf['level'] = $this->isUpLv();                  //种植等级
        $this->outf['brwd'] = $this->changerwd($hd_cfg['brwd']); //手动领取的奖励信息
        $camp = $this->getcamp();                                //活动结束前一天阵营胜负情况 每个小时作为一个时间段 随机优劣状态
        $this->outf['set'][0]['score'] = $camp[0];
        $this->outf['set'][1]['score'] = $camp[1];

    }

    /*
     * 应援
     * id  道具id
     * hid 皇子id
     * */
    public function play($id,$hid,$num){
        //判断活动是否结束
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        if($this->get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }
        $num = intval($num);
        if ($num <= 0){
            Master::error(PARAMS_ERROR);
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
        Master::sub_item($this->uid,KIND_ITEM,$id,$num);

        if (empty($this->hd_cfg['items'][$id])){
            Master::error(HD_TYPE8_USE_ITEM_ERROR);
        }

        //抽奖
        $rwds = $this->lottery($this->hd_cfg['items'][$id],$num);

        if(empty($rwds)){
            Master::error(HD_TYPE8_USE_ITEM_ERROR);
        }
        //领取奖励
        Master::add_item3($rwds);
        //积分值
        $score *= $num;
        //排行榜数据插入    总贡献榜
        $Redis6229Model = Master::getRedis6229($this->hd_cfg['info']['id']);
        $Redis6229Model->zIncrBy($this->uid,$score);
        $Redis6229Model->back_data_my($this->uid);

        //单独皇子个人贡献榜
        if($hid == min($this->pkIDs)){
            //皇子id小的
            $Redis6223Model = Master::getRedis6223($this->hd_cfg['info']['id']);
            $Redis6223Model->zIncrBy($this->uid,$score);
        }else{
            //皇子id大的
            $Redis6224Model = Master::getRedis6224($this->hd_cfg['info']['id']);
            $Redis6224Model->zIncrBy($this->uid,$score);
        }
        //用时间记录每天的积分
        $ymd = Game::get_today_id();
        $this->info['cons'][$ymd] += $score;
        $this->save();
    }

    /*
	 * 抽奖函数
	 */
    private function lottery($rwdcfg,$num){
        //随机奖励
        $items = array();
        $list = $rwdcfg['list'];
        for ($i = 0;$i < $num;$i++){
            $rid =  Game::get_rand_key(10000,$list,'prob');
            if (empty($items[$rid])){
                $items[$rid] = array('id'=>$list[$rid]['id'],'kind'=>$list[$rid]['kind'],'count'=>$list[$rid]['count']);
            }else{
                $items[$rid]['count'] += $list[$rid]['count'];
            }
        }
        $rwdcfg['fixed']['count'] *= $num;
        $items[] = $rwdcfg['fixed'];
        return $items;
    }

    /*
     * 种植等级
     * */
    public function isUpLv(){
        $Redis6229Model = Master::getRedis6229($this->hd_cfg['info']['id']);
        $cons = $Redis6229Model->zScore($this->uid);
        $lv_cfg = Game::getcfg('chungeng');
        $lv = $this->info['level'];
        $max = count($lv_cfg)-1;
        foreach ($lv_cfg as $v){
            if ($cons <= $v['score'] || $v['level'] == $max){
                $lv = $v['level'];
                break;
            }
        }
        return $lv;
    }

    /*
     * 选择阵营
     * */
    public function select($id){
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
        Master::back_data($this->uid,$this->b_mol,'shop',$this->back_data_shop());
        Master::back_data($this->uid,$this->b_mol,'exchange',$this->back_data_exchange());
        $Sev6229Model = Master::getSev6229($this->hd_cfg['info']['id']);
        $Sev6229Model->setWinID();
        $Redis6229Model = Master::getRedis6229($this->hd_cfg['info']['id']);
        $Redis6229Model->back_data_my($this->uid);
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
    public function paihang(){
        //个人排行榜
        $Redis6229Model = Master::getRedis6229($this->hd_cfg['info']['id']);
        $Redis6229Model->back_data();
        $Redis6229Model->back_data_my($this->uid);
    }

    /**
     * 获取优劣情况
     */
    public function getcamp()
    {

        $show = $this->hd_cfg['info']['eTime']-86400;
        if ($_SERVER['REQUEST_TIME'] <= $show){
            $Sev6229Model = Master::getSev6229($this->hd_cfg['info']['id']);
            return $Sev6229Model->outs();
        }else{
            $Sev6229Model = Master::getSev6229($this->hd_cfg['info']['id']);
            $Redis6223Model = Master::getRedis6223($this->hd_cfg['info']['id']);
            $camp1 = (int)$Redis6223Model->zSum();
            if( self::get_state() == 2 && isset($Sev6229Model->info['add'])){
                $camp1 += $Sev6229Model->info['add'];
            }
            $Redis6224Model = Master::getRedis6224($this->hd_cfg['info']['id']);
            $camp2 = (int)$Redis6224Model->zSum();
            return array($camp1,$camp2);
        }
    }
}
