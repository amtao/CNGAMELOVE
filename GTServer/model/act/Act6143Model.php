<?php
require_once "ActBaseModel.php";
/*
 * 伙伴换装
 */
class Act6143Model extends ActBaseModel
{
    public $atype = 6143;//活动编号
    
    public $comment = "伙伴换装";
    public $b_mol = "hero";//返回信息 所在模块
    public $b_ctrl = "heroDress";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'clothes' => array(),
        'limittime' => array(),
        'herodress' => array(),
    );

    public function addSpClothe($id){
        $clothes = $this->info['clothes'];
        if (in_array($id,$clothes)){
            // Master::error(USER_CLOTHE_DUPLICATE);
            return;
        }
        if ($this->isUnlock($id))return;
        $clothe_sys = Game::getcfg_info("hero_dress", $id);

        if ($clothe_sys['limit'] != 0){
            $clothe_sys['limit'][$id] = Game::get_over($clothe_sys['limit']*86400);
        }

        Game::cmd_flow(6143, $id, 2, 2);

        $clothes[] = $id;
        $this->info['clothes'] = $clothes;
        $this->save();

        if (isset($clothe_sys['dressstory']) && !empty($clothe_sys['dressstory'])) {

            $Act6005Model = Master::getAct6005($this->uid);
            $Act6005Model ->addItem($clothe_sys['dressstory']);
        }

        $HeroModel = Master::getHero($this->uid);
        if (count($clothe_sys['prop']) > 0 && $HeroModel->check_info($clothe_sys['heroid'], true) ){

            $TeamModel = Master::getTeam($this->uid);
            $TeamModel->reset(1);
            $TeamModel->back_hero();//返回门客信息.
        }
    }

    /*
     * 改变展示伙伴
     */
    public function addClothe($id, $isGold=true){
//        if ($isGold)Master::error("购买服装功能维护中，请小主稍后再试。");
        if ($isGold && $this->isUnlock($id))return;
        $clothes = $this->info['clothes'];
        $clothe_sys = Game::getcfg_info("hero_dress", $id);
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
                    Game::cmd_flow(6143, $id, 1, 1);
                    $clothes[] = $id;
                }
                break;
            case 2:
                $cost = $clothe_sys['money'];
                if (!empty($cost)){
                    Master::sub_item($this->uid,KIND_ITEM, $cost["itemid"], $cost["count"]);
                    Game::flow_php_record($this->uid, 7, $id, 1, '', $cost["count"]);
                }
                Game::cmd_flow(6143, $id, 1, 1);
                $clothes[] = $id;
                break;
            default:
                return;
                break;
        }

        if ($clothe_sys['limit'] != 0){
            $clothe_sys['limit'][$id] = Game::get_over($clothe_sys['limit']*86400);
        }

        $this->info['clothes'] = $clothes;
        $this->save();

        if (count($clothe_sys['prop']) > 0 && $isGold){
            $TeamModel = Master::getTeam($this->uid);
            $TeamModel->reset(1);
        }
    }

    public function getAddProp(){

        $heroInfo = array();
        $dress_sys = Game::getcfg("hero_dress");
        $clothes = $this->info['clothes'];
        foreach ($dress_sys as $dressId => $dressInfo){

            $heroId = $dressInfo["heroid"];
            if (!isset($heroInfo[$heroId])) {
                $heroInfo[$heroId] = array("e1" => 0, "e2" => 0, "e3" => 0, "e4" => 0);
            }

            if ($this->isUnlock($dressId)){
                foreach ($dressInfo['prop'] as $add){
                    if ($add['prop'] == 5){
                        for ($i=1;$i<=count($heroInfo[$heroId]);$i++){
                            $heroInfo[$heroId]["e".$i] += $add['value'];
                        }
                        continue;
                    }
                    $heroInfo[$heroId]["e".$add['prop']] += $add['value'];
                }
            }
        }
        return $heroInfo;
    }

    public function isUnlock($id){
        if ($id == 0)return true;
        if (!in_array($id, $this->info['clothes'])){
            $sys = Game::getcfg_info("hero_dress", $id);
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

    public function changeClothe($heroId, $dressId){

        $clothes = $this->info['clothes'];

        if ($dressId > 0) {
            if (!$this->isUnlock($dressId)){
                Master::error(USER_SAVE_LOST);
            }
            $this->info["herodress"][$heroId] = $dressId;
        }else{
            $this->info["herodress"][$heroId] = 0;
        }

        $this->save();
    }

}