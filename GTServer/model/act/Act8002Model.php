<?php
require_once "ActHDBaseModel.php";

/*
 * 凰之历练
 */
class Act8002Model extends ActHDBaseModel
{
    public $atype = 8002;//活动编号
    public $comment = "凰之历练";
    public $b_mol = "Professional";//返回信息 所在模块
    public $b_ctrl = "act";//子类配置
    public $hd_id = 'huodong_8002';//活动配置文件关键字-编号
    public $data = array();        //act数据返回
    public $sjRwd = array(
            'first'=> 200,
            'second'=> 995,
            'thirdly'=> 1000,

    );

    /*
     * 初始化结构体
     */
    public $_init = array(
        'cons'      => 0,        //积分
        'place'     => 1,        //地点ID
        'site'      => array(),  //停留过的点
        'shop'      => array(),  //商城购买信息
        'event'      => array(),  //商品权重
        'exchange'  => array(),  //兑换信息
    );

    /*
	 * 构造输出结构体
	 */
    public function make_out(){
        //默认输出直接等于内部存储数据
        $this->outf = $this->info;
    }

    /*
     * 行驶
     * num 道具数量
     * */
    public function play($num){
        //活动未开启
        if (self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //活动结束，展示中
        if (self::get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }
        //次数限定（1次或10次）
        if (!in_array($num,array(1,10))){
            Master::error(PARAMS_ERROR);
        }
        //扣除道具
        Master::sub_item($this->uid,KIND_ITEM,$this->hd_cfg['need'],$num);
        //判断一次或10次
        if ($num == 1){
            $this->_loadData(1);
        }else{
            for ($i=0;$i<$num;$i++){
                $this->_loadData($num);
            }
        };
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->data);

    }

    /*
     * 摇一次数据
     * */
    public function _loadData($num=1){
        //获取文件配置
        $map = Game::getcfg('reqiqiu');
        //到点重置
        $max = array_search(max($map),$map);
        if ($this->info['place'] == $max){
            $this->info['place'] = 1;
        }
        //行驶距离
        $step = rand(1,3);
        //积分等于距离
        $score = $step;
        //地图定位
        $this->info['place'] += $step;
        //到达终点
        if ($this->info['place'] >= $max){
            $this->info['place'] = $max;
            $score = $step + 9;//到达终点添加积分
            //记录日志
            $Sev8002Model = Master::getSev8002($this->hd_cfg['info']['id']);
            $Sev8002Model->add($this->uid);
            $Sev8002Model->bake_data();
        }
        //事件奖励
        $cfg = $map[$this->info['place']];
        $type = $cfg['type'];
        $arr = array();
        if ($type == 2){
            //触发事件 - 随机奖励

            $randMax = 0;
            $event = $this->hd_cfg["event"];
            for ($i=0; $i < count($event); $i++) {
                $randMax += intval($event[$i]["prob"]);
            }

            $rand = rand(1,$randMax);
            for ($i=0; $i < count($event); $i++) {
                $randNow += intval($event[$i]["prob"]);
                if ($randNow >= $rand) {

                    $tid = $event[$i]["id"];
                    $arr[] = $event[$i]["item"];
                    break;
                }
            }
        }
        Master::add_item3($arr);
        //基础奖励
        Master::add_item($this->uid,KIND_ITEM,1050,$step);
        //排行榜
        $Redis8002Model = Master::getRedis8002($this->hd_cfg['info']['id']);
        $Redis8002Model->zIncrBy($this->uid,$score);
        //停留记录
        $this->info['site'][] = $this->info['place'];
        //积分
        $this->info['cons'] += $score;
        $this->save();
        //数据返回
        $data = array();
        $data['place'] = $this->info['place'];
        $data['type'] = $type;
        $data['add'] = $score;
        $data['cons'] = $this->info['cons'];
        $data['tid'] = $tid;
        $this->data[] = $data;

    }


    /*
     * 商城 - 添加
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
        unset($hd_cfg['info']['no']);
        unset($hd_cfg['shop']);
        unset($hd_cfg['event']);
        unset($hd_cfg['exchange']);
        Master::back_data($this->uid,$this->b_mol,'cfg',$hd_cfg);
        //基本信息
        $act = array();
        $act['place'] = $this->info['place'];
        $act['cons'] = $this->info['cons'];
        $act['type'] = 0;
        $act['add'] = 0;
        $act['tid'] = 0;
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,array($act));
        //获取商城列表
        Master::back_data($this->uid,$this->b_mol,'shop',$this->back_data_shop());
        //兑换商城
        Master::back_data($this->uid,$this->b_mol,'exchange',$this->back_data_exchange());
        //通关日志
        $Sev8002Model = Master::getSev8002($this->hd_cfg['info']['id']);
        $Sev8002Model->bake_data();
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
            //满足凰之历练行驶
            $need = $this->hd_cfg['need'];
            $ItemModel = Master::getItem($this->uid);
            if(!empty($ItemModel->info[$need]['count'])){
                return 1; //可以领取
            }
            //满足兑换条件
            foreach ($this->hd_cfg['exchange'] as $k => $v){
                $need = $v['items'][0]['id'];
                $count = $v['items'][0]['count'];
                if ($ItemModel->info[$need]['count'] >= $count){
                    return 1;
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
        $Redis8002Model = Master::getRedis8002($this->hd_cfg['info']['id']);
        $Redis8002Model->back_data();
        $Redis8002Model->back_data_my($this->uid);
    }

    /*
     * 活动信息
     * */
    public function back_data_allhd() {
        $this->data_out();
    }

}
