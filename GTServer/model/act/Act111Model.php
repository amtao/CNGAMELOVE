<?php
require_once "ActBaseModel.php";
/*
 * 讨伐
 */
class Act111Model extends ActBaseModel
{
	public $atype = 111;//活动编号
	
	public $comment = "讨伐";
	public $b_mol = "taofa";//返回信息 所在模块
	public $b_ctrl = "playInfo";//返回信息 所在控制器
	public $cfg;
	
	public $rwd = array(
	    1 => 0,
	    2 => 0,
	    3 => 0,
	    4 => 1,
	    5 => 2,
	); 
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
	     'gid' => 1,
	     'data'=> array(),
	);
	/*
	 * 构造输出
	 * */
	public function make_out() {
	    $outof =  array();
	    $cfg = Game::getcfg('taofa');
	    if(empty($this->info['data'])){
	        $this->distribution();
	        $this->save();
	    }
	    $outof['gid'] = $this->info['gid'];
        if(!empty($this->info['data'])){
            foreach ($this->info['data'] as $id => $val){
                $outof['data'][] = array(
                    'id' => $id,
                    'army' => $val['army'],
                    'rwd' => $val['rwd_prob'],
                );
            }
        }
        $this->outf = $outof;
	}
	/*
	 * 分配
	 * */
	public function distribution(){
	    
	    //判断是否可以讨伐了
	    $cfg = Game::getcfg('taofa');
	    
	    $cfg = $cfg[$this->info['gid']];
	    if(!empty($cfg)){
    	    $this->info['data'] = array();
            $items_cfg = Game::getcfg('taofa_rwd');
            if(empty($items_cfg)){
                Master::error(ACT_HD_REWARD_CONFIG_ERROR);
            }
            $WifeModel = Master::getWife($this->uid);
            $HeroModel = Master::getHero($this->uid);
            if(empty($WifeModel->info))Master::error(ACT_HD_LEAST_ONE_WIFE);
            if(empty($HeroModel->info))Master::error(ACT_HD_LEAST_ONE_HERO);
            
            //获取三组信息
            for ($i=0;$i<3;$i++){
    	        //怪物id
    	        $key = array_rand($cfg['guai'],1);
    	        $id = $cfg['guai'][$key];
    	        unset($cfg['guai'][$key]);//避免出现同样的怪物
    
               	//随机兵力
               	$army = rand($cfg['army']*0.8,$cfg['army']*1.2);
                
               	//随机奖励
               	$bo = $this->info['gid'] % 10;
               	if($this->info['gid'] !=0 && $bo == 0) $bo = 10;
               	$items = array();
                if($bo >=1){
                    //随机一个红颜
                    $wifeid = $WifeModel->get_one_wife();
                    //随机魅力点
                    foreach ($items_cfg[1]['rwd'] as $val){
                        $rank_count = $val['rcount'];
                        $count = $rank_count[array_rand($rank_count,1)];
                        if(empty($count)) Master::error(ACT_HD_RAND_CHARM_ERROR);
                        //组装信息 暂时写死
                        $items[] = array('id'=>$wifeid,'kind'=>4,'count' => $count);
                    }
                }
                if($bo>=3){
                    //随机一个门客
                    $HeroModel = Master::getHero($this->uid);
                    $heroid = $HeroModel->get_one_hero();
                    //随机书籍经验或者技能经验
                    $two_rwd = $this->item_rand_rwd($items_cfg[2]['rwd']);
                    //组装信息
                    if(!empty($two_rwd)){
                        $kind = $two_rwd['id'] == 34 ? 6 : 5;
                        $items[] = array('id'=>$heroid,'kind'=> $kind,'count'=> $two_rwd['count']);
                    }
                }
                if($bo>=5){
                    //随机获取属性丸
                    $three_rwd = $this->item_rand_rwd($items_cfg[3]['rwd']);
                    if(!empty($three_rwd)){
                        $items[] = $three_rwd;
                    }
                }
                if($bo>=7){
                    //随机获取属性丹
                    $four_rwd = $this->item_rand_rwd($items_cfg[4]['rwd']);
                    if(!empty($four_rwd)){
                        $items[] = $four_rwd;
                    }
                }
                if($bo >= 9){//最高奖励
                    $five_rwd = $this->item_rand_rwd($items_cfg[5]['rwd']);
                    if(!empty($five_rwd)){
                        $items[] = $five_rwd;
                    }
                    if($five_rwd['id'] == 77){//有卷轴出现的话
                        $army = $army*1.5;
                    }
                }
                $this->info['data'][$id] = array(
                    'army' => $army,
                    'rwd_prob' => $items,
                );
            }
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
            $id = $items[$rk]['itemid'];
            $num = $items[$rk]['count'];
            $kind = empty($items[$rk]['kind']) ? 1: $items[$rk]['kind'];
            $allitems = array('id' => $id,'kind'=> $kind,'count' => $num);
        }
	    return $allitems;
	}
	/*
	 * 一键讨伐 
	 * */
	public function rootPlay($id){
	    if(empty($id) || !is_numeric($id)){
	        Master::error(ACT_HD_CUSTOM_ERROR);
	    }
	    if($id < $this->info['gid']){
	        Master::error(ACT_HD_PLAY_CUSTOM_ERROR);
	    }
	    //获取自己的阵法
	    $team = Master::get_team($this->uid);
	    $wuli = $team['allep'][1];//武力
	    $UserModel = Master::getUser($this->uid);
	    
	    $items = array();//记录获得的总奖励
	    $userArmy = $UserModel->info['army'];//用户总士兵
	    $useAllArmy = 0;//消耗总士兵
	    $start=$this->info['gid'];//开始关卡数
	    $items_cfg = Game::getcfg('taofa_rwd');

        $rItems = array();  //存放活动292道具

	    for($gid = $this->info['gid'];$gid<=$id;){
    	    $data = Game::getcfg_info('taofa',$gid,'已达到最高关卡');
    	    if(empty($data)){
    	       break;
    	    }
	        //怪物信息
	        $guai = $this->info['data'][array_rand($this->info['data'],1)];
	        
	        //一键内打的士兵都是用标准兵
	        $max_army = $data['army'];
	        
	        //需要消耗的兵力   敌数*敌数*0.101/武力
	        $need_army = round($max_army*$max_army*0.101/$wuli);
	        
	        //标准消耗兵力 敌方兵力*26%
	        $bz_army = round($max_army*0.26);
	         
	        $need_army = $need_army >= $bz_army ? $need_army : $bz_army;
	        
	        //消耗数据处理
	        $userArmy = $userArmy - $need_army;
	        if($userArmy < 0 || $need_army < 0){
	            break;
	        }
	        $useAllArmy +=$need_army;
	
	        //必得奖励
	        if(!empty($data['rwd'])){
	            foreach ($data['rwd'] as $item){
	                $kind = empty($item['kind'])? 1: $item['kind'];
	                $items[$item['itemid']][$kind] = empty($items[$item['itemid']][$kind]) ? $item['count'] : $items[$item['itemid']][$kind]+$item['count'];
	            }
	        }
	         
	        //随机奖励
	        if(!empty($guai['rwd_prob'])){
	            $count = count($guai['rwd_prob']);
	            if(empty($count) || $count>5) $count=0;
	            if(empty($this->rwd[$count])) $this->rwd[$count] = 0;
	            $i = 0;//获得的道具数量
	            $j = 1;//循环的次数
	            do{
	                if(empty($guai['rwd_prob']))break;
	                foreach ($guai['rwd_prob'] as $key => $ite){
	                        
	                        if($this->rand_num($items_cfg[$key+1]['rand'])){
	                            $items[$ite['id']][$ite['kind']] = empty($items[$ite['id']][$ite['kind']]) ? $ite['count'] : $items[$ite['id']][$ite['kind']]+$ite['count'];
	                            unset($guai['rwd_prob'][$key]);
	                            $i++;
	                            if($j>1 && $this->rwd[$count]<=$i){
	                                break;
	                            }
	                        }
	                }
	                $j++;
	            }while ($this->rwd[$count] > $i);
	        }

            //双旦活动道具产出
            $Act292Model = Master::getAct292($this->uid);
            $rItem = $Act292Model->chanChu(4,$gid,1);
            if(!empty($rItem)){
                $rItems[] = $rItem;
            }


	        //获取下一关数据
	        $this->info['gid'] +=1;
	        $gid++;
	        $this->distribution();
	    }
	    
	    $end = $this->info['gid'];//结束是关卡数

	    $pass = $end - $start;
		     
        //主线任务 ---  围剿乱党	围剿X波乱党
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(42, $pass);
	    
	    Master::back_data($this->uid, 'taofa', 'win', array('pass'=>$pass));
	    
	    //总奖励 items  总消耗 $useAllArmy
	    Master::sub_item($this->uid,KIND_ITEM,4,$useAllArmy);//扣兵
	    //记录获得的总道具
	    $addItems = array();
	    if(!empty($items)){
	       foreach ($items as $id => $val){
	           foreach ($val as $kind => $count){
	               $addItems[] = array('id'=>$id,'kind'=>$kind,'count'=>$count);
	           }
	       }
	    }

        if(!empty($rItems)){
	        foreach ($rItems as $rv)
            $addItems[] = $rv;
        }

	    if(!empty($addItems)){
	        Master::add_item3($addItems,'taofa','pvewin');//加奖励
	    }
	    $this->save();//保存下一个关信息
	}
	
	/*
	 * 讨伐 - 打
	 * */
	public function play($id){
	    
	    if(empty($this->info['data'][$id])){
	        Master::error(ACT_HD_NO_FUND_OPPONENT);
	    }
	    //怪物信息
	    $guai = $this->info['data'][$id];
	    
	    //获取自己的阵法
	    $team = Master::get_team($this->uid);
	    $wuli = $team['allep'][1];
	    
	    $data = Game::getcfg_info('taofa',$id);
	    if(empty($data)){
	        Master::error(ACT_HD_ALL_KILL_OPPONENT);
	    }
	    $msgWin = array();//弹窗
	    //敌方武力  敌数*(0.101)
	    $guai_wuli = round($guai['army']*0.101);
	     
        //需要消耗的兵力     敌数*敌数*0.101/武力
	    $need_army = round($guai['army']*$guai['army']*0.101/$wuli);
	    
	    //标准消耗兵力 敌方兵力*26%
	    $bz_army = round($guai['army']*0.26);
	    
	    $need_army = $need_army >= $bz_army ? $need_army : $bz_army;
	    
	    $UserModel = Master::getUser($this->uid);
	    $army = $UserModel->info['army'];
	    
	    if($army >= $need_army){//拥有的兵力大于需要的兵力
	        //打过了
	        //扣兵
	        Master::sub_item($this->uid,KIND_ITEM,4,$need_army);
	        
	        //必得奖励
	        $items = array();//记录获得的总奖励
	        if(!empty($data['rwd'])){
	            foreach ($data['rwd'] as $item){
	                $kind = empty($item['kind'])? 1: $item['kind'];
	                $items[] = array(
	                    'id' => $item['itemid'],
	                    'count' => $item['count'],
	                    'kind' => $kind
	                );
	            }
	        }
	        
	        //随机奖励
	        if(!empty($guai['rwd_prob'])){
	            $count = count($guai['rwd_prob']);
	            if(empty($count) || $count>5) $count = 0;
	            $i = 0;//获取道具个数
	            $j = 1; //循环次数
	            $items_cfg = Game::getcfg('taofa_rwd');
	            if(empty($this->rwd[$count])) $this->rwd[$count] = 0;
	            do{
	                if(empty($guai['rwd_prob']))break;
	                foreach ($guai['rwd_prob'] as $key => $ite){

	                    if($this->rand_num($items_cfg[$key+1]['rand'])){
	                        $items[] = $ite;
	                        unset($guai['rwd_prob'][$key]);
	                        $i++;
	                        if($j > 1 && $i>=$this->rwd[$count])break; 
	                    }
	                }
	                $j++;
	            }while ($this->rwd[$count] > $i);
	        }
	        $win = 1;//胜利

            //双旦活动道具产出
            $Act292Model = Master::getAct292($this->uid);
            $rItem = $Act292Model->chanChu(4,$this->info['gid'],1);
            if(!empty($rItem)){
                $items[] = $rItem;
            }
	        Master::add_item3($items,'msgwin','fight');
	        //获取新的一关信息
	        $this->info['gid'] +=1;
	        $this->distribution();
	        $this->save();

            //主线任务 ---  围剿乱党	围剿X波乱党
            $Act39Model = Master::getAct39($this->uid);
            $Act39Model->task_add(42, 1);

	    }else{//打不过
	        //兵力清空
	        Master::sub_item($this->uid,KIND_ITEM,4,$army);

	        //敌方消耗      我军数x我军数/敌军总武力*8/75;  军数*武力/($guai['army']*0.101)
	        $monber_die = round($army*$wuli/($guai['army']*0.101));
	        if($monber_die >= $guai['army']){
	            $monber_die  = $guai['army']*0.9;
	        }
	        $win = 0;//失败
	    }
	    $monster = Game::getcfg_info('taofa_monster', $id);
	    $msgWin = array(
	        'win' => $win,
	        'map' => array(
	            'title' => '围剿',
	            'bgimg' => 1,
	        ),
	        'members' => array(
	            array(//默认敌方
	               'name' => $monster['name'],
	               'index' => $monster['index'],
	               'itype' => 2,//1人 2怪
	               'level' => 0,
	               'e1' => $guai_wuli,
	               'army_max' => $guai['army'],
	               'army' => $guai['army'],
	               'die' => $win == 1 ? $guai['army'] : $monber_die,
	               'army_type'=> $monster['action'],
	            ),
	            array(//默认自己
	                'name' => $UserModel->info['name'],
	                'index' => $UserModel->info['job'],//
	                'itype' => 1,//人 怪
	                'user' =>  Master::fuidInfo($this->uid),
	                'level' => $UserModel->info['level'],
	                'e1' => $wuli,
	                'army_max' => $army,
	                'army' => $army,
	                'die' => $win == 1 ? $need_army : $army,
	            ),
	        ),
	    );
	    Master::back_data($this->uid, 'msgwin', 'fight', $msgWin);
	    
	}
	
	
	/*
	 * 概率是否为真
	 * 
	 * */
	public function rand_num($num) {
	    $r = rand(1,100);
	    if($r > $num){
	        return 1;
	    }else{
	        return 0;
	    }
	}
	
}
