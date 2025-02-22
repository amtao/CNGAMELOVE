<?php
require_once "ActHDBaseModel.php";

/*
 * 活动6123
 */
class Act6123Model extends ActHDBaseModel
{
    public $atype = 6123;//活动编号
    public $comment = "盛装出席";
    public $b_mol = "clothepve";//返回信息 所在模块
    public $b_ctrl = "info";//子类配置
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_6123';//活动配置文件关键字


    /*
     * 初始化结构体
     */
    public $_init =  array(
        'use' => 0,
        'buy' => 0,
        'score' => 0,
        'lastTime' => 0,
        'gate' => 0,
        'buys' => array(),
        'scores' => array(),
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

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $state = self::get_state();
        if( $state  == 0 || $state == 2){
            return 0;
        }
        $this->updateLast();
        $news = 0; //不可领取
        $info = $this->info;
        if ($this->hd_cfg['count'] > $info['use']){
            $news = 1;
        }
        return $news;
    }

    private function updateLast(){
        $state = self::get_state();
        if($state == 0){
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }

        if ($this->info['lastTime'] < Game::day_0()){
            $this->info['use'] = $this->info['use'] < 0?$this->info['use']:0;
            $this->info['buy'] = 0;
            $this->info['lastTime'] = Game::get_now();
            $this->info['buys'] = array();
        }
    }

    private function getBase(){
        $data = array();
        $data['use'] = $this->info['use'];
        $data['buy'] = $this->info['buy'];
        $data['score'] = $this->info['score'];
        $data['lastTime'] = $this->info['lastTime'];
        $data['gate'] = $this->info['gate'];
        return $data;
    }

    private function getScores(){
        $data = array();
        if (empty($this->info['scores'])){
            return $data;
        }
        foreach ($this->info['scores'] as $item){
            $data[] = $item;
        }
        return $data;
    }

    public function add($item, $count){
        $this->info['score'] = $this->info['score'] + $count;
        $this->save();

        $Redis6123Model = Master::getRedis6123($this->hd_cfg['info']['id']);
        $Redis6123Model->zAdd($this->uid,$this->info['score']);
    }

    public function addCount($count = 1){
        if ($count <= 0)return;
        $state = self::get_state();
        if( $state  == 0 || $state == 2){
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }

        $this->updateLast();
        $this->info['use'] -= $count;
        $this->info['buy'] += 1;

        Master::sub_item($this->uid, KIND_ITEM, 1, Game::getcfg_param("clothe_cost"));

        $this->save();
        Master::back_data($this->uid,$this->b_mol,'base', $this->getBase());
    }

    private function addTag($score, $id, $part){
        if ($id == 0)return $score;
        $sys = Game::getcfg_info("use_clothe", $id);
        if (empty($sys) ||  $sys['part'] != $part || empty($sys['tag']))return $score;
        foreach ($sys['tag'] as $tag){
            $score[$tag['tag']] += $tag['score'];
        }
        return $score;
    }

    public function fight($head, $body, $ear, $bg, $eff, $ani, $id){

        $state = self::get_state();
        if( $state  == 0 || $state == 2){
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }

        if ($id > $this->info['gate'] + 1){
            Master::error(CLOTHE_PVE_OPEN_LIMI);
        }

        $Act6140Model = Master::getAct6140($this->uid);
        $score = array( 1=> 0,2=> 0,3=> 0,4=> 0,5=> 0,6=> 0,);
        if ($Act6140Model->isUnlock($head)){
            $score = $this->addTag($score, $head, 1);
        }

        if ($Act6140Model->isUnlock($body) ){
            $score = $this->addTag($score, $body, 2);
        }

        if ($Act6140Model->isUnlock($ear)){
            $score = $this->addTag($score, $ear, 3);
        }

        if ($Act6140Model->isUnlock($bg) ){
            $score = $this->addTag($score, $bg, 4);
        }

        if ($Act6140Model->isUnlock($eff) ){
            $score = $this->addTag($score, $eff, 5);
        }

        if ($Act6140Model->isUnlock($ani) ){
            $score = $this->addTag($score, $ani, 6);
        }

        $hd_cfg = $this->hd_cfg;
        $s = 0;
        $isWin = false;
        foreach ($hd_cfg['gate'] as $v){
            if ($v['id'] == $id){
                $sys = Game::getcfg_info("clothepve", $v['gateid']);
                foreach ($sys['clothe_score'] as $tag){
                    $s += ceil($score[$tag['tag']] * $tag['point'] / 10000);
                }
                $isWin = $s >= $sys['win_score'];
            }
        }

        $info = array();
        $info['score'] = $s;
        $info['head'] = $head;
        $info['body'] = $body;
        $info['ear'] = $ear;
        $info['background'] = $bg;
        $info['effect'] = $eff;
        $info['animal'] = $ani;
        $info['iswin'] = $isWin?1:0;

        Master::back_data($this->uid,$this->b_mol,'win', $info);

        //刷新 20名日志表
        $info['gate'] = $id;
        unset($info['iswin']);

        if ($isWin){
            if (empty($this->info['scores'] )){
                $this->info['scores'] = array();
            }
            $s1 = empty($this->info['scores'][$id])?0:$this->info['scores'][$id]['score'];
            if ($s1 < $s){
                $this->info['scores'][$id] = $info;
                $data = array();
                $data[] = $info;
                Master::back_data($this->uid,$this->b_mol,'scores', $data);
            }

            $isAddCount = $this->info['gate'] >= $id;
            $this->info['gate'] = $this->info['gate'] < $id ?$id:$this->info['gate'];
            $this->clear($id, 1, $isAddCount);

            $info['uid'] = $this->uid;
            $Sev6123Model = Master::getSev6123($this->hd_cfg['info']['id']);
            $Sev6123Model->add_msg($info);
        }
    }

