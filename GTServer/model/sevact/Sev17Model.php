<?php
/*
 * 联盟-宴会开启
 */
require_once "SevBaseModel.php";
class Sev17Model extends SevBaseModel
{
	public $comment = "联盟-开启宴会";
	public $act = 17;//活动标签

	public $stime = 0;//当天开始时间戳
	public $etime = 0; //当天结束时间戳

	public $_init = array(//初始化数据
        'startTime' => 0,//宴会开启时间
        'totalResource' => 0,//公会玩家每日提交资源
        'partyLv' => 0,//开启宴会的等级
        'joinPartyPeople' => array(),//进入宴会的玩家uid
    );

	// public function __construct($hid,$cid){
	// 	parent::__construct($hid,$cid);
	// 	//01:00:00  -  23:30:00
	// 	$this->stime = mktime(01,00,00,date("m"),date("d"),date("y"));//当天开始时间戳
	// 	$this->etime = mktime(23,30,00,date("m"),date("d"),date("y"));//当天结束时间戳
    // }

    public function getEndTime(){
        $cacluTime = $this->getStrtotime();
        $finalTime = $cacluTime + 86400;
        $this->stime = strtotime(date('Y-m-d 01:00:00',$finalTime));
        $this->etime = strtotime(date('Y-m-d 23:30:00',$finalTime));
    }

    //根据时间戳获取当天零点的时间戳
    public function getStrtotime(){
        $startData = date('Y-m-d',$this->info['startTime']);
        return strtotime($startData);
    }

    /**
	 * 判断正式宴会开启时间
	 */
	public function partyTime(){
        $this->getEndTime();

		if($_SERVER['REQUEST_TIME'] < $this->stime){
			Master::error(CLUB_PARTY_START_TIME_NOT_REACH);
		}
		if($_SERVER['REQUEST_TIME'] > $this->etime){
			Master::error(CLUB_PARTY_START_TIME_NOT_REACH);
		}
    }
    
    //判断宴会是否结束
    public function isEnd(){
        $this->getEndTime();
        if($_SERVER['REQUEST_TIME'] > $this->etime){
            return true;
        }
        return false;
    }
    
    public function addResource($num){
        if(empty($this->info['totalResource'])){
            $this->info['totalResource'] = 0;
        }
        $maxResource = Game::getcfg_param('club_partyRes');
        if($this->info['totalResource'] >= $maxResource){
            Master::error(CLUB_PARTY_MAX_RESOURCE);
        }
        if($this->info['totalResource'] + $num - $maxResource >= 0){
            $this->info['totalResource'] = $maxResource;
        }else{
            $this->info['totalResource'] += $num;
        }
        $this->save();
    }

    //设置宴会开启
    public function setPartyStart($id){
        $now = Game::get_now();
        $cacluTime = $this->getStrtotime();
        if(!empty($this->info['startTime']) && ($cacluTime + 172800 > $now)){
            Master::error(CLUB_PARTY_START_TIME_NOT_REACH);
        }
        $partyCfg = Game::getcfg_info('party',$id);
        if($this->info['totalResource'] < $partyCfg['cost']){
            Master::error(CLUB_PARTY_RESOURCE_NOT_ENOUGH);
        }
        $this->info['totalResource'] -= $partyCfg['cost'];
        $this->info['startTime'] = $now;
        $this->info['joinPartyPeople'] = array();
        $this->info['partyLv'] = $id;
        $this->save();

    }

    //玩家进入宴会
    public function setPartyUser($uid){
        self::partyTime();
        if(!in_array($uid,$this->info['joinPartyPeople'])){
            array_push($this->info['joinPartyPeople'],$uid);
        }
        $this->save();
    }

    
    /*
     * 返回协议信息
     */
    public function bake_data(){
        $this->outof = $this->info;
        Master::back_data(0,'club','partyResource',$this->outof);
    }
}





