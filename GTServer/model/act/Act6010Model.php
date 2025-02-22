<?php
require_once "ActHDBaseModel.php";
/*
 * 主角换装
 */
class Act6010Model extends ActHDBaseModel
{
    public $atype = 6010;//活动编号
    public $comment = "拜年";
    public $b_mol = "actboss";//返回信息 所在模块
    public $b_ctrl = "info";//子类配置
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_6010';//活动配置文件关键字

    /*
     * 初始化结构体
     */
    public $_init =  array(

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
        $news = 0; //不可领取
        return $news;
    }

    /*
	 * 战斗
	 * hit($hid);
	 */
    public function hit($hid, $type){
        if( self::get_state() == 0 ){
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }

        if (Game::day_0(9) > Game::get_now()){
            Master::error(SHOP_ACTIVITY_UNOPEN);
        }

        $Sev6010Model = Master::getSev6010();
        if (!$Sev6010Model->in_fight()){
            Master::error(GAME_LEVER_PLAY_END);
        }

        //距离上次打的时间
        if ($_SERVER['REQUEST_TIME'] - $this->info['htime'] < 10){
            Master::error(GAME_LEVER_PLAY_END);
        }

        $HeroModel = Master::getHero($this->uid);
        //门客存在
        $HeroModel->check_info($hid);

        //门客出战列表
        $Act6011Model = Master::getAct6011($this->uid);
        //这个门客 是不是可以出战(活的)
        $per = $Act6011Model->go_fight($hid, $type);

        //获取阵法信息
        $TeamModel  = Master::getTeam($this->uid);
        //英雄伤害值
        $hero_damage = $TeamModel->getHeroDamage($hid, 3) * $per;
        //BOSS扣血
        $Sev6010Model->hit($hero_damage);


        //获取奖励
        $hd_cfg = $this->hd_cfg;
        if (!empty($hd_cfg['hitrwd'])){
            if (!empty($hd_cfg['hitrwd']['rwds'])){
                Master::add_item3($hd_cfg['hitrwd']['rwds']);
            }
            else if (!empty($hd_cfg['hitrwd']['prop_rwds'])){
                Master::add_rwd_singe($this->uid, $hd_cfg['hitrwd']['prop_rwds'], 'prop', 1, 10000);
            }
        }

        //伤血排行更新
        $Redis6010Model = Master::getRedis6010($this->_get_day_redis_id());
        $Redis6010Model->zIncrBy($this->uid,$hero_damage);//加上伤害血量
        $Redis6010Model->back_data();
        $Redis6010Model->back_data_my($this->uid);

        //弹窗信息
        Master::back_win("actboss","hit","damage",$hero_damage);

        $this->save();
    }

    /*
	 * 使用出战令
	 */
    public function comeback($hid){
        if( self::get_state() == 0 ){
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }
        //是否战斗中
        $Sev6010Model = Master::getSev6010();
        if (!$Sev6010Model->in_fight()){
            Master::error(GAME_LEVER_PLAY_END);
        }

        //门客出战列表
        $Act6011Model = Master::getAct6011($this->uid);
        //恢复出战
        $Act6011Model->cone_back($hid);
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

        $hd_cfg['info']['news'] = $this->get_news();
        $this->outf = $hd_cfg;
    }

}














