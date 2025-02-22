<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动6181
 */
class Act6181Model extends ActHDBaseModel
{
	public $atype = 6181;//活动编号
	public $comment = "皇子累充解锁";
    public $b_mol = "jshuodong";//返回信息 所在模块
	public $b_ctrl = "unlock";//子类配置
	public $hd_id = 'huodong_6181';//活动配置文件关键字

    /*
 * 初始化结构体
 * 累计数量
 * 领奖档次
 */
    public $_init =  array(
        'cons' => 0,  //已消耗(完成)量
        'get' => 0,   //已领取的档次
    );


    /**
     * 资源消耗
     * @param $num
     */
    public function add($num){
        if( self::get_state() == 1 ){
            $this->info['cons'] += $num;
            $this->save();
        }
    }

    /**
     * 获得奖励
     */
    public function get_rwd($id = 0){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        $HeroModel = Master::getHero($this->uid);
        if (isset($HeroModel->info[$id])){
            Master::error(HERO_HAVEED);
        }
        if (!empty($this->info['get'])){
            Master::error(ACTHD_NO_RECEIVE);
        }
        if ($this->info['cons'] < $this->hd_cfg['need']){
            Master::error(ACTHD_NO_RECEIVE);
        }
        if (!isset($this->info['type'])){
            $this->info['type'] = $this->checkHero()?0:1;
        }
        $this->info['get'] = empty($this->info['type'])?1:$id;
        if ($this->checkHero()){
            Master::add_item($this->uid,KIND_ITEM,$this->hd_cfg['rwd']['id'],$this->hd_cfg['rwd']['count']);
        }else{
            Master::add_item($this->uid,KIND_HERO,$id);
        }
        $this->save();
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
            if (empty($this->info['get'])){
                if(!empty($this->hd_cfg) && $this->info['cons'] >= $this->hd_cfg['need']){
                    $news = 1; //可以领取
                }
            }


        }
        return $news;
    }

    public function checkHero(){
        if (isset($this->info['type'])){
            return $this->info['type']==1?false:true;
        }
        $HeroModel = Master::getHero($this->uid);
        $heros = array_keys($HeroModel->info);
        $rwds = $this->hd_cfg['heros'];
        foreach ($rwds as $b){
            if (!in_array($b,$heros)){
                return false;
            }
        }
        return true;
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
        $this->outf['cfg'] = $hd_cfg;  //活动期间花费多少元宝
        $this->outf['cons'] = $this->info['cons'];  //活动期间花费多少元宝
        $this->outf['get'] = $this->info['get'];  //领取到的档次
        $this->outf['type'] = $this->checkHero()?0:1;
    }








}

