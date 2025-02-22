<?php
require_once "ActBaseModel.php";
/*
 * 祈福
 */
class Act6210Model extends ActBaseModel
{
	public $atype = 6210;//活动编号
	
	public $comment = "许愿树";
    public $b_mol = "user";//返回信息 所在模块
    public $b_ctrl = "wishTree";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(
        'count' => array(), //已抽次数
        'time'  => 0,       //时间记录
    );

    /*
	 * 抽奖
	 */
    public function play($id,$num = 1){

        $riqi = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        $daycount_cfg = Game::getcfg_info("hero_treegroup",$id);
        $daycount = $daycount_cfg['daycount'];
        if ($num < 1 || $num > 10){
            Master::error(PARAMS_ERROR);
        }
        //兼容
        if (!is_array($this->info['count'])){
            $this->info['count'] = empty($this->info['count'])?array():array(1=>$this->info['count']);
        }
        if (empty($this->info['time']) || $riqi != $this->info['time']){
            $this->info['count'] = array();
            $this->info['time'] = $riqi;
        }
        $count = empty($this->info['count'][$id])?0:$this->info['count'][$id];
        if ($daycount - $count < $num){
            Master::error(JINGYING_COUNT_LIMIT);
        }
        $lottery_nums = $this->ms_consume($id,$num);
        //判断是否可以购买
        $UserModel = Master::getUser($this->uid);
        if ($lottery_nums > $UserModel->info['army']){
            Master::error(COMMON_LIMIT);
        }
        Master::sub_item($this->uid,KIND_ITEM,4,$lottery_nums);

        $item_win = array();
        $get = false;
        for ($i = 0;$i < $num;$i++){
            if ($i == 9 && $get === false){
                $item_win[] = $this->lottery($id,true);
            }else{
                $item = $this->lottery($id);
                if ($item['kind'] == 96){
                    $get = true;
                }
                $item_win[] = $item;
            }
        }
        //领取奖励
        Master::add_item3($item_win);
        //获取的剧情随便id数组(前端专用)
        $jb_cfg = Game::getcfg('hero_pve');
        if ($num == 10 || $get === true){
            $temp = array();
            foreach ($item_win as $info){
                if ($info['kind'] == 96){
                    $temp[] = $info['id'];
                    //抽到天赐聊天广播
                    if ($jb_cfg[$info['id']]['star'] == 3){
                        $Sev6012Model = Master::getSev6012();
                        $msg = "#wishtree#::".$info['id'];
                        $Sev6012Model->add_msg($this->uid, $msg, 3);
                    }
                }
            }
            Master::back_data($this->uid,$this->b_mol,'plotFragments',$temp);
        }
        $this->info['count'][$id] = $count + $num;
        $this->save();
       
    }

    /*
	 * 抽奖函数
	 */
    private function lottery($tree_id,$isTen = false){
        $lottery_cfg = Game::getcfg('tree_reward'.$tree_id);
        if ($isTen){
            foreach ($lottery_cfg as $x=>$z){
                if ($z['kind'] != 96){
                    unset($lottery_cfg[$x]);
                }
            }
        }
        $key = Game::get_rand_key1($lottery_cfg,'prob');
        $item = array();
        $item['kind'] = $lottery_cfg[$key]['kind'];
        $item['count'] = $lottery_cfg[$key]['count'];
        switch ($lottery_cfg[$key]['kind']){
            case 1:  //普通道具
                $item['id'] = $lottery_cfg[$key]['kindid'];
                break;
            case 96: //羁绊剧情
                $jb_arr = array();
                $jb_cfg = Game::getcfg('hero_pve');
                foreach ($jb_cfg as $v){
                    if ($v['star'] == $lottery_cfg[$key]['kindid'] && !is_numeric($v['id']) && $v['tree'] == $tree_id){
                        $jb_arr[] = $v['id'];
                    }
                }
                $id = array_rand($jb_arr,1);
                $item['id'] = $jb_arr[$id];
                break;
            case 103://珍宝碎片
                $treasure_cfg = Game::getcfg('treasure');
                $id = array_rand($treasure_cfg,1);
                $item['id'] = $treasure_cfg[$id]['tagid'];
                break;
        }
        return $item;
    }

    /*
	 * 计算名声消耗
	 */
    public function ms_consume($id,$num){
        $count = $this->info['count'][$id];
        $ms = 0;
        for ($i = 1;$i <= $num;$i++){
            $count ++;
            switch ($id){
                case 1:
                    $ms += Game::getCfg_formula()->tree_ms1($count);
                    break;
                case 2:
                    $ms += Game::getCfg_formula()->tree_ms2($count);
                    break;
            }

        }
        return $ms;
    }

    /*
	 * 构造输出结构体
	 */
    public function make_out(){
        //默认输出直接等于内部存储数据
        $this->outf = array();
        $riqi = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        $group_cfg = Game::getcfg('hero_treegroup');
        $hero_pve = Game::getcfg('hero_pve');
        if ($riqi != $this->info['time']){
            $this->info['count'] = 0;
        }
        $Act6005Model = Master::getAct6005($this->uid);
        //获取图鉴组类型
        $group = array();
        foreach ($group_cfg as $j){
            $group[] = $j['unlocktype'];
            if (empty($this->info['count'][$j['id']])){
                if (!is_array($this->info['count'])){
                    $this->info['count'] = array();
                }
                $this->info['count'][$j['id']] = 0;
            }
        }
        foreach ($this->info['count'] as $id => $con){
            $this->outf['countInfo'][] = array('id'=>$id,'count'=>$con);
        }
        //获取图鉴所需id集合
        foreach ($Act6005Model->info['jbItem'] as $k=>$v){
            foreach ($v as $x=>$y){
                if ($x!='prop' && in_array($hero_pve[$x]['unlocktype'],$group)){
                    $this->outf['have'][] = $x;
                }
            }
        }
    }
}

