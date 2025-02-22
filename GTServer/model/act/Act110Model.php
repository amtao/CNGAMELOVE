<?php
require_once "ActHDBaseModel.php";
/*
 * 狩猎
 */
class Act110Model extends ActHDBaseModel
{
	public $atype = 110;//活动编号
	
	public $comment = "狩猎";
	public $b_mol = "hunt";//返回信息 所在模块
	public $b_ctrl = "hunt";//返回信息 所在控制器
	public $hd_id = 'huodong_110';//活动配置文件关键字
	public $hd_cfg;
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
	    
	    'score' => 0, //积分
	    'guanka' => 1, //关卡
	    'rwd_level' => 0, //积分领取档次
	    'cd' => 0,//打的时间
	    
	);
	
	public function get_news(){
	    return 0;
	}
	public function isOpen(){
	    $status = 1;
	    if(Game::is_over($this->hd_cfg['info']['eTime'])){
	        $status =0;
	    }
	    Master::back_data($this->uid, 'hunt', 'isopen', array('status' => $status));
	}
	
	/*
	 * 狩猎 - 打
	 * */
	public function play($id){
	    if(empty($this->hd_cfg)){
	        Master::error(CFG_ERROR);
	    }
	    if(Game::is_over($this->hd_cfg['info']['eTime'])){
	        Master::back_data($this->uid, 'hunt', 'huntFinish', array('status' => 1));
	        
	    }else{
    	    //第一步是否还在cd中
    	    if($_SERVER['REQUEST_TIME'] < $this->info['cd']){
    	        Master::error(BATTLE_CD);
    	    }
    	    
    	    $hurt_cfg = $this->hd_cfg['hurt_level'];
    	    
    	    if(empty($hurt_cfg[$id]) && $hurt_cfg[$id] !=0){
    	        Master::error(RANK_NOT_FIND);
    	    }
    	    //已经打到第几关
    	    $guanka = $this->info['guanka'];
    	    //获取关卡的信息
    	    $gk_info = Game::getcfg('hunt');
            if(empty($gk_info[$guanka])){
                Master::error(KILL_ALL);
            }	    
            //获取自己的阵法
            $team = Master::get_team($this->uid);
            $wuli = $team['shili'];//战斗力
            //造成的伤害
            $hurt = intval($wuli*(1+$hurt_cfg[$id]/100));
            if($hurt < $gk_info[$guanka]['hp']){
                //打不过  返回差多少
                Master::back_data($this->uid, 'hunt', 'fail', array('hurt'=>$hurt));
                $this->info['cd'] = $_SERVER['REQUEST_TIME']+60;
                $this->save();
            }else{
                //打过了
                
                $Ser40Model = Master::getSev40($this->hd_cfg['info']['id']);//总积分
                $Ser40Model->add($gk_info[$guanka]['score']);
                
                //积分排行
                $Redis108Model = Master::getRedis108($this->hd_cfg['info']['id']);
                $Redis108Model->zIncrBy($this->uid, $gk_info[$guanka]['score']);
                
                //必得奖励
                $items = array();//记录获得的总奖励
                if(!empty($gk_info[$guanka]['rwd'])){
                    foreach ($gk_info[$guanka]['rwd'] as $item){
                        Master::add_item($this->uid, KIND_ITEM , $item['itemid'],$item['count']);
                        $items[] = array(
                            'id' => $item['itemid'],
                            'count' => $item['count'],
                            'kind' => empty($item['kind'])? 1: $item['kind']
                        );
                    }
                }
                
                //随机奖励
                $allitems = array();
                for($i=0 ; $i < 1 ; $i++){
                    $rk = Game::get_rand_key(100, $gk_info[$guanka]['rwd_prob'],'prob_100');
                    
                    if(!empty($gk_info[$guanka]['rwd_prob'][$rk])){
                        $add_itemid = $gk_info[$guanka]['rwd_prob'][$rk]['itemid'];
                        $add_itemnum = $gk_info[$guanka]['rwd_prob'][$rk]['count'];
                        if(empty($allitems[$add_itemid])){
                            $allitems[$add_itemid] = 0;
                        }
                        $allitems[$add_itemid] += $add_itemnum;
                    }
                }
                
                
                if(!empty($allitems)){
                    foreach ($allitems as $id => $num){
                        $items[] = array(
                            'id' => $id,
                            'count' => $num,
                            'kind' => 1
                        );
                        Master::add_item($this->uid, 1, $id,$num);
                        //添加日志
                        if(!in_array($id,array(65,66,67))){
                            $Ser41Model = Master::getSev41($this->hd_cfg['info']['id']);
                            $Ser41Model->add($this->uid,$guanka,$id,$num);
                        }
                    }
                }
                
                $win = array(
                    'score' => $gk_info[$guanka]['score'],
                    'items' => $items,
                    'hurt' => $hurt,
                );
                Master::back_data($this->uid, 'hunt', 'win', $win);
                
                $this->info['guanka'] += 1;
                $this->info['cd'] = 0;
                //加积分
                $this->info['score'] += $gk_info[$guanka]['score'];//个人奖励
                
                $this->save();
                
            }
            
	    }
        
	}
	/*
	 * 领取积分奖励
	 * */
	public function jf_rwd($id){
	    if($id - $this->info['rwd_level'] != 1){
	        Master::error(ACT_HD_GIVE_ATTRIBUTE_ERROR);
	    }
	    //判断总积分够不够
	    $cfg = Game::getcfg_info('hunt_rwd', $id);
	    if(empty($cfg)){
	        Master::error(ACT_HD_GIVE_MAX);
	    }
	    $Ser40Model = Master::getSev40($this->hd_cfg['info']['id']);//总积分
	    if($Ser40Model->info <  $cfg['score']){
	        Master::error(ACT_HD_TOTAL_SCORE_IS_SHORT);
	    }
	    
	    if(!empty($cfg['rwd'])){
	        foreach ($cfg['rwd'] as $item){
	            if(empty($item['kind'])){
	                $item['kind'] = 1;
	            }
	            Master::add_item($this->uid,empty($item['kind']) ? 1 : $item['kind'], $item['itemid'],$item['count']);
	        }
	    }
	    $this->info['rwd_level'] += 1;
	    $this->save();
	}
	
	/*
	 * 构造输出
	 * */
	public function make_out() {
	    $outof = array();
	    $outof['score'] = $this->info['score'];
	    $outof['guanka'] = $this->info['guanka'];
	    $outof['rwd_level'] = $this->info['rwd_level'];
	    if(Game::is_over($this->info['cd'])){
	         $outof['cd']['next'] = 0;
	    }else{
	        $outof['cd']['next'] =  $this->info['cd'];
	    }
        $outof['cd']['label'] = 'huntOnecd';
        $this->outf = $outof;
	}
	public function back_data() {
	    if(empty($this->outf)){
	        Master::error(ACTHD_OVERDUE);
	    }
	    Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
	}
}
