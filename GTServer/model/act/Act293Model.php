<?php
require_once "ActHDBaseModel.php";

/*
 * 活动293
 */
class Act293Model extends ActHDBaseModel
{
    public $atype = 293;//活动编号
    public $comment = "寻宝大冒险";
    public $b_mol = "xbhuodong";//返回信息 所在模块
    public $b_ctrl = "xunbao";//子类配置
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_293';//活动配置文件关键字
    protected $_rank_id = 293;

    protected $yid = 0;  //摇出来的id
    protected $skill = 0;  //使用的技能

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'cons' => 1, //位于格子id
        'quan' => 0, //已经转过的圈数
        'rwd' => array(),  //已领取的档次
        'squan' => array(), //记录圈数触发的技能次数   圈数 => 格式id => 触发次数
        'czge' => array(), //一圈内踩中的格子,以圈数未下标
        'num' => 0, //剩余骰子个数
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

        //每日重置,获得骰子
        $this->outf['cfg']['touzi'] =  array();
        $Act87Model = Master::getAct87($this->uid);
        foreach($hd_cfg['touzi'] as $value){
            $has = $Act87Model->get_num($value['type']);
            $isGet = $Act87Model->check($value['id'],$value['type'],$value['max']);
            $value['has'] = $has;  //当前拥有
            $value['isGet'] = $isGet;  //是否已领取 0:不可领取 1:可领取 2:已领取
            $this->outf['cfg']['touzi'][] = $value;
        }

        //格子配置
        $this->outf['cfg']['gezi'] =  array();
        $this->outf['cfg']['gezi']['min'] = $hd_cfg['gezi']['min'];
        $this->outf['cfg']['gezi']['max'] = $hd_cfg['gezi']['max'];
        foreach ($hd_cfg['gezi']['list'] as $v){
            //是不是踩中
            $isGet = 0;
            if( !empty($this->info['czge'][$this->info['quan']])
                && in_array($v['id'],$this->info['czge'][$this->info['quan']])){
                $isGet = 1;
            }
            $this->outf['cfg']['gezi']['list'][] = array(
                'id' => $v['id'],
                'items' => $v['items'],
                'type' => empty($v['type'])?0:$v['type'],
                'sid' => $v['skill']['sid'],
                'isGet' => $isGet
            );
        }

        //通过圈数奖励配置
        $this->outf['cfg']['rwd'] = array();
        foreach($hd_cfg['rwd'] as $rv ){
            $isGet = 0;
            if( !empty($this->info['rwd']) && in_array($rv['id'],$this->info['rwd'])){
                $isGet = 2;
            }else{
                if($this->info['quan'] >= $rv['need']){
                    $isGet = 1;
                }
            }
            $rv['isGet'] = $isGet;  //0:不可领取 1:可领取 2:已领取
            $this->outf['cfg']['rwd'][] = $rv;
        }