    public function clear($id, $count = 1, $isAddCount = true){
        $state = self::get_state();
        if( $state  == 0 || $state == 2){
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }

        if ($this->info['gate'] < $id){
            Master::error(CLOTHE_GATE_LIMIT);
        }

        $this->updateLast();
        if ($count <= 0)return;

        if ($isAddCount){
            if ($this->hd_cfg['count'] - $this->info['use'] - $count < 0){
                Master::error(CLOTHE_COUNT_LIMIT);
            }
            $this->info['use'] = $this->info['use'] + $count;
        }

        $hd_cfg = $this->hd_cfg;
        $dur_day = Game::day_dur($this->hd_cfg['info']['sTime']);
        $gate = null;
        foreach ($hd_cfg['gate'] as $v){
            if ($v['id'] == $id){
                $gate = $v;
                if ($v['day'] > $dur_day){
                    Master::error(CLOTHE_DAY_LIMIT);
                }
            }
        }
        if (empty($gate)){
            Master::error(COMMON_DATA_ERROR);
        }

        Master::add_rwd_singe($this->uid, $gate['prop_rwds'], 'prop', $count, 10000);
        $rwds = array();
        foreach ($gate['rwds'] as $rwd){
            $rwd['count'] = $rwd['count'] * $count;
            $rwds[] = $rwd;
        }
        Master::add_item3($rwds);
        $this->save();
        Master::back_data($this->uid,$this->b_mol,'base', $this->getBase());
    }

    /*
     * 构造输出结构体
     */
    public function make_out(){
        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }
        $this->updateLast();
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);

        if (!empty($hd_cfg['rwd'])){
            $info = $this->info;
            $rwds = array();
            foreach($hd_cfg['rwd'] as $rwd){
                $rwd['buy'] = empty($info['buys'][$rwd['id']])?0:$info['buys'][$rwd['id']];
                $rwds[] = $rwd;
            }
            $hd_cfg['rwd'] = $rwds;
        }

        $hd_cfg['info']['news'] = $this->get_news();
        $this->outf = $hd_cfg;
    }

    public function get_rwd($id = 0)
    {
        if( parent::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        $buy_count = floor($id / 10000);
        if ($buy_count <= 0)return;
        $id = $id % 10000;
        if ($buy_count == 0)Master::error();
        foreach($this->hd_cfg['rwd'] as $rwd){
            if ($rwd['id'] == $id){
                $c = empty($this->info['buys'][$id])?0:$this->info['buys'][$id];

                if ($c + $buy_count > $rwd['count']){
                    Master::error(CLUB_EXCHANGE_GOODS_MAX);
                }

                $item = $rwd['items'][0];
                $ItemModel = Master::getItem($this->uid);
                $ItemModel->sub_item($item['id'], $item['count'] * $buy_count);

                $this->info['buys'][$id] = $c + $buy_count;
                $item = $rwd['items'][1];
                $item['count'] = $item['count'] * $buy_count;
                Master::add_item2($item);
                $this->save();
                break;
            }
        }
        $this->back_data_hd();
    }

    public function back_data_hd($isSendRank = false){
        if( empty($this->outf) ){
            $this->outf = array();
        }
        $out = $this->outf;
        if (!$isSendRank){
            unset($out['rank']);
            unset($out['info']);
            unset($out['gate']);
        }
        $this->updateLast();
        Master::back_data($this->uid,$this->b_mol,'base', $this->getBase());
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$out);
        Master::back_data($this->uid,$this->b_mol,'scores', $this->getScores());
    }

}
