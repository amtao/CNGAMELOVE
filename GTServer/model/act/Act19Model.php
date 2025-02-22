<?php
require_once "ActBaseModel.php";
/*
 * 牢房
 */
class Act19Model extends ActBaseModel
{
	public $atype = 19;//活动编号
	
	public $comment = "灵囿";
	public $b_mol = "laofang";//返回信息 所在模块
	public $b_ctrl = "pets";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
        'ep' =>array(),
        'kaifang'=>0,        //解锁数量
        'pets'	=> array(),  //灵囿列表
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
        $this->outf = array();
        if (!isset($this->info['pets'])){
            $this->reset();
        }
		//默认输出直接等于内部存储数据
        if (!empty($this->info['pets'])){
            foreach ($this->info['pets'] as $k=>$v){
                $this->outf[] = array('id'=>$k,'lv'=>$v['lv'],'exp'=>$v['exp'],'bjid'=>$v['bjid']);
            }
        }
	}

    /**
     * 重置脚本
     */
    public function reset(){
        if (!empty($this->info)){
            $old_info = $this->info;
            $this->info = array();
            $this->info['ep'] = array();
            $this->info['kaifang'] = $old_info['kaifang'];
            unset($old_info);
            $pets = array();
            for ($i=1;$i<=$this->info['kaifang'];$i++){
                $pets[$i]['lv'] = 1;
                $pets[$i]['exp'] = 0;
                $pets[$i]['bjid'] = rand(1,4);
                $this->info['ep'][$i] = array();
            }
            $this->info['pets'] = $pets;
            $this ->_save();
        }

    }
	
	/**
	 * @param $id 宠物解锁
	 */
	public function shouya($id){
        if (!empty($this->info['pets'][$id])){
            Master::error(PARAMS_ERROR);
        }
        $this->info['kaifang'] = $id;
        $this->info['ep'][$id] = array();
		$this->info['pets'][$id] = array('lv'=>1,'exp'=>0,'bjid'=>rand(1,4));
		$this ->save();
	}
	
	/**
	 * @param $type 1-4:普通喂食   5:一键鞭打
	 */
	public function bianDa($type,$id){
		//判断是否解锁
		if($id > $this->info['kaifang']){
			Master::error(JAIL_NO_PRISONER);
		}
        if($type > 4 || $type < 1){
            Master::error(PARAMS_ERROR);
        }
		//获取宠物配置信息
        $pets = $this->info['pets'][$id];
		$fanren_cfg = Game::getcfg_info('pve_fanren',$id);
        $prop = Game::getcfg_param('pet_eat_change');//灵囿在每次喂养后，会改变喜欢的食物的几率
        $Act20Model = Master::getAct20($this->uid);
        $cost = 0;
		// 1:一键鞭打
		if($type == 5){
            Master::error(JAIL_NO_PRISONER);
//			//一次性鞭打了几下
//			$daCount =  $fanren_cfg['hp'] - $this->info['hit'];
//			//无限死刑犯
//			if($fanren_cfg['hp'] == 0){
//				$daCount = 10000;
//			}
		}else{
		    //灵宠特权 减少消耗百分比
            $sub_a = (1-$Act20Model->info['add'][4]/10000);
			//一次性鞭打了1下
            $UserModel = Master::getUser($this->uid);
            $sub_g = ceil(floor(($id * 0.1 + 1) * ($UserModel->info['level']*0.2+1)*200)*$sub_a);
            //减去声望
            Master::sub_item($this->uid,KIND_ITEM,3,$sub_g);
            $daCount = 1;
            $cost = $sub_g;
		}
		
		//扣除名望值
		$Act20Model = Master::getAct20($this->uid);
		//当前名望最多可打几次
		$left_count = floor($Act20Model->info['mw'] / ceil($fanren_cfg['power']*$sub_a));
		//获取当前声望可鞭打几次
		$daCount = min($left_count,$daCount);
		$exp = $daCount*$fanren_cfg['power'];
		$Act20Model->sub_mw($exp);


        //经验暴击
        $per = $type == $pets['bjid']?2:1;
        //重置喜欢的食物类型
        if (rand(1,10000)<=$prop){
            $pets['bjid'] = rand(1,4);
        }

        //未满级加经验
        if ($pets['lv']<100){
            $exp_id = $pets['lv']+($fanren_cfg['exptype']*1000+1);
            $fanren_jy = Game::getcfg_info('prisoner_update',$exp_id);
            $exp = $daCount * $per *$exp ;
            $pets['exp'] += $exp;
            //如果当前犯人被打死却换到下一个犯人
            if($pets['exp'] >= $fanren_jy['food']){
                $pets['lv'] += 1;
                $pets['exp'] -= $fanren_jy['food'];
                if (is_int($this->info['ep'])){
                    //修改就数据 (之前加全属性 后来改独立属性)
                    $this->info['ep'] = array();
                    foreach ($this->info['pets'] as $e=>$d){
                        $index = $d['lv']+($e*1000);
                        $prisoner_cfg = Game::getcfg_info('prisoner_update',$index);
                        $this->info['ep'][$e] = array('type'=>$prisoner_cfg['ep_type'],'val'=>$prisoner_cfg['ep']);
                    }
                }else{
                    //正常数据
                    $this->info['ep'][$id] = array('type'=>$fanren_jy['ep_type'],'val'=>$fanren_jy['ep']);
                }
                $TeamModel = Master::getTeam($this->uid);
                $TeamModel->reset(5);
                $TeamModel->back_hero();
            }
        }

		//发放奖励
		if(!empty($fanren_cfg['rwd'])){
            $TeamModel  = Master::getTeam($this->uid);
			for($i = 1 ; $i <= $daCount; $i ++){
				//概率获得奖励
				$roll = rand(1, 10000);
				$pid = null;
		        foreach ($fanren_cfg['rwd'] as $k => $v) {
		            if ($roll <= $v['prob_10000']) {
                        $pid = $v;
		                break;
		            }
		            $roll -= $v['prob_10000'];
		        }
		        
		        if(!empty($pid['type'])){
		            //阵法
                    $pid['count'] = Game::type_to_count($pid['type'],$TeamModel->info['allep']);
		        }
                //暴击
                $pid['count']*=$per;
		        //灵囿特权加成
		        $add = 1;
                if (!empty($Act20Model->info['add'][4])){
                    $add = ($Act20Model->info['add'][4]/10000+1);
                }
		        Master::add_item($this->uid,KIND_ITEM,$pid['itemid'],$pid['count']*$add);
			}
		}
		
		//主线任务
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(29,$daCount);
		
		//活动消耗 - 惩戒犯人次数
		$HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong217',$daCount);
        
		$this->info['pets'][$id] = $pets;
        $this ->save();
        
	}

    /*
     * 获取属性加成
     */
    public function get_pets_ep(){
        if (!isset($this->info['pets'])){
            $this->reset();
        }
        $ep = array(1=>0,2=>0,3=>0,4=>0);
        if (!empty($this->info['ep'])){
            foreach ($this->info['ep'] as $v){
                if ($v['type'] == 5){
                    $ep[1] += $v['val'];
                    $ep[2] += $v['val'];
                    $ep[3] += $v['val'];
                    $ep[4] += $v['val'];
                }else{
                    $ep[$v['type']] += $v['val'];
                }

            }
        }
        return $ep;
    }
}
