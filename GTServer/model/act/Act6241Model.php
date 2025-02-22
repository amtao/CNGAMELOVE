<?php
require_once "ActHDBaseModel.php";

/*
 * 七夕活动
 */
class Act6241Model extends ActHDBaseModel
{
    public $atype = 6241;//活动编号
    public $comment = "七夕";
    public $b_mol = "sevenDays";//返回信息 所在模块
    public $b_ctrl = "cfg";//子类配置
    public $hd_id = 'huodong_6241';//活动配置文件关键字-编号

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'cons'      => array(),  //积分
        'get'       => array(),  //领奖状态
        'shop'      => array(),  //商城购买信息
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
        unset($hd_cfg['list']);
        unset($hd_cfg['shop']);
        unset($hd_cfg['exchange']);
        unset($hd_cfg['info']['no']);
        foreach ($hd_cfg['rwds'] as $k => $v){
            $hid = $v['hid'];
            $hd_cfg['rwds'][$k]['cons'] = empty($this->info['cons'][$hid])?0:$this->info['cons'][$hid];
            foreach ($v['rwd'] as $x => $r){
                $id = $r['id'];
                if (empty($this->info['get'][$hid][$id])){
                    $hd_cfg['rwds'][$k]['rwd'][$x]['get'] = 0;
                }else{
                    $hd_cfg['rwds'][$k]['rwd'][$x]['get'] = 1;
                }
            }
        }
        $hd_cfg['cons'] = $this->info['cons'];
        $this->outf = $hd_cfg;
    }

    /*
     * 摇骰子
     * id  道具id
     * num 道具数量
     * */
    public function play($num=1){
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
        $heros =$this->get_heros();
        if (empty($heros)){
            Master::error(PARAMS_ERROR);
        }
        $list  = $this->hd_cfg['list'];
        $score = array();
        $jiban = array();
        $data  = array();
        $allrwd   = array();
        $qixi_cfg = Game::getcfg('qixi_reward');
        for ($i = 0;$i < $num;$i++){
            $key = Game::get_rand_key1($list,'prob');
            $allrwd[] = $list[$key];
            $rand = rand(1,3);
            $rwd =  $qixi_cfg[$rand];
            $hid = $heros[array_rand($heros)];
            $rwward = rand(1,4);
            if (empty($jiban[$hid]))$jiban[$hid] = 0;
            if (empty($score[$hid]))$score[$hid] = 0;
            $jiban[$hid] += $rwd['jiban'];
            $score[$hid] += $rwd['qingyuan'];
            $allrwd[] =  array('id'=>1036, 'kind' => 1, 'count'=>$rwd['qingyuan']);//固定奖励
            $data['draw'][] = array('id'=>$rwward,'hid'=>$hid,'type'=>$rand);

        }

        //领取奖励
        Master::add_item3($allrwd);
        foreach ($jiban as $id=>$val){
            Master::add_item($this->uid,92,$id,$val);
        }
        $total = array_sum($score);
        //排行榜
        $Redis6241Model = Master::getRedis6241($this->hd_cfg['info']['id']);
        $Redis6241Model->zIncrBy($this->uid,$total);

        foreach ($score as $k=>$v){
            if (empty($this->info['cons'][$k]))$this->info['cons'][$k] = 0;
            $this->info['cons'][$k] += $v;
            $data['add'][] = array('id' => $k,'cons' => $v);
        }
        $this->save();

        //数据返回
        Master::back_data($this->uid,$this->b_mol,'act',$data);
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
     * 七夕-领取奖励
     *
     * */
    public function get_hrwd($id,$hid)
    {
        //活动已结束
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        $rinfo = $this->get_rwds();
        $heros = $this->get_heros();
        $keys = array_keys($this->info['cons']);
        if (!in_array($hid,$heros) || !in_array($hid,$keys)){
            Master::error(ACT_14_CONFIGWRONG.__LINE__);
        }

        if (empty($rinfo[$hid][$id])){
            Master::error(ACT_14_CONFIGWRONG.__LINE__);
        }

        if ($this->info['cons'][$hid] < $rinfo[$hid][$id]['need']){
            Master::error(ACT_14_CONFIGWRONG.__LINE__);
        }

        if (!empty($this->info['get'][$hid][$id])){
            Master::error(HD_TYPE8_HAVE_LINGQU.__LINE__);
        }

        Master::add_item3($rinfo[$hid][$id]['items']);
        $this->info['get'][$hid][$id] = 1;
        $this->save();
    }

    /*
     * 获取皇子id集合
     *
     * */
    public function get_heros()
    {
        foreach ($this->hd_cfg['rwds'] as $v){
            $heros[] = $v['hid'];
        }
        return $heros;
    }

    /*
	 * 组装奖励数据
     * array(
     *      皇子id=>array(
     *          档次id=>array(),对应id的档次信息
     *      ),
     * )
	 */
    public function get_rwds(){
        $hd_cfg = array();
        if (empty($this->hd_cfg['rwds']))return $hd_cfg;
        foreach ($this->hd_cfg['rwds'] as $v){
            if (isset($v['rwd']) && is_array($v['rwd'])){
                foreach ($v['rwd'] as $val){
                    $hd_cfg[$v['hid']][$val['id']] = $val;
                }
            }
        }
        return $hd_cfg;
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

            //满足领奖条件
            foreach ($this->hd_cfg['rwds'] as $k => $v){
                $hid = $v['hid'];
                foreach ($v['rwd'] as $j=>$x){
                    if ($this->info['cons'][$hid] >= $x['need'] && empty($this->info['get'][$hid][$x['id']])){
                        return 1;
                    }
                }
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
        $Redis6241Model = Master::getRedis6241($this->hd_cfg['info']['id']);
        $Redis6241Model->back_data();
        $Redis6241Model->back_data_my($this->uid);
    }

}
