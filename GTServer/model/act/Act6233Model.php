<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6233
 */
class Act6233Model extends ActHDBaseModel
{
    public $atype = 6233;//活动编号
    public $comment = "四大藩王";
    public $b_mol = "sidafanwanghd";//返回信息 所在模块
    public $b_ctrl = "fanwang";//子类配置
    public $hd_id = 'huodong_6233';//活动配置文件关键字

    /*
     * 初始化结构体
     */
    public $_init =  array(
    );

    /*
     * 获得奖励
     * $id  兑换门客id
     */
    public function get_rwd($id = 0){
        //活动是否开去
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //是否拥有该门客
        $HeroModel = Master::getHero($this->uid);
        if (isset($HeroModel->info[$id])){
            Master::error(HERO_HAVEED);
        }
        //获取id
        $heroid = $id > 200?0:$id;
        $findRwd = null;
        foreach($this->hd_cfg['rwd'] as $rwd) {
            //判断id不等于0且等于英雄id
            if ($heroid != 0 && $rwd['heroid'] == $heroid) {
                $findRwd = $rwd;
                break;
            }
        }
        //参数为空，或兑换数量为空
        if(empty($findRwd) || empty($findRwd['need'])){
            Master::error(PARAMS_ERROR);
        }
        //判断道具是否充足
        $ItemModel = Master::getItem($this->uid);
        if (!$ItemModel->sub_item($findRwd['itemid'], $findRwd['need'],true)){
            return;
        }
        //加伙伴
        if ($heroid != 0){
            Master::add_item($this->uid,KIND_HERO,$heroid);
        }
        //扣除道具
        $ItemModel->sub_item($findRwd['itemid'], $findRwd['need']);
        $this->info[$id] =1;
        $this->make_out();
        $this->back_data_hd();
    }

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0;//不可领取
        $ItemModel = Master::getItem($this->uid);
        $HeroModel = Master::getHero($this->uid);
        foreach($this->hd_cfg['rwd'] as $rwd) {
            //验证道具是否充足
            if ($rwd['heroid'] != 0) {
                $flag = $ItemModel->sub_item($rwd['itemid'], $rwd['need'], true);
                $hero_info = $HeroModel->check_info($rwd['heroid'], true);
                if ($flag && !$hero_info) {
                    return 1; //可领取
                }
            }
        }
        return $news;
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
        $info = $this->info;
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        $hd_cfg['info']['news'] = $this->get_news();
        foreach ($hd_cfg['rwd'] as $k=>$v){
                $heroid = $v['heroid'];
                if (!empty($info[$heroid])){
                    $hd_cfg['rwd'][$k]['open']= 0;
                }else{
                    $hd_cfg['rwd'][$k]['open']= 1;
                }
        }
        $this->outf = $hd_cfg;

    }








}

