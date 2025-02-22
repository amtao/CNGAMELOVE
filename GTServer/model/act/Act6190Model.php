<?php
require_once "ActBaseModel.php";
/*
 * 御花园
 */
class Act6190Model extends ActBaseModel
{
	public $atype = 6190;//活动编号
	
	public $comment = "御花园";
    public $b_mol = "flower";//返回信息 所在模块
    public $b_ctrl = "base";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'types' => array(/*
            "id",
            "cur",
            'rwd',
        */),
        'lastTime' => 0,
        'cPoints' => array(/*
            "type",
            "id",
            "time",
            "chen",
            "rwd",
            "sUids"
        */),
        'protect' => array(/*
            "cur",当前正在使用的保护罩id
            "ctime",
            "time",
            "cd"=>array(
                array(
                    "id",
                    "over"
                ),
            ),
        */),
    );

    /*
     * 构造输出结构体
     * 修改保存结构体
     */
    public function make_out()
    {
        $this->resetValue();
        $this->outf = $this->info["types"];
    }

    /*
     * 返回活动信息
     */
    public function back_data($flag = false){
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
        $protect = $this->back_data_protect();
        Master::back_data($this->uid,$this->b_mol,'protect',$protect);
        if ($flag)Master::back_data($this->uid,$this->b_mol,"chenlu",$this->info["cPoints"]);
    }

    public function zeroFlush(){
        $this->make_out();
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
    }

    private function resetValue(){
        if ($this->info['lastTime'] < Game::day_0()){
            $types = array();
            foreach ($this->info['types'] as $v){
                $cSys = Game::getcfg_info("flowerRain", $v['id']);
                if ($cSys['type'] == 1){
                    $v['cur'] = 0;
                    $v['rwd'] = 0;
                }
                $types[] = $v;
            }
            $this->info['types'] = $types;
            $this->info['lastTime'] = Game::get_now();
			$this->_save();
        }
        else {
            $flag = false;
            $types = array();
            foreach ($this->info['types'] as $v){
                if ($v['cur'] < 0){
                    $v['cur'] = 0;
                    $flag = true;
                }
                $types[] = $v;
            }
            if ($flag){
                $this->info['types'] = $types;
                $this->_save();
            }
        }
    }

    public function addType($type, $value){
        if($value <= 0)return;
        $this->resetValue();
        $cSys = Game::getcfg_info("flowerRain", $type);
        $fItem = null;

        foreach ($this->info['types'] as $v){
            if ($v['cur'] < 0)$v['cur'] = 0;
            if ($v['id'] == $type){
                $v['cur'] += $value;
                $fItem = $v;
                continue;
            }
            $types[] = $v;
        }
        if ($fItem == null){
            $fItem = array();
            $fItem['id'] = $type;
            $fItem['cur'] = $value;
            $fItem['rwd'] = 0;
        }

        if ($fItem['cur'] >= $cSys['set']){
            if ($cSys['type'] == 1 && $fItem['rwd'] != 1){
                $this->genChenLu($type, 1, $cSys['dew']);
            }
            else if ($cSys['type'] == 2){
                $num = floor($fItem['cur'] / $cSys['set']);
                $fItem['cur'] = $fItem['cur'] % $cSys['set'];
                $this->genChenLu($type, $num, $cSys['dew']);
            }
            $fItem['rwd'] = 1;
        }

        $types[] = $fItem;
        $this->info['types'] = $types;
        $this->save();
    }

    public function isNews(){
        $now_t = Game::get_now();
        $types = array();
        $Act6192Model = Master::getAct6192($this->uid);
        $isNew = 0;
        $isSave = false;
        $chen = 0;
        foreach($this->info['cPoints'] as $v){
            if ($v['rwd'] == 1){
                $isSave = true;
                continue;
            }

            if ($now_t - $v['time'] > 86400 && $v['rwd'] != 1){
                $v['rwd'] = 1;
                $chen += $v['chen'];
                $isSave = true;
                continue;
            }

            if ($v['time'] <= $now_t && $v['rwd'] != 1){
                $isNew = 1;
            }
            $types[] = $v;
        }
        if ($chen != 0){
            $Act6192Model->addChenlu($chen);
            Master::back_data($this->uid,$this->b_mol,"autoshou", array('id'=>$chen));
        }
        if ($isSave){
            $this->info['cPoints'] = $types;
            $this->_save();
        }
        return $isNew;
    }

    private function genChenLu($type, $num, $t){
        $ids = array();
        $out = array();
//        error_log($type."    ".$num."   ".$t);
        if (count($this->info['cPoints']) > 100){
            $types = array();
            $now_t = Game::get_now();
            $chen = 0;
            foreach($this->info['cPoints'] as $v){
                if ($v['rwd'] == 1)continue;
                $ids[] = $v['id'];
                if ($v['time'] <= $now_t){
                    $chen += $v['chen'];
                    $v['rwd'] = 1;
                    $out[] = $v;
                    continue;
                }
                $types[] = $v;
            }
            $this->info['cPoints'] = $types;
            if ($chen)Master::add_item($this->uid, KIND_OTHER, 10001, $chen);
        }
        else {
            foreach($this->info['cPoints'] as $v){
                $ids[] = $v['id'];
            }
        }

        if (count($this->info['cPoints']) + $num > 150){
            Master::add_item($this->uid, KIND_OTHER, 10001, $t * $num);
            if (count($out) > 0)Master::back_data($this->uid,$this->b_mol,"chenlu",$out);
            Game::cmd_flow(6190, $type, 2, $t);
            return;
        }

        $n = Game::get_now();
        for($i = 0; $i < $num; $i++){
            $item = array();
            $item['type'] = $type;
            $item['id'] = $this->getId($ids);
            $ids[] = $item['id'];
            $item['time'] = $n + Game::getcfg_param("flower_time");
            $item['chen'] = $t;
            $item['rwd'] = 0;
            $item['sUids'] = array();
            $this->info['cPoints'][] = $item;
            $out[] = $item;
        }
        Game::cmd_flow(6190, $type, 1, $t);
        Master::back_data($this->uid,$this->b_mol,"chenlu",$out);
    }

    public function shou($id,$isYj=false){
        $types = array();
        $out = array();
        $isFind = false;
        foreach ($this->info['cPoints'] as $v) {
            if (($isYj || $v['id'] == $id) && $v['time'] < Game::get_now()){
                if (!$isYj && $v['rwd'] == 1){
                    Master::error(FLOWER_STEAL_SHOU);
                }
                $isFind = true;
                $v['rwd'] = 1;
                $out[] = $v;
                Master::add_item($this->uid, KIND_OTHER, 10001, $v['chen']);
                continue;
            }
            $types[] = $v;
        }
        if (!$isFind){
            Master::error(FLOWER_STEAL_SHOU);
        }
        $this->info['cPoints'] = $types;
        $this->_save();
        Master::back_data($this->uid,$this->b_mol,"chenlu", $out);
    }

    private function getId($ids){
        for ($i = 0; $i < 10; $i++){
            $id = rand(0, 1000000);
            if ($ids[$id] != 1){
                return $id;
            }
        }
        return rand(0, 1000000);
    }

    public function steal($id, $suid){
        $isFind = false;
        $types = array();
        $steal = 0;
        $Act6194Model = Master::getAct6194($this->uid);
        foreach ($this->info['cPoints'] as $v){
            if ($v['id'] == $id){
                $isFind = true;
                $this->info['protect']['ctime'] = empty($this->info['protect']['ctime'])?0:$this->info['protect']['ctime'];
                if(!Game::is_over($this->info['protect']['ctime'])){
                    Master::error(FLOWER_PROTECT_PROTECT_LIMIT);
                }
                if ($v['time'] > Game::get_now() || $v['rwd'] == 1 || in_array($suid, $v['sUids'])){
                    Master::error(FLOWER_STEAL_ERROR);
                }

                $cl = Game::getcfg_info("flowerRain", $v['type']);
                $max = $cl['max'];
                //用户信息
                $UserModel = Master::getUser($this->uid);
                //获得VIP配置
                $vip_cfg_info = Game::getcfg_info('vip',$UserModel->info['vip']);
                //免费转运次数
                $max = $max + ceil($vip_cfg_info['shenji'] * $cl['dew'] / 10);

                if ($v['chen'] <= $max){
                    $v['rwd'] = 1;
                    $Act6192Model = Master::getAct6192($this->uid);
                    $Act6192Model->addChenlu($v['chen']);
                    $Act6194Model -> addLog($suid, -1);
                    $steal = -1;
                    $types[] = $v;
                    continue;
                }

                if ($v['chen'] > $max && !in_array($suid, $v['sUids'])){
                    $v['chen'] -= $cl['steal'];
                    $steal = $cl['steal'];
                    $v['sUids'][] = $suid;
                    $Act6194Model -> addLog($suid, $steal);
                    Master::add_item($suid, KIND_OTHER, 10001, $steal);
                }
            }
            $types[] = $v;
        }
        if (!$isFind){
            Master::error(FLOWER_STEAL_SHOU);
        }
        $this->info['cPoints'] = $types;
        $this->_save();
        return $steal;
    }

    /*
     *  开启御花园保护罩
     */
    public function openProtect($id,$type=0){
        if (isset($this->info['protect']['cd'][$id]) && !Game::is_over($this->info['protect']['cd'][$id])){
            Master::error(ACT_HD_PLAY_CD);
        }
        if (!empty($this->info['protect']['cur'])){
            //判断时间是否过期
            if (Game::is_over($this->info['protect']['ctime'])){
                //过期重置数据
                $this->info['protect']['cd'][$this->info['protect']['cur']] = Game::is_over($this->info['protect']['cd'][$this->info['protect']['cur']])?0:$this->info['protect']['cd'][$this->info['protect']['cur']];
                $this->info['protect']['cur'] = 0;
                $this->info['protect']['ctime'] = 0;
            }else{
                Master::error(ACT_HD_PLAY_CD);
            }

        }
        //获取配置
        $Cover_cfg = Game::getcfg_info('flowershell',$id);
        $costResource = 0;
        if ($type==0){
            //扣除晨露
            $Act6192Model = Master::getAct6192($this->uid);
            $Act6192Model->addChenlu(-$Cover_cfg['dew']);
            $costResource = $Cover_cfg['dew'];
        }else{
            //消耗黄金
            Master::sub_item($this->uid,KIND_ITEM,1,$Cover_cfg['yb']);
            $costResource = $Cover_cfg['yb'];
        }
        //存入保护罩数据
        $this->info['protect']['cur'] = $id;
        $this->info['protect']['ctime'] = $Cover_cfg['time'] + Game::get_now();
        $this->info['protect']['cd'][$id] = $this->info['protect']['ctime']+$Cover_cfg['cd'];
        $this->save();
    }

    /*
     *  返回保护罩数据
     */
    public function back_data_protect(){
        $data = array();
        if (!empty($this->info['protect'])){
            $data['cur'] = $this->info['protect']['cur'];
            $data['ctime'] = $this->info['protect']['ctime'];
            $data['cd'] = array();
            if (!empty($this->info['protect']['cd'])){
                foreach ($this->info['protect']['cd'] as $id=>$time){
                    $data['cd'][] = array('id'=>$id,'over'=>$time);
                }
            }
        }
        return $data;
    }
}














