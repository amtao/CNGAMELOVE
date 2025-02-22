<?php
require_once "ActHDBaseModel.php";

/*
 * 皇子应援活动
 */
class Act6136Model extends ActHDBaseModel
{
    public $atype = 6136;//活动编号
    public $comment = "皇子应援活动";
    public $b_mol = "yyhuodong";//返回信息 所在模块
    public $b_ctrl = "cfg";//子类配置
    public $hd_id = 'huodong_6136';//活动配置文件关键字-编号
    public $item_type = 'hd6136';
    public $pkIDs = array();//门客IDs
    public $hd_cfg;

    /**
     * @param unknown_type $uid   玩家id
     * @param unknown_type $id    活动id
     */
    public function __construct($uid)
    {

        $this->uid = intval($uid);

        //获取活动配置
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);

        if(!empty($this->hd_cfg['info']['id'])){
            parent::__construct($uid,$this->hd_cfg['info']['id']);//执行基类的构造函数
            //获取门客id
            $this->get_heroIds();
        }

    }


    /*
     * 初始化结构体
     */
    public $_init =  array(

        'get'=>0,  //领取奖励状态


    );

    /*
	 * 返回活动信息
	 */
    public function back_data(){
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
    }

    public function make_out()
    {
        //构造输出
        $this->outf = array();
        if(self::get_state() == 0) {
            return ;
        }

        $hd_cfg['info'] = $this->hd_cfg['info'];
        $hd_cfg['winnerRank'] = $this->hd_cfg['winnerRank'];
        $hd_cfg['teamRank'] = $this->hd_cfg['teamRank'];
        $hd_cfg['set'] = $this->hd_cfg['set'];
        $hd_cfg['state'] = $this->get_state();
        $hd_cfg['get'] = $this->info['get'];

        //用户是否领取队伍奖励
        $this->outf = $hd_cfg;

    }
    /**
     * 获取对决的门客id存在heroIds里
     */
    public function get_heroIds()
    {

        foreach($this->hd_cfg['set']['pk'] as $val) {
            array_push($this->pkIDs,$val['pkID']);
        }
    }

    /*
     * 应援
     * id 道具id
     * */
    public function play($id,$pkID){
        //判断活动是否结束
        if( parent::get_state() == 0 || parent::get_state() == 2){
            Master::error(ACTHD_OVERDUE);
        }
        //检查门客
        $this->checkInfo($pkID);
        if (!in_array($pkID,$this->pkIDs)){
            Master::error(PARAMS_ERROR);
        }
        //应援道具
        $itemcfg_info = Game::getcfg_info('item',$id);
        //活动编号
        $type = $itemcfg_info['type'][0];
        //贡献
        $contribution = $itemcfg_info['type'][1];
        //积分
        $score = $itemcfg_info['type'][2];
        //物品数据是否正确
        if(empty($score) || empty($contribution) || $type != $this->item_type){
            Master::error(HD_TYPE8_USE_ITEM_ERROR);
        }
        //减去使用的道具
        Master::sub_item($this->uid,KIND_ITEM,$id,1);


        //添加积分
        $Act6163Model = Master::getAct6163($this->uid);
        $Act6163Model->add_score($score);

        //添加贡献
        $Act6164Model = Master::getAct6164($this->uid);
        $Act6164Model->add_score($contribution,$pkID);

        //应援日志
        $Sev6136Model = Master::getSev6136($this->hd_cfg['info']['id']);
        $Sev6136Model->add($this->uid,$pkID,$id);

        //排行榜数据插入    总贡献榜
        $Redis6112Model = Master::getRedis6112($this->hd_cfg['info']['id']);
        $Redis6112Model->zIncrBy($this->uid,$contribution);

        //单独皇子个人贡献榜
        if($pkID == max($this->pkIDs)){
            //id大的皇子
            $Redis6114Model = Master::getRedis6114($this->hd_cfg['info']['id']);
            $Redis6114Model->zIncrBy($this->uid,$contribution);

        }else{
            //id小的皇子
            $Redis6113Model = Master::getRedis6113($this->hd_cfg['info']['id']);
            $Redis6113Model->zIncrBy($this->uid,$contribution);
        }


    }

    /*
     * 商品购买
     * id 商品列表档次 id
     * */
    public function buyone($id,$num){

        if( parent::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

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
        $Act6160Model = Master::getAct6160($this->uid);

        if (empty($Act6160Model->info[$id]) && $num > $shop[$id]['limit']){
            Master::error(HD_TYPE8_EXCEED_LIMIT);
        }
        $cha = $shop[$id]['limit'] - $Act6160Model->info[$id];
        if ($num >$cha){
            Master::error(HD_TYPE8_EXCEED_LIMIT);
        }
        if($shop[$id]['is_limit'] == 1 && $shop[$id]['limit'] <= $Act6160Model->info[$id]){
            Master::error(HD_TYPE8_EXCEED_LIMIT);
        }
        //扣除
        Master::sub_item($this->uid,KIND_ITEM,$shop[$id]['need']['id'],$shop[$id]['need']['count']*$num);
        //购买
        if($shop[$id]['is_limit'] == 1){
            $Act6160Model->add($id,$num);
        }

        $items = $shop[$id]['items'];
        if(empty($items['kind'])){
            $items['kind'] = 1;
        }
        $item_cfg = Game::getcfg_info('item', $items['id']);
        if($item_cfg['type'][1] == 'list'){
            foreach ($item_cfg['type'][2] as $item){
                Master::add_item($this->uid,empty($item['kind']) ? 1 : $item['kind'],$item['id'],$item['num']*$num);
            }
        }else{
            Master::add_item($this->uid,$items['kind'],$items['id'],$num);
        }

    }


    /*
     * 商品兑换
     * 兑换列表档次id
     * */
    public function exchangea($id,$num){

        if( parent::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        //判断id是否可以兑换
        if($num <= 0){
            Master::error(PARAMS_ERROR);
        }
        if(empty($this->hd_cfg['exchange']) ){
            Master::error(HD_TYPE8_EXCHANGE_NO_FUND);
        }
        foreach ($this->hd_cfg['exchange'] as $ite){
            $exchange[$ite['id']] = $ite;
        }
        if(empty($exchange[$id]) ){
            Master::error(HD_TYPE8_EXCHANGE_NO_FUND);
        }

        $Act6161Model = Master::getAct6161($this->uid);
        $duihuan = empty($Act6161Model->info[$id])?0:abs($Act6161Model->info[$id]);//已兑换物品数量
        if($exchange[$id]['is_limit'] == 1 && ($exchange[$id]['limit']-$duihuan) < $num){
            Master::error(HD_TYPE8_EXCEED_LIMIT);
        }
        $Act6163Model = Master::getAct6163($this->uid);
        $Act6163Model->sub_score($exchange[$id]['need'],$num);
        $items = $exchange[$id]['item'];//要兑换的信息
        if(empty($items['kind'])){
            $items['kind'] = 1;
        }
        $Act6161Model->add_items($id,$num);
        Master::add_item($this->uid,$items['kind'],$items['id'],$num);
    }

    /*
     * 构造输出
     */
    public function data_out(){
        $hdstate = parent::get_state();
        //活动状态
        if( $hdstate == 0){
            Master::error(ACTHD_OVERDUE);
        }

        //获取商城列表
        $Act6160Model = Master::getAct6160($this->uid);
        $Act6160Model->back_data();

        //获取兑换列表
        $Act6161Model = Master::getAct6161($this->uid);
        $Act6161Model->back_data();

        //仓库信息
//        $Act6162Model = Master::getAct6162($this->uid);
//        $Act6162Model->back_data();

        //积分信息
        $Act6163Model = Master::getAct6163($this->uid);
        $Act6163Model->back_data();

        //贡献信息
        $Act6164Model = Master::getAct6164($this->uid);
        $Act6164Model->back_data();

        //累计充值领取档次
        $Act6165Model = Master::getAct6165($this->uid);
        $Act6165Model->back_data();


        //日志
        $Sev6136Model = Master::getSev6136($this->hd_cfg['info']['id']);
        $Sev6136Model->bake_data();

        //判断上没上榜
        if ($hdstate == 2 && empty($Act6164Model->info['contribution'][$this->pkIDs[0]]) && empty($Act6164Model->info['contribution'][$this->pkIDs[1]])  ) {
            $this->info['get'] = 2;
        }

        //活动信息
        $hd_cfg = $this->hd_cfg;
        $cfg['info'] = $hd_cfg['info'];
        $cfg['winnerRank'] = $hd_cfg['winnerRank'];
        $cfg['teamRank'] = $hd_cfg['teamRank'];
        $cfg['set'] = $hd_cfg['set'];
        $cfg['state'] = $hdstate;
        $cfg['get'] = $this->info['get'];

        Master::back_data($this->uid,$this->b_mol,'cfg',$cfg);

        //结束展示各皇子排行
        if( $hdstate == 2){
            $Sev6137Model = Master::getSev6137($this->hd_cfg['info']['id']);
            if (empty($Sev6137Model->info['winID'])){
                $Sev6137Model->setWinID();
            }
            $Sev6137Model->bake_data();

            $Redis6113Model = Master::getRedis6113($this->hd_cfg['info']['id']);
            $Redis6113Model->back_data();

            $Redis6114Model = Master::getRedis6114($this->hd_cfg['info']['id']);
            $Redis6114Model->back_data();
        }
    }

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取
        if (parent::get_state() ==2){
            if (empty($this->info['get'])){
                //判断上没上榜
                $Act6164Model = Master::getAct6164($this->uid);
                if (!empty($Act6164Model->info['contribution'][$this->pkIDs[0]]) || !empty($Act6164Model->info['contribution'][$this->pkIDs[1]]) ) {
                    $news = 1;
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
        $Redis6112Model = Master::getRedis6112($this->hd_cfg['info']['id']);
        $Redis6112Model->back_data();
        $Redis6112Model->back_data_my($this->uid);
    }


    public function back_data_hd() {
        self::data_out();
    }

    /**
     * 检查门客
     * @param int $pkID
     * @return int mixed
     */
    public function checkInfo($pkID)
    {
        //获取门客信息
        $itemcfg_info = Game::getcfg_info('hero',$pkID);
        //门客存在
        if (empty($itemcfg_info)){
            Master::error(HERO_ERROR_ID);
        }


    }

    /**
     *  胜利队伍领取奖励
     */
    public function rewards()
    {
        //活动是否开启
        if(self::get_state() == 0) {
            Master::error(ACTHD_OVERDUE);
        }
        //活动是否处于展示阶段
        if ($this->get_state() != 2) {
            Master::error(MKDJ_004);
        }
        //没有投票，不能领奖
        $Act6164Model = Master::getAct6164($this->uid);
        if (empty($Act6164Model->info['contribution'][$this->pkIDs[0]]) && empty($Act6164Model->info['contribution'][$this->pkIDs[1]])  ) {
            Master::error(NOT_WINID_DATA);
        }
        //结算中
        $Sev6137Model = Master::getSev6137($this->hd_cfg['info']['id']);
        if (empty($Sev6137Model->info['winID'])){
            Master::error(MKDJ_010);
        }
        //获取胜利者id
        $winID = $Sev6137Model->getWinID();

        //已领取
        if ($this->info['get'] > 0) {
            Master::error(MKDJ_011);
        }

        //获取我的排名
        $Redis6112Model = Master::getRedis6112($this->hd_cfg['info']['id']);
        $rid = $Redis6112Model->get_rank_id($this->uid);
        //我的排名是否能领奖
        if ($rid < 201){
            //获取活动奖励数据
            $items = $this->hd_cfg['winnerRank'];
            //活动数据为空
            if (empty($items)) {
                Master::error(ACT_14_CONFIGWRONG);
            }
            //获取对应排行奖励数据
            $myItem=array();
            foreach($items as $rwd){
                //如果在排名奖励范围内  发放奖励
                if($rid >= $rwd['rand']['rs'] && $rid <= $rwd['rand']['re']){
                    $myItem = $rwd['member'];
                }
            }
            //没有数据
            if (empty($myItem)){
                Master::error(ACT_14_CONFIGWRONG);
            }
            //领取奖励
            Master::add_item3($myItem);
        }

        //胜利者羁绊奖励值
        $victory = $this->hd_cfg['teamRank']['victory'][0]['jiban'];
        //失败者羁绊奖励值
        $defeat = $this->hd_cfg['teamRank']['defeat'][0]['jiban'];
        //羁绊值奖励领取
        $Act6001Model = Master::getAct6001($this->uid);
        $Act6001Model -> addHeroJB($winID, $victory);
        if ($this->pkIDs[0] == $winID){
            $Act6001Model -> addHeroJB($this->pkIDs[1], $defeat);
        }else{
            $Act6001Model -> addHeroJB($this->pkIDs[0], $defeat);
        }

        $this->info['get'] = 1;
        $this->save();


    }


}
