<?php
require_once "ActHDBaseModel.php";

/*
 * 活动296
 */
class Act296Model extends ActHDBaseModel
{
    public $atype = 296;//活动编号
    public $comment = "挖宝";
    public $b_mol = "wbhuodong";//返回信息 所在模块
    public $b_ctrl = "wabao";//子类配置
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_296';//活动配置文件关键字

    public $tip = 0;// 0:没有红点 1:触发红点

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'cons' => 0, //拥有的锄头
        'rwd' => array(),  //已领取的格子id
        'time' => 0,
    );


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
        }
    }


    /*
     * 构造输出结构体
     */
    public function make_out(){

        $this->tip = 0;  //0:没有红点 1:触发红点

        //每天重置挖宝进度
        if(!Game::is_today($this->info['time'])){
            $this->info['rwd'] = array();
            $this->info['cons'] = 0;
            $this->info['time'] = $_SERVER['REQUEST_TIME'];
        }

        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
            return ;
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        $this->outf['cfg']['info'] = $hd_cfg['info'];  //活动展示时间
        $this->outf['cfg']['msg'] = $hd_cfg['msg'];  //活动说明

        //每日重置,获得锄头
        $this->outf['cfg']['chutou'] =  array();
        $Act89Model = Master::getAct89($this->uid);
        foreach($hd_cfg['chutou'] as $value){
            $has = $Act89Model->get_num($value['type']);
            $isGet = $Act89Model->check($value['id'],$value['type'],$value['max']);
            $value['has'] = $has;  //当前拥有
            $value['isGet'] = $isGet;  //是否已领取 0:不可领取 1:可领取 2:已领取
            $this->outf['cfg']['chutou'][] = $value;
            //触发红点
            $this->tip = $this->tip == 1 ? 1 : $isGet;
        }

        //随机奖励
        $this->outf['cfg']['suiji'] =  $hd_cfg['suiji'];

        $wanum = 0; //挖宝次数
        //配置信息
        $rwd_cfg = self::get_cfg_rwd();
        foreach ( $rwd_cfg as $k => $v) {
            $v['isGet'] = 0;  //0:不可领取 1:可领取 2:已领取
            if(in_array($v['tid'],$this->info['rwd'])){
                $v['isGet'] = 2;  //0:不可领取 1:可领取 2:已领取
                if($v['type'] == 1 ){  //0:空土地 1:土地挖宝 2:银宝箱 3:金宝箱
                    $wanum += 1;
                }
            }elseif($v['type'] == 2 || $v['type'] == 3){  //0:空土地 1:土地挖宝 2:银宝箱 3:金宝箱
                if(!empty($v['need']) && !empty($this->info['rwd']) ){
                    if(count(array_diff($v['need'],$this->info['rwd'])) <= 0){
                        $v['isGet'] = 1;  //0:不可领取 1:可领取 2:已领取
                        //触发红点
                        $this->tip = $v['isGet'] == 1 ? 1 : 0;
                    }
                }
            }
            $this->outf['cfg']['rwd'][] = $v;
        }
        $this->outf['cons'] = $this->info['cons']; //拥有的锄头
        $this->outf['num'] = $wanum; //挖宝次数
    }

    /**
     * 获取土地配置
     */
    public function get_cfg_rwd(){

        static $cfg;
        if(empty($cfg)){
            $cfg = array();
            $id = 0;
            foreach ($this->hd_cfg['rwd'] as $k => $v ){
                $id ++;
                $cfg[$id] = array(
                    'tid' => $k, //土地配置id
                    'id' => $id,  //前端排序id
                    'type' => $v['type'],  //0:空土地 1:土地挖宝 2:银宝箱 3:金宝箱
                    'need' => empty($v['need'])?array():$v['need'],  //获得宝箱需要的条件
                    'items' => empty($v['items'])?array():$v['items'],  //道具
                );
            }
        }

        return $cfg;
    }


    //---------------------------- 玩家操作 挖宝----------------------
    /**
     * 锄草
     */
    public function chucao(){

        //道具是否充足
        if($this->info['cons'] <= 0){
            Master::error(ITEMS_NUMBER_SHORT);
        }

        //获取可以铲的地
        $dis = array(); //存放可以铲的地
        $baos = array(); //存放不能领取的宝箱
        foreach ( $this->outf['cfg']['rwd'] as $v) {
            //过滤 可领取 , 已领取
            if( $v['isGet'] == 1 || $v['isGet'] == 2 ){
                continue;
            }
            //可以铲的地
            if($v['type'] == 1 ){   //0:空土地 1:土地挖宝 2:银宝箱 3:金宝箱
                $dis[$v['id']] = 5;  //存放为5的概率
            }
            //存放不能领取的宝箱
            if($v['type'] == 2 || $v['type'] == 3 ){   //0:空土地 1:土地挖宝 2:银宝箱 3:金宝箱
                $baos[$v['id']] = $v['need'];  //存放完成条件
            }
        }

        //制作伪概率 一排或者一列只剩一块土地未开发,将概率从5设为2
        $bx = array();//单个宝箱未开发土地id
        foreach ($baos as $bk => $bv){  //循环每个宝箱
            //单个宝箱
            foreach($bv as $xv){
                //如果未开发
                if(!empty($dis[$xv])){
                    $bx[] = $xv; //记录未开发id
                }
            }
            //如果不是只剩一块土地,跳过
            if(count($bx) != 1){
                continue;
            }
            //过滤配置异常 -> 不报错
            if( empty($dis[$bx[0]]) ){
                continue;
            }
            //剩一块土地,制作伪概率 = 1
            $dis[$bx[0]] = 1;
        }

        if(empty($dis)){
            Master::error(ACT_296_STATUS);
        }

        //概率 铲一个地
        $gid = rand(1,array_sum($dis));
        foreach ( $dis as $k => $v ) {
            if($gid > $v ){
                $gid -= $v;
                continue;
            }
            $tuid = $k;
            break;
        }
        if(empty($tuid)){
            Master::error(ACT_14_CONFIGWRONG.$tuid);
        }

        //扣除道具 - 锄头
        Master::sub_item($this->uid,11,332,1);

        //记录领取格子
        $rwd_cfg = self::get_cfg_rwd();
        if(empty($rwd_cfg[$tuid]['tid'])){
            Master::error(SEV_54_XITONGMANG.$rwd_cfg[$tuid]['tid']);
        }
        $this->info['rwd'][] = $rwd_cfg[$tuid]['tid'];
        $this->save();

        //扣除流水+铲地id
        Game::cmd_flow(6,'332_'.$tuid,-1,$this->info['cons']);

        //随机获得奖励
        $extra = $this->outf['cfg']['suiji'];
        $rk = Game::get_rand_key(10000,$extra,'prob10000');
        if(!empty($extra[$rk])){
            $item = array(
                'kind' => $extra[$rk]['kind']?$extra[$rk]['kind']:1,
                'id' => $extra[$rk]['id'],
                'count' => $extra[$rk]['count'],
            );
            Master::add_item2($item,'','');
            //弹窗
            Master::$bak_data['a']['wbhuodong']['win'] = array(
                'chid' => $tuid,  //被铲的id
                'items' => $item,     //获得的奖励
            );
        }

        return true;
    }

    //---------------------------- 玩家操作 领取宝箱----------------------
    /**
     * 领取宝箱
     * $id 档次id
     */
    public function baoxiang($id){

        //是否已领取
        $rwd_cfg = self::get_cfg_rwd();
        //记录领取格子
        if(empty($rwd_cfg[$id]['tid'])){
            Master::error(SEV_54_XITONGMANG.$rwd_cfg[$id]['tid']);
        }

        if(in_array($rwd_cfg[$id]['tid'],$this->info['rwd'])){
            Master::error(ACT_36_LINGWAN);
        }

        //判断能不能领取
        foreach ($this->outf['cfg']['rwd'] as $value){

            if($value['id'] != $id){
                continue;
            }

            if($value['isGet'] == 0  ){ //0:不可领取 1:可领取 2:已领取
                Master::error(ACT_30_BUKELING);  //参数错误
            }

            if($value['isGet'] == 2  ){ //0:不可领取 1:可领取 2:已领取
                Master::error(ACT_30_YILING);  //参数错误
            }

            if($value['type'] != 3 && $value['type'] != 2 ){//0:空土地 1:土地挖宝 2:银宝箱 3:金宝箱
                Master::error(PARAMS_ERROR);  //参数错误
            }

            $this->info['rwd'][] = $rwd_cfg[$id]['tid'];
            $this->save();

            Master::add_item3($value['items'],'','');
        }

    }

    //---------------------------- 道具操作 ----------------------
    /**
     * 资源消耗
     * @param $num
     */
    public function add($num){
        if( self::get_state() == 1 ){
            $this->info['cons'] += $num;
            $this->save();
            Game::cmd_flow(6,332,$num,$this->info['cons']);
        }
    }

    /**
     * 资源消耗
     * @param $num
     */
    public function sub($num){
        if( parent::get_state() == 0 ){
            Master::error(ACTHD_ACTIVITY_UNOPEN.__LINE__);
        }
        $this->info['cons'] -= $num;
        if($this->info['cons'] < 0){
            Master::error(ITEMS_NUMBER_SHORT);
        }
        $this->save();
    }



    //----------------------------每日重置任务----------------------
    /*
     * 任务
     * @param $id  任务类型id
     * @param $num 任务数量
     */
    public function get_chutou_task($id,$num){
        //判断活动是否还持续中
        if( parent::get_state() != 1 ){
            return 0;
        }
        $Act89Model = Master::getAct89($this->uid);
        $Act89Model->add_task($id,$num);

        //刷新红点
        $this->back_data();

    }

    /*
     * 领取任务奖励 --  每日任务
     * $id : 要领取的档次id
     */
    public function get_chutou_rwd($id){
        foreach($this->outf['cfg']['chutou'] as $value ){
            if($value['id'] != $id){
                continue;
            }
            if($value['isGet'] == 0){
                Master::error(ACTHD_NO_RECEIVE);  //不能领取
            }
            if($value['isGet'] == 2){
                Master::error(DAILY_IS_RECEIVE);  //已领取
            }

            //记录已经领取过了
            $Act89Model = Master::getAct89($this->uid);
            $Act89Model->add_get($id);
            //加道具
            Master::add_item2($value['items']);

            //刷新红点
            $this->back_data();

            return true;
        }
        Master::error(PARAMS_ERROR.$id);  //参数错误
    }

    //----------------------------u 下发----------------------
    /*
    * 下发每日任务信息前端
    */
    public function back_data_task_u(){
        $data = array();
        $data['cons'] = $this->info['cons']; //拥有的锄头
        $data['cfg']['chutou'] = $this->outf['cfg']['chutou']; //每日信息
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);
    }

    /*
    * 下发每日任务信息前端
    */
    public function back_data_rwd_u(){
        $data = array();
        $data['cons'] = $this->info['cons']; //拥有的锄头
        $data['num'] = $this->outf['num']; //挖宝次数
        $data['cfg']['rwd'] = $this->outf['cfg']['rwd']; //土地信息
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);
    }


    //----------------------------处理活动额外逻辑----------------------
    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取

        //判断活动是否还持续中
        if( parent::get_state() != 1 ){
            return $news;
        }
        //可以锄草 ,  可以领取任务奖励   可以领取宝箱
        if($this->info['cons'] > 0 || $this->tip == 1){
            $news = 1;
        }
        return $news;
    }



    /*
     * 返回活动信息--保存时不返回信息
     * 只返回当前活动在生效列表中对应的部分
     */
    public function back_data(){
        Common::loadModel('HoutaiModel');
        $outf = HoutaiModel::get_huodong_list($this->uid,$this->hd_id);
        Master::back_data($this->uid,'huodonglist','all',$outf,true);
    }
    /*
     * 累计每日登陆兼容
     */
    public function do_check(){
        //每日登陆做兼容,登陆至少要有一次
        $Act89Model = Master::getAct89($this->uid);
        $Act89Model->do_check();
    }

}




