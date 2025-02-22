<?php 
require_once "ActBaseModel.php";
/*
 * 伙伴邀约
 */

class Act730Model extends ActBaseModel{
    
    public $atype = 730;//活动编号

	public $comment = "伙伴邀约";
	public $b_mol = "hero";//返回信息 所在模块
    public $b_ctrl = "invite";//返回信息 所在控制器
    
    public $_init = array(
        'events' => array(),//随机事件
        'refreshTime' => 0,//上次刷新时间
        'joinLimitEvent' => array(),//是否进入过限时事件
    );

    
    //刷新事件，时间
    public function refreshEventTime(){
        $now = Game::get_now();
        $revival = Game::getcfg_param("game_refresh");
        if($this->info['refreshTime'] == 0 || $this->info['refreshTime'] + $revival <= $now){
            $this->info['refreshTime'] = $now;
            $this->randEvents();
            $this->info['joinLimitEvent'] = array();
        }
        $this->save();
    }

    //随机事件
    public function randEvents(){
        $this->info['events'] = array();
        $xfBuildCfg = Game::getcfg("xf_build");
        $cityArr = array();
        $UserModel = Master::getUser($this->uid);
        $bmap = $UserModel->info['bmap'];
        foreach( $xfBuildCfg as $v ){
            if( $v['lock'] <= $bmap ){
                array_push( $cityArr, $v['id']);
            }
        }
        //在解锁的城市中随机三个城市
        $results = array_rand($cityArr,3);
        $tempArr = array_diff($cityArr,$results);

        $gamesCfg = Game::getcfg("games");

        foreach($results as $cityId){
            $foodArr = array();
            $fishArr = array();
            foreach($gamesCfg as $k => $game){
                if( $game['city'] == 0 || $game['city'] == $cityId ){
                    if($game['type'] <= 2){
                        $fishArr[$k] = $game;
                    }else{
                        $foodArr[$k] = $game;
                    }
                }
            }
            $fishId = Game::get_rand_key1($fishArr,'prop');
            $foodId = Game::get_rand_key1($foodArr,'prop');
            if($cityId == 0 || $cityId == 15){
                $cityIndex = array_rand($tempArr,1);
                $cityId = $tempArr[$cityIndex];
            }
            if(empty($this->info['events'][$cityId])){
                $this->info['events'][$cityId] = array('fish' => 0,'food' => 0);
            }
            $this->info['events'][$cityId]['fish'] = $fishId;
            $this->info['events'][$cityId]['food'] = $foodId;
        }
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