        $this->outf['yao'] = $this->yid;//摇出的点数
        $this->outf['sid'] = $this->skill;//使用的技能
        $this->outf['cons'] = $this->info['cons']; //当前位于哪个格子id
        $this->outf['quan'] = $this->info['quan'];//已经转过的圈数
        $this->outf['num'] = $this->info['num'];//拥有的骰子数
    }

    /**
     * 摇骰子
     */
    public function run(){

        //删除道具-骰子
        Master::sub_item($this->uid,11,331,1);
        //获取点数
        $this->yid = rand(1,6);

        //初始格子
        $sid = $this->info['cons'];
        //最终格子
        $this->info['cons'] = $sid + $this->yid;

        $max = $this->outf['cfg']['gezi']['max'];
        $list = self::get_list();

        //计算点数和圈数
        if($this->info['cons'] > $max){
            $this->info['quan'] += 1;
            $this->info['cons'] -= $max;
        }

        //获得的技能
        $skillId = empty($list[$this->info['cons']]['skill']['sid'])?0:$list[$this->info['cons']]['skill']['sid'];

        switch ($skillId){
            case 1: //黑洞
                $this->info['cons'] = $list[$this->info['cons']]['skill']['nextid'];
                $this->skill = $list[$this->info['cons']]['skill']['sid'];
                //重新计算点数和圈数
                if($this->info['cons'] > $max){
                    $this->info['quan'] += 1;
                    $this->info['cons'] -= $max;
                }
                break;
            default:
                break;
        }

        //获取我的阵法属性
        $team = Master::get_team($this->uid);
        //奖励
        if(!empty($list[$this->info['cons']]['items']) ){
            //没踩过的
            if( empty($this->info['czge'][$this->info['quan']])
                || !in_array($this->info['cons'],$this->info['czge'][$this->info['quan']])){
                $items = Game::auto_count($list[$this->info['cons']]['items'],$team['allep']);
                Master::add_item2($items);
            }
        }
        //记录踩中的格子
        $this->info['czge'][$this->info['quan']][] = $this->info['cons'];
        $this->save();
    }

    /*
     * 格子列表(谢谢惠顾不计) 以格子id未下标
     */
    public function get_list(){
        $list = array();
        foreach($this->hd_cfg['gezi']['list'] as $v){
            $list[$v['id']] = $v;
        }
        return $list;
    }

    /*
     * 返回   寻宝大冒险 - 格子进度+奖励
     */
    public function back_data_gezi_u(){
        $data = array();

        $data['cfg']['rwd'] = $this->outf['cfg']['rwd'];
        $data['cfg']['gezi']['list'] = $this->outf['cfg']['gezi']['list'];
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);

        self::back_data_run_u();

    }

    /*
     * 返回活动详细信息
     */
    public function back_data_hd(){
        if( empty($this->outf) ){
            $this->outf['cfg'] = array();
        }
        $data['cfg'] = $this->outf['cfg'];
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data);
        self::back_data_run_u();
    }


    /*
     * 返回   寻宝大冒险 - 格子进度+奖励
     */
    public function back_data_run_u(){

        $data1 = array();
        $data1['yao'] = $this->yid;//摇出的点数
        $data1['sid'] = $this->skill;//使用的技能
        $data1['cons'] = $this->info['cons']; //当前位于哪个格子id
        $data1['quan'] = $this->info['quan'];//已经转过的圈数
        $data1['num'] = $this->info['num'];//拥有的骰子数

        Master::back_data($this->uid,$this->b_mol,'run',$data1);
        Common::loadModel('HoutaiModel');
        $outf = HoutaiModel::get_huodong_list($this->uid,$this->hd_id);
        Master::back_data($this->uid,'huodonglist','all',$outf,true);
    }

    /*
     * 任务
     * @param $id  任务类型id
     * @param $num 任务数量
     */
    public function get_touzi_task($id,$num){
        //判断活动是否还持续中
        if( parent::get_state() != 1 ){
            return 0;
        }
        $Act87Model = Master::getAct87($this->uid);
        $Act87Model->add_task($id,$num);

        $this->make_out();//重置
        $new = $this->get_news();
        if($new == 1){
            Common::loadModel('HoutaiModel');
            $outf = HoutaiModel::get_huodong_list($this->uid,$this->hd_id);
            Master::back_data($this->uid,'huodonglist','all',$outf,true);
        }
    }

    /*
    * 领取任务奖励
     * $id : 要领取的档次id
    */
    public function get_touzi_rwd($id){
        foreach($this->outf['cfg']['touzi'] as $value ){
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
            $Act87Model = Master::getAct87($this->uid);
            $Act87Model->add_get($id);
            //加道具
            Master::add_item2($value['items']);
            return true;
        }
        Master::error(PARAMS_ERROR.$id);  //参数错误
    }

    /*
    * 领取圈数奖励
     * $id : 要领取的档次id
    */
    public function get_quan_rwd($id){
        foreach($this->outf['cfg']['rwd'] as $value ){
            if($value['id'] != $id){
                continue;
            }
            if($value['isGet'] == 0){
                Master::error(ACTHD_NO_RECEIVE);  //不能领取
            }
            if($value['isGet'] == 2){
                Master::error(DAILY_IS_RECEIVE);  //已领取
            }
            Master::add_item3($value['items']);
            $this->info['rwd'][] = $id;
            $this->save();
            return true;
        }
        Master::error(PARAMS_ERROR.$id);  //参数错误
    }

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取
        if( parent::get_state() == 1 ){
            //判断是否有塞子或者奖励
            if($this->info['num'] > 0){
                $news = 1;
            }else{
                if(!empty($this->outf['cfg']['rwd'])){
                    foreach ($this->outf['cfg']['rwd'] as $rwd){
                        if($rwd['isGet'] == 1){//有未领取的奖励
                            $news = 1;
                            break;
                        }
                    }
                }
                if(!empty($this->outf['cfg']['touzi'])){
                    foreach ($this->outf['cfg']['touzi'] as $rwd){
                        if($rwd['isGet'] == 1){//有未领取的奖励
                            $news = 1;
                            break;
                        }
                    }
                }
            }

        }

        if(parent::get_state() == 2 && !empty($this->outf['cfg']['rwd'])){
            foreach ($this->outf['cfg']['rwd'] as $rwd){
                if($rwd['isGet'] == 1){//有未领取的奖励
                    $news = 1;
                    break;
                }
            }
        }
        return $news;
    }


    /*
     * 返回活动圈数领取奖励模块
     */
    public function back_data_rwd_u(){
        $data = array();
        $data['cfg']['rwd'] = $this->outf['cfg']['rwd'];
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);

        self::back_data_run_u();

    }
    /*
     * 返回  双旦-寻宝大冒险 - 领取每日重置任务
     */
    public function back_data_touzi_u(){
        $this->make_out();
        $data = array();
        $data['cfg']['touzi'] = $this->outf['cfg']['touzi'];
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);

        self::back_data_run_u();

    }

    /**
     * 资源消耗
     * @param $num
     */
    public function add($num){
        if( self::get_state() == 1 ){
            $this->info['num'] += $num;
            $this->save();

            self::back_data_run_u();
            
            Game::cmd_flow(6,331,$num,$this->info['num']);
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
        $this->info['num'] -= $num;
        if($this->info['num'] < 0){
            Master::error(ITEMS_NUMBER_SHORT);
        }
        $this->save();

        Game::cmd_flow(6,331,-$num,$this->info['num']);
    }

    /*
     * 返回活动信息--保存时不返回信息
     * 只返回当前活动在生效列表中对应的部分
     */
    public function back_data(){

    }


    /*
     * 累计每日登陆兼容
     */
    public function do_check(){
        //每日登陆做兼容,登陆至少要有一次
        $Act87Model = Master::getAct87($this->uid);
        $Act87Model->do_check();
    }


}




