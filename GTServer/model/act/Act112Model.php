<?php
require_once "ActBaseModel.php";
/*
 * 丝绸之路 - 关卡
 */
class Act112Model extends ActBaseModel
{
	public $atype = 112;//活动编号
	
	public $comment = "丝绸之路-关卡";
	public $b_mol = "trade";//返回信息 所在模块
	public $b_ctrl = "Info";//返回信息 所在控制器
	public $cfg;
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
	     'gid' => 1,//当前要征服的关卡
	     'rwd' => array(),
	);
	
	/*
	 * 构造输出
	 * */
	public function make_out() {
	    $outof =  array();
	    if(empty($this->info['rwd'])){
	        $this->distribution();
	        $this->save();
	    }
	    $outof['gid'] = $this->info['gid'];//当前关卡数
	    $Act113Model = Master::getAct113($this->uid);
	    $outof['status'] = 0;//一键通商开关是否开启
	    if($Act113Model->info['status'] == 1){
	        $outof['status'] = 1;
	    }
        if(!empty($this->info['rwd'])){
            foreach ($this->info['rwd'] as $item){
                unset($item['prob_100']);
                $outof['rwd'][] = $item;
            }
        }
        $this->outf = $outof;
	}
	/*
	 * 分配
	 * */
	public function distribution(){
	    //判断是否可以通商了
	    $hd_cfg = Game::getcfg('trade');
	    $cfg = $hd_cfg[$this->info['gid']];
	    if(!empty($cfg)){
    	    $this->info['rwd'] = array();
            $items_cfg = Game::getcfg('trade_rwd');
            if(empty($items_cfg)){
                Master::error(ACT_HD_REWARD_CONFIG_ERROR);
            }
           	//随机奖励
           	$items = array();
           	if(!empty($cfg['prob_rwd'])){
           	    foreach ($cfg['prob_rwd'] as $k => $rand){
           	        if(!empty($items_cfg[$k]['rwd'])){
           	            $rand_rwd = $this->item_rand_rwd($items_cfg[$k]['rwd']);
           	            if(!empty($rand_rwd)){
           	                $items[] = array('id' => $rand_rwd['id'],'kind'=> $rand_rwd['kind'],'count'=>$rand_rwd['count'],'prob_100'=>$rand);
           	            }
           	        }
           	    }
           	}
            $this->info['rwd'] = $items;
        }
	}
	
	
	/*
	 * 随机获取道具
	 * */
	public function item_rand_rwd($items){  
	    if(empty($items)){
	        return array();
	    }
	    $allitems = array();
        $rk = Game::get_rand_key(100, $items,'prob_100');
        if(!empty($items[$rk])){
            $id = empty($items[$rk]['itemid']) ? $items[$rk]['id'] : $items[$rk]['itemid'];
            $num = $items[$rk]['count'];
            $kind = empty($items[$rk]['kind']) ? 1: $items[$rk]['kind'];
            $allitems = array('id' => $id,'kind'=> $kind,'count' => $num);
        }
	    return $allitems;
	}
	/*
	 * 一键通商 
	 * */
	public function rootPlay($id){
	    if(empty($id) || !is_numeric($id)){
	        Master::error(ACT_112_MUBIAOWRONG);
	    }
	    if($id < $this->info['gid']){
	        Master::error(ACT_112_MUBIAOCHAOGUO);
	    }
	    //获取自己的阵法
	    $team = Master::get_team($this->uid);
	    $intellect = $team['allep'][2];//智力
	    $UserModel = Master::getUser($this->uid);
	    
	    $items = array();//记录获得的总奖励
	    $userCoin = $UserModel->info['coin'];//用户总金币
	    $useAllCoin = 0;//消耗总金币
	    $start=$this->info['gid'];//开始关卡数
	    $items_cfg = Game::getcfg('trade_rwd');
	    $hdcfg = Game::getcfg('trade');

        $hdIts = array();  //存放活动道具

	    for($gid = $this->info['gid'];$gid<=$id;$gid++){
    	    $cfg = $hdcfg[$gid];
    	    if(empty($cfg)){
    	       break;
    	    }
    	    $enemy_intellect = $cfg['intellect']; //智力
    	    $enemy_coin = $cfg['coin'];//金币
    	    $need_coin = round($enemy_coin*$enemy_intellect/$intellect);//需要消耗的金币 敌方金币x敌方智力/我军智力
    	    $bz_coin = round($enemy_coin*0.26);//标准消耗金币 敌方金币*26%
    	    $need_coin = $need_coin >= $bz_coin ? $need_coin : $bz_coin;
	        //消耗数据处理
	        $userCoin = $userCoin - $need_coin;
	        if($userCoin < 0 || $need_coin < 0){
	            break;
	        }
	        $useAllCoin +=$need_coin;
	        //必得奖励
	        if(!empty($cfg['rwd'])){
	            foreach ($cfg['rwd'] as $item){
	                $kind = empty($item['kind'])? 1: $item['kind'];
	                $items[$item['itemid']][$kind] = empty($items[$item['itemid']][$kind]) ? $item['count'] : $items[$item['itemid']][$kind]+$item['count'];
	            }
	        }
	         
	        //随机奖励
	       if(!empty($this->info['rwd'])){
	            $rand_rwd = $this->item_rand_rwd($this->info['rwd']);
	            if(!empty($rand_rwd)){
	                $items[$rand_rwd['id']][$rand_rwd['kind']] = empty($items[$rand_rwd['id']][$rand_rwd['kind']]) ? $rand_rwd['count'] : $items[$rand_rwd['id']][$rand_rwd['kind']]+$rand_rwd['count'];
	            }
	        }

            //双旦活动道具产出
            $Act292Model = Master::getAct292($this->uid);
            $hdItems = $Act292Model->chanChu(5,$gid,1);
            if(!empty($hdItems)){
                $hdIts[] = $hdItems;
            }

	        //获取下一关数据
	        $this->info['gid'] +=1;
	        
	        $this->distribution();
	        
	    }
	    $end = $this->info['gid'];//结束是关卡数
	    $pass = $end - $start;    
	    Master::back_data($this->uid, 'trade', 'win', array('pass'=>$pass));

	    //总奖励 items  总消耗 $useAllCoin
	    Master::sub_item($this->uid,KIND_ITEM,2,$useAllCoin);//扣兵
	    //记录获得的总道具
	    $addItems = array();
	    if(!empty($items)){
	       foreach ($items as $id => $val){
	           foreach ($val as $kind => $count){
	               $addItems[] = array('id'=>$id,'kind'=>$kind,'count'=>$count);
	           }
	       }
	    }

	    if(!empty($hdIts)){
            foreach ($hdIts as $v){
                $addItems[] = $v;
            }
        }

	    if(!empty($addItems)){
	        Master::add_item3($addItems,'trade','pvewin');//加奖励
	    }
	    
	    $this->save();//保存下一个关信息
	    

        //主线任务 ---  丝路竞价	丝路竞价X关
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(44, $pass);
			
	}
	
	/*
	 * 通商 - pk
	 * id =>关卡id
	 * */
	public function play($id){
	    if($id !== $this->info['gid']){
	        Master::error(TRADE_NOT_REACH_PLACE_ERROR);
	    }
	    $cfg = Game::getcfg_info('trade',$id,TRADE_NOT_ROUND_WORLD_WEEK);
	    //敌方信息
       	$enemy_intellect = $cfg['intellect']; //智力
       	$enemy_coin = $cfg['coin'];//金币
	    
	    //获取自己的阵法
	    $team = Master::get_team($this->uid);
	    $intellect = $team['allep'][2];//智力
	    $UserModel = Master::getUser($this->uid);
	    $coin = $UserModel->info['coin'];//金币
	    $msgWin = array();//弹窗
	    $need_coin = round($enemy_coin*$enemy_intellect/$intellect);//需要消耗的金币 敌方金币x敌方智力/我军智力
	    $bz_coin = round($enemy_coin*0.26);//标准消耗金币 敌方金币*26%    
	    $need_coin = $need_coin >= $bz_coin ? $need_coin : $bz_coin;  
	    if($coin >= $need_coin){//拥有的金币大于需要的金币
	        //扣兵
	        Master::sub_item($this->uid,KIND_ITEM,2,$need_coin);        
	        //必得奖励
	        $items = array();//记录获得的总奖励
	        if(!empty($cfg['rwd'])){
	            foreach ($cfg['rwd'] as $item){
	                $kind = empty($item['kind'])? 1: $item['kind'];
	                $items[] = array(
	                    'id' => $item['itemid'],
	                    'count' => $item['count'],
	                    'kind' => $kind
	                );
	            }
	        }
	        //随机奖励
	        if(!empty($this->info['rwd'])){
	            $rand_rwd = $this->item_rand_rwd($this->info['rwd']);
	            if(!empty($rand_rwd)){
	                $items[] = $rand_rwd;
	            }
	        }

            //双旦活动道具产出
            $Act292Model = Master::getAct292($this->uid);
            $hdItems = $Act292Model->chanChu(5,$id,1);
            if(!empty($hdItems)){
                $items[] = $hdItems;
            }

	        $win = 1;//胜利
	        Master::add_item3($items,'trade','pvewin');        
	        if($this->info['gid'] >=50){
	            $Act113Model = Master::getAct113($this->uid);
	            $Act113Model->update();
	        }
	        //获取新的一关信息
	        $this->info['gid'] +=1;
	        $this->distribution();
	        $this->save();   

            //主线任务 ---  丝路竞价	丝路竞价X关
            $Act39Model = Master::getAct39($this->uid);
            $Act39Model->task_add(44, 1);

	    }else{//打不过
	        //兵力清空
	        Master::sub_item($this->uid,KIND_ITEM,2,$coin);
	        //敌方消耗      我方金币*我方智力/敌方智力
	        $monber_coin = round($coin*$intellect/$enemy_intellect);
	        if($monber_coin >= $enemy_coin){
	            $monber_coin  = $enemy_coin*0.9;
	        }
	        $win = 0;//失败
	    }
	    $msgWin = array(
	        'win' => $win,
	        'map' => array(
	            'title' => '丝绸之路',
	            'bgimg' => 1,
	        ),
	        'members' => array(
	            array(//默认敌方
	               'name' => $cfg['place'],
	               'itype' => 2,//1人 2怪
	               'level' => 0,
	               'e2' => $enemy_intellect,
	               'coin' => $enemy_coin,
	               'die' => $win == 1 ? $enemy_coin : $monber_coin,
	            ),
	            array(//默认自己
	                'name' => $UserModel->info['name'],
	                'itype' => 1,//人 怪
	                'user' =>  Master::fuidInfo($this->uid),
	                'level' => $UserModel->info['level'],
	                'e2' => $intellect,
	                'coin' => $coin,
	                'die' => $win == 1 ? $need_coin : $coin,
	            ),
	        ),
	    );
	    Master::back_data($this->uid, 'trade', 'fight', $msgWin);
	}
	
	
	/*
	 * 概率是否为真
	 * 
	 * */
	public function rand_num($num=0) {
	    $r = rand(1,100);
	    if($r > $num){
	        return 1;
	    }else{
	        return 0;
	    }
	}
	
}
