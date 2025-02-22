<?php
require_once "ActHDBaseModel.php";

/*
 * 活动6121
 */
class Act6121Model extends ActHDBaseModel
{
    public $atype = 6121;//活动编号
    public $comment = "天天秒杀";
    public $b_mol = "daydaybuy";//返回信息 所在模块
    public $b_ctrl = "dayday";//子类配置
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_6121';//活动配置文件关键字


    /*
     * 初始化结构体
     */
    public $_init =  array(
        'buys' => array(),
        'duihuan' => array(),
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
        $news = 0; //不可领取
        $info = $this->info;
        $count = 0;
        foreach($info['buys'] as $bcount){
            //验证道具是否充足
            $count += $bcount;
        }
        foreach($this->hd_cfg['miaosha'] as $rwd){
            //验证道具是否充足
            $id = $rwd['id'];
            if ($rwd['need'] <= $count && $info['duihuan'][$id] != 1){
                $news = 1;
                break;
            }
        }
        return $news;
    }

    /**
     * 获得奖励
     * $id 兑换的门客id
     */
    public function get_rwd($id = 0){
        if( parent::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        if ($id > 10000){
            $this->get_miaosha($id - 10000);
        }
        else {
            $this->buy_miaosha($id);
        }
    }

    private function buy_miaosha($id = 0){
        $info = $this->info;
        $dur_day = Game::day_dur($this->hd_cfg['info']['sTime']);
        $dur_day = $dur_day % 7 == 0?7:$dur_day % 7;
        foreach($this->hd_cfg['rwd'] as $rwd){
            if ($rwd['id'] == $id){
                if ($dur_day != $rwd['count']){
                    Master::error(DAYDAY_ITEM_LIMIT);
                }
                $b = !empty($info['buys'][$id])?$info['buys'][$id]:0;
                if ($b != 0)return;
                Master::sub_item($this->uid,KIND_ITEM, 1, $rwd['need']);
                $info['buys'][$id] = $b + 1;
                Master::add_item3($rwd['items']);
                $this->info = $info;
                $this->save();
                $this->back_data_hd();
                $itemId = $rwd['items'][0]['id'];
                $sysItem = Game::getcfg_info("item", $itemId);
                Game::flow_php_record($this->uid, 2, $itemId, 1, $sysItem['name'], $rwd['need']);
                break;
            }
        }
    }

    private function get_miaosha($id = 0){
        $info = $this->info;
        $count = 0;
        foreach($info['buys'] as $bcount){
            $count += $bcount;
        }
        foreach($this->hd_cfg['miaosha'] as $rwd){
            if ($rwd['id'] == $id){
                if ($rwd['need'] > $count || $info['duihuan'][$id] == 1)return;
                $info['duihuan'][$id] = 1;
                Master::add_item3($rwd['items']);
                $this->info = $info;
                $this->save();
                $this->back_data_hd();
                break;
            }
        }
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
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);

        $dur_day = Game::day_dur($hd_cfg['info']['sTime']);
        $info = $this->info;
        $dur_day = $dur_day % 7 == 0?7:$dur_day % 7;
        $rwds = array();
        foreach($hd_cfg['rwd'] as $rwd){
            if ($dur_day != $rwd['count'])continue;
            $rwd['buy'] = empty($info['buys'][$rwd['id']])?0:$info['buys'][$rwd['id']];
            $rwds[] = $rwd;
        }
        $hd_cfg['rwd'] = $rwds;

        $miaosha = array();
        foreach($hd_cfg['miaosha'] as $rwd){
            $rwd['isrwd'] = empty($info['duihuan'][$rwd['id']])?0:$info['duihuan'][$rwd['id']];
            $miaosha[] = $rwd;
        }
        $hd_cfg['miaosha'] = $miaosha;

        $count = 0;
        foreach($info['buys'] as $bcount){
            //验证道具是否充足
            $count += $bcount;
        }

        $hd_cfg['buyCount'] = $count;
        $hd_cfg['info']['news'] = $this->get_news();
        $this->outf = $hd_cfg;
    }

}
