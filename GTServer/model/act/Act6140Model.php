<?php
require_once "ActBaseModel.php";
/*
 * 主角换装
 */
class Act6140Model extends ActBaseModel
{
	public $atype = 6140;//活动编号
	
	public $comment = "主角换装";
    public $b_mol = "clothe";//返回信息 所在模块
    public $b_ctrl = "clothes";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'clothes' => array(),
        'limittime' => array(),
        'score' => 0,
        'suit' => array(),
    );

    /*
	 * 返回活动信息
	 */
    public function back_data(){
        Master::back_data($this->uid,$this->b_mol, $this->b_ctrl, $this->info['clothes']);

        $time = array();
        if (!empty($this->info['limittime'])) {
            foreach ($this->info['limittime'] as $k => $t) {
                $time[] = array("id" => $k, 'time' => $t);
            }
        }
        $suit = array();
        if (!empty($this->info['suit'])) {
            foreach ($this->info['suit'] as $k => $t) {
                $suit[] = array("id" => $k, 'lv' => $t);
            }
        }
        Master::back_data($this->uid,$this->b_mol, "limittime", $time);
        Master::back_data($this->uid,$this->b_mol, "score", array(score=>$this->info["score"]));
        Master::back_data($this->uid, $this->b_mol, "suitlv", $suit);
    }

    public function addSpClothe($id){
        $clothes = $this->info['clothes'];
        if (in_array($id,$clothes)){
            // Master::error(USER_CLOTHE_DUPLICATE);
            return;
        }
        if ($this->isUnlock($id))return;
        $clothe_sys = Game::getcfg_info("use_clothe", $id);

        $this->info['score'] = (empty($this->info['score'])?0:$this->info['score']) + $clothe_sys['score'];
        if ($clothe_sys['limit'] != 0){
            $clothe_sys['limit'][$id] = Game::get_over($clothe_sys['limit']*86400);
        }

        Game::cmd_flow(6140, $id, 2, 2);
        $Redis6140Model = Master::getRedis6140();
        $Redis6140Model->zAdd($this->uid,$this->info['score']);

        $clothes[] = $id;
        $this->info['clothes'] = $clothes;
        $this->save();

        //主线任务 - 刷新
		$Act39Model = Master::getAct39($this->uid);
		$Act39Model->task_add(115, 1);

        $this->getSuitAdd($id);
        if (count($clothe_sys['prop']) > 0){
            $TeamModel = Master::getTeam($this->uid);
            $TeamModel->reset(4);
        }
    }

    public function delSpClothe($id){
        $clothes = $this->info['clothes'];

        $newClothes = array();
        foreach ($clothes as $key => $value) {

            if ( $value == $id ) {
                continue;
            }
            $newClothes[] = $value;
        }

        $Act6141Model = Master::getAct6141($this->uid);
        $actInfo = $Act6141Model->info;
        if ($id == $actInfo["body"]) {
            $actInfo["body"] = 0;
        }

        if ($id == $actInfo["ear"]) {
            $actInfo["ear"] = 0;
        }

        if ($id == $actInfo["background"]) {
            $actInfo["background"] = 0;
        }

        if ($id == $actInfo["effect"]) {
            $actInfo["effect"] = 0;
        }

        if ($id == $actInfo["animal"]) {
            $actInfo["animal"] = 0;
        }

        $Act6141Model->changeClothe($actInfo["head"], $actInfo["body"], $actInfo["ear"], $actInfo["background"], $actInfo["effect"], $actInfo["animal"]);

        $this->info['clothes'] = $newClothes;
        $this->save();

        $TeamModel = Master::getTeam($this->uid);
        $TeamModel->reset(4);
    }

    /*
     * 改变展示伙伴
     */
    public function addClothe($id, $isGold=true){
//        if ($isGold)Master::error("购买服装功能维护中，请小主稍后再试。");
        if(in_array($id,$this->info['clothes'])){
            return;
        }
        if ($isGold && $this->isUnlock($id))return;
        $clothes = $this->info['clothes'];
        $clothe_sys = Game::getcfg_info("use_clothe", $id);
        switch ($clothe_sys['unlock']){
            case 1:
                $UserModel = Master::getUser($this->uid);
                if ($UserModel->info['level'] >= $clothe_sys['para']){
                    $clothes[] = $id;

                    // 额外赠送一套
                    if (isset($clothe_sys["extra"])) {
                        foreach ($clothe_sys["extra"] as $key => $value) {
                            $clothes[] = $value;
                        }
                    }
                }
                else if ($isGold){
                    $cost = $clothe_sys['money'];
                    if ($cost["count"] != 0){
                        Master::sub_item($this->uid,KIND_ITEM, $cost["itemid"], $cost["count"]);
                    }
                    Game::cmd_flow(6140, $id, 1, 1);
                    $clothes[] = $id;
                }
                break;
            case 2:
                $cost = $clothe_sys['money'];
                if (!empty($cost)){
                    Master::sub_item($this->uid,KIND_ITEM, $cost["itemid"], $cost["count"]);
                    Game::flow_php_record($this->uid, 7, $id, 1, '', $cost["count"]);
                }
                Game::cmd_flow(6140, $id, 1, 1);
                $clothes[] = $id;
                break;
            default:
                return;
                break;
        }

        $this->info['score'] = (empty($this->info['score'])?0:$this->info['score']) + $clothe_sys['score'];
        if ($clothe_sys['limit'] != 0){
            $clothe_sys['limit'][$id] = Game::get_over($clothe_sys['limit']*86400);
        }

        $Redis6140Model = Master::getRedis6140();
        $Redis6140Model->zAdd($this->uid,$this->info['score']);

        $this->info['clothes'] = $clothes;
        $this->save();

        $this->getSuitAdd($id);
        if (count($clothe_sys['prop']) > 0 && $isGold){
            $TeamModel = Master::getTeam($this->uid);
            $TeamModel->reset(4);
        }
        if ($clothe_sys['pet_type']>0){
            $Act20Model = Master::getAct20($this->uid);
            $Act20Model->addition($clothe_sys['pet_type'],true);
        }
    }

    public function getAddProp(){
        $list = Game::getcfg("use_clothe");
        $UserModel = Master::getUser($this->uid);
        $lv = $UserModel->info['level'];
        $allJob = json_decode($UserModel->info['allJob'],true);
        $ep = array(1 => 0,2 => 0,3 => 0,4 => 0);
        foreach ($list as $v) {
            if ($this->isUnlock($v['id']) ||
                ($v['unlock'] == 1 && $v['para'] <= $lv)){
                if ($v['prop_type'] != 1) continue;
                foreach ($v['prop'] as $add){
                    if ($add['prop'] == 5){
                        for ($i=1;$i<=count($ep);$i++){
                            $ep[$i] += $add['value'];
                        }
                        continue;
                    }
                    $ep[$add['prop']] += $add['value'];
                }
            }
        }
        //增加额外的属性
        //新增服装锦衣华服等级上的属性
        $Act756Model = Master::getAct756($this->uid);
        $extraEp = $Act756Model->getPropCount(2);
        if(!empty($extraEp)){
            $ep = Game::epadd($extraEp,$ep);    
        }
    
        $Act757Model = Master::getAct757($this->uid);
        $activate = $Act757Model->info['activateSmallProp'][1];
        if(!empty($activate)){
            if(!empty($activate[1])){
                $ep = Game::epadd($activate,$ep);
            }
        }

        $listJ = Game::getcfg("clothe_job");
        foreach ($listJ as $v){
            $IsOk = false;
            $num = count($allJob);
            for($i=0;$i<$num;$i++){
                if($v['id']==$allJob[$i]){
                    $IsOk = true;
                    break;
                }
            }
            if($IsOk){
                if(($v['prop']) != NULL) {
                    if($v['prop']['prop']==5){
                        for ($i=1;$i<=count($ep);$i++){
                            $ep[$i] += $val;
                        }
                    }else {
                        $ep[$v['prop']['prop']] += $v['prop']['value'];
                    }
                }
            }
        }
       
        return $ep;
    }

    public function getAddPercentage(){
        $list = Game::getcfg("use_clothe");
        $UserModel = Master::getUser($this->uid);
        $lv = $UserModel->info['level'];
        $ep = array();
        foreach ($list as $v) {
            if ($this->isUnlock($v['id']) ||
                ($v['unlock'] == 1 && $v['para'] <= $lv)){
                if ($v['prop_type'] > 1){
                    foreach ($v['prop'] as $add){
                        $index = $this->getAddPropType($v['prop_type']);
                        if ($add['prop'] == 5){
                            for ($i=1;$i<5;$i++){
                                $ep[$index][$i] += ($add['value']/10000);
                            }
                            continue;
                        }
                        $ep[$index][$add['prop']] += ($add['value']/10000);
                    }
                }
            }
        }
        return $ep;
    }

    public function getAddPropType($type){
        $key = '';
        switch ($type){
            case 2://百分比伙伴属性
                $key = 'hero';
                break;
            case 3://百分比服饰属性
                $key = 'clothe';
                break;
            case 4://百分比徒弟属性
                $key = 'son';
                break;
            case 5://卡牌加成百分比
                $key = 'card';
                break;
            case 6://总属性百分比加成
                $key = 'all';
                break;
            case 7://总属性百分比加成
                $key = 'baowu';
                break;
        }
        return $key;
    }

    public function getSuitAdd($newid = 0){
        $list = Game::getcfg("clothe_suit");
        $ep = array(1 => 0,2 => 0,3 => 0,4 => 0);
        $isChange = false;
        foreach ($list as $v) {
            $flag = true;
            $isFind = false;
            foreach ($v['clother'] as $id){
                if ($newid == $id){
                    $isFind = true;
                }
                if (!$this->isUnlock($id)){
                    $flag = false;
                    break;
                }
            }
            if ($flag){
                if ($isFind)$isChange = count($v['ep']) > 0;
                $lv = $this->info['suit'][$v['id']];
                $lv = empty($lv)?1:$lv;
                $lvSys = Game::getcfg_info('clothe_suit_prop', $v['lvup']*1000 + $lv);
                foreach ($lvSys['ep'] as $prop){
                    if ($prop['prop'] == 5){
                        for ($i=1;$i<=count($ep);$i++){
                            $ep[$i] += $prop['value'];
                        }
                        continue;
                    }
                    $ep[$prop['prop']] += $prop['value'];
                }
            }
        }
        if ($isChange){
            $TeamModel = Master::getTeam($this->uid);
            $TeamModel->reset(5);
            $TeamModel->back_hero();
        }
        return $ep;
    }

    public function addUseLvClothe($lv){
        $list = Game::getcfg("use_clothe");
        foreach ($list as $v){
            if ($v['unlock'] == 1 && $v['para'] == $lv){
                $this->addClothe($v['id'], false);
            }
        }
    }

    public function isUnlock($id){
        if ($id == 0)return true;
        if (!in_array($id, $this->info['clothes'])){
            $sys = Game::getcfg_info("use_clothe", $id);
            if ($sys['unlock'] == 1){
                $UserModel = Master::getUser($this->uid);
                if ($UserModel->info['level'] >= $sys['para']){
                    $this->addClothe($id, false);
                    return true;
                }
            }
            return false;
        }
        $time = !empty($this->info['limittime']) && !empty($this->info['limittime'][$id])?$this->info['limittime'][$id]:0;
        if ($time != 0 && $time < Game::get_now())return false;
        return true;
    }

    public function lvupSuit($id){
        $suitSys = Game::getcfg_info("clothe_suit", $id);
        $lv = empty($this->info['suit']) || empty($this->info['suit'][$id])?1:$this->info['suit'][$id];
        $lvSys = Game::getcfg_info('clothe_suit_prop', $suitSys['lvup']*1000 + $lv);
        if ($lvSys['cost'] == 0)return;
        Master::sub_item($this->uid, KIND_ITEM, Game::getcfg_param("clother_item"), $lvSys['cost']);
        if (empty($this->info['suit'])){
            $this->info['suit'] = array();
        }
        $this->info['suit'][$id] = $lv + 1;
        $this->_save();

        $suit = array();
        if (!empty($this->info['suit'])) {
            foreach ($this->info['suit'] as $k => $t) {
                $suit[] = array("id" => $k, 'lv' => $t);
            }
        }
        Master::back_data($this->uid, $this->b_mol, "suitlv", $suit);

        $TeamModel = Master::getTeam($this->uid);
        $TeamModel->reset(5);
        $TeamModel->back_hero();
    }

    public function getOneProp($userClotheId){
        $clothe = Game::getcfg_info("use_clothe",$userClotheId);
        $UserModel = Master::getUser($this->uid);
        $lv = $UserModel->info['level'];
        $ep = array(1 => 0,2 => 0,3 => 0,4 => 0);
        if ($this->isUnlock($clothe['id']) ||
            ($clothe['unlock'] == 1 && $clothe['para'] <= $lv)){
            if ($clothe['prop_type'] != 1) continue;
            foreach ($clothe['prop'] as $add){
                if ($add['prop'] == 5){
                    for ($i=1;$i<=count($ep);$i++){
                        $ep[$i] += $add['value'];
                    }
                    continue;
                }
                $ep[$add['prop']] += $add['value'];
            }
        }   
        return $ep;
    }

}














