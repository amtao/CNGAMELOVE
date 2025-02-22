<?php
require_once "ActHDBaseModel.php";

/*
 * 端午节活动
 */
class Act6230Model extends ActHDBaseModel
{
    public $atype = 6230;//活动编号
    public $comment = "端午节";
    public $b_mol = "dragonBoat";//返回信息 所在模块
    public $b_ctrl = "act";//子类配置
    public $hd_id = 'huodong_6230';//活动配置文件关键字-编号
    public $sjRwd = array(
                        'zongzi'=> 8000,
                        'xionghuang'=> array(//雄黄概率
                                            array('count'=>1,'prob'=>4000),
                                            array('count'=>2,'prob'=>3500),
                                            array('count'=>3,'prob'=>2500),
                                        )
                    );
    public $data = array();        //act数据返回

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'cons'      => 0,        //积分
        'place'     => 1,        //地点ID
        'lun'       => 0,        //第几轮
        'site'      => array(),  //停留过的点
        'shop'      => array(),  //商城购买信息
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
     * 摇骰子
     * id  道具id
     * num 道具数量
     * */
    public function play($num){
        //活动已结束
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //活动结算阶段
        if($this->get_state() == 2){
            Master::error(ACTHD_SETTLEMENT);
        }
        //次数限制
        if (!in_array($num,array(1,10))){
            Master::error(PARAMS_ERROR);
        }
        //减去使用的道具
        Master::sub_item($this->uid,KIND_ITEM,$this->hd_cfg['need'],$num);
        //地图配置
        $map = Game::getcfg('activityduanwu_move');
        //终点重置
        $max = array_search(max($map),$map);
        if ($this->info['place'] == $max){
            $this->info['place'] = 1;
        }
        //前进距离=积分
        $step = $this->_randNumber($num);
        $score =  $step;
        //当前位置
        $this->info['place'] += $step;
        //到达终点
        if ($this->info['place'] >= $max){
            $this->info['place'] = $max;    //终点定位
            $this->info['lun'] += 1;        //第几轮
            $score = $step + 49;            //终点增加积分
            //抵达终点记录日志
            $Sev6230Model = Master::getSev6230($this->hd_cfg['info']['id']);
            $Sev6230Model->add($this->uid);
            $Sev6230Model->bake_data();
        }
        $this->data['place'] = $this->info['place'];
        //当前位置配置信息
        $cfg = $map[$this->info['place']];
        //事件类型
        $type = $cfg['dwm_type'];
        //奖励发放
        $this->_award($this->info['place'],$type,$num);
        //基础奖励
        Master::add_item($this->uid,KIND_ITEM,1027,$num);
        //排行榜
        $Redis6230Model = Master::getRedis6230($this->hd_cfg['info']['id']);
        $Redis6230Model->zIncrBy($this->uid,$score);

        //停留点记录
        $this->info['site'][] = $this->info['place'];
        //积分
        $this->info['cons'] += $score;
        $this->save();
        //数据返回
        $this->data['type'] = $type;
        $this->data['add'] = $score;
        $this->data['cons'] = $this->info['cons'];
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->data);
    }

    /*
     * 发放随机奖励和随机事件
     * $place 当前位置
     * $type  事件类型
     * $num   道具数量
     * */
    private function _award($place,$type,$num = 1)
    {
        //随机奖励配置信息
        $rwd_cfg = Game::getcfg('activityduanwu_reward');
        $rwd_arr = array();
        //触发随机事件
        if ($type == 3){
            //随机 随机事件
            $rand = rand(1,4);
            if ($this->info['place'] > 20 || $this->info['place'] < 100)$rand=rand(3,4);
            $this->data['tid'] = $rand;
            switch ($rand){
                case 1://免费获得一次划桨机会
                    $rwd_arr[] = array('id'=>$this->hd_cfg['need'],'kind'=>1,'count'=>1);
                    break;
                case 2://获得多一次奖励
                    $num += 1;
                    break;
                case 3://前进10单位
                    $this->info['place'] += 10;
                    $this->data['eventPlace'] = $this->info['place'];
                    break;
                case 4://退后10单位
                    $this->info['place'] -= 10;
                    $this->data['eventPlace'] = $this->info['place'];
                    break;
            }
        }
        //匹配档次
        $rinfo = array();
        foreach ($rwd_cfg as $v){
            if ($place <= $v['dwr_poz']){
                $rinfo = $v['dwr_item'];
                break;
            }
        }
        //随机奖励
        $zongzi = 0;//粽子个数
        $xionghuang = 0;//雄黄个数
        for ($i=0;$i<$num;$i++){
            //随机奖励
            $key = Game::get_rand_key1($rinfo,'prob');
            $item = $rinfo[$key];
            $rwd_arr[] = $item;
            //随机粽子奖励
            if (rand(1,10000)<=$this->sjRwd['zongzi'])$zongzi += 1;
            //随机雄黄奖励
            $k = Game::get_rand_key1($this->sjRwd['xionghuang'],'prob');
            $xionghuang += $this->sjRwd['xionghuang'][$k]['count'];
        }
        if ($zongzi!=0)$rwd_arr[] = array('id'=>1028,'kind'=>1,'count'=>$zongzi);       //粽子
        $rwd_arr[] = array('id'=>1029,'kind'=>1,'count'=>$xionghuang);//雄黄
        Master::add_item3($rwd_arr);
    }


    /*
     * 随机距离
     *
     * */
    private function _randNumber($num = 1)
    {
        $total = 0;
        for ($i=0;$i<$num;$i++){
            $total += rand(1,5);
        }
        return $total;
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
        unset($hd_cfg['exchange']);
        Master::back_data($this->uid,$this->b_mol,'cfg',$hd_cfg);
        //基本信息
        $act = array();
        $act['place'] = $this->info['place'];
        $act['cons'] = $this->info['cons'];
        $act['type'] = 0;
        $act['add'] = 0;
        $act['tid'] = 0;
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$act);
        //获取商城列表
        Master::back_data($this->uid,$this->b_mol,'shop',$this->back_data_shop());
        //兑换商城
        Master::back_data($this->uid,$this->b_mol,'exchange',$this->back_data_exchange());
        //通关日志
        $Sev6230Model = Master::getSev6230($this->hd_cfg['info']['id']);
        $Sev6230Model->bake_data();
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
            //满足划龙舟
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
        $Redis6230Model = Master::getRedis6230($this->hd_cfg['info']['id']);
        $Redis6230Model->back_data();
        $Redis6230Model->back_data_my($this->uid);
    }

    /*
     * 活动信息
     * */
    public function back_data_allhd() {
        $this->data_out();
    }

}
