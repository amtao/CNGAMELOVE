<?php
//
require_once "ActFlBaseModel.php";
/*
 * 世界BOSS - 葛二蛋来袭 门客出战列表
 */
class Act5Model extends ActFlBaseModel
{
	public $atype = 5;//活动编号
	
	public $comment = "葛二蛋出战列表";
	public $b_mol = "wordboss";//返回信息 所在模块
	public $b_ctrl = "g2dft";//返回信息 所在控制器

    public $counts = array();
    public $cBuys = array();
    public $costbuy = 0;
    public $add1 = 0;
    public $add2 = 0;
    public $haogan = 0;
    public $jb = 0;
    public $cost1 = 0;
    public $cost2 = 0;

    private function getParam(){
        if (count($this->counts) == 0){
            $ss = Game::getcfg_param("world_boss_cost_numberparam");
            $this->counts = explode('|', $ss);
            $ss = Game::getcfg_param("world_boss_cost_numberbuyparam");
            $this->cBuys = explode('|', $ss);
            $this->costbuy = Game::getcfg_param("world_boss_cost_numberbuycost");
            $this->add1 = Game::getcfg_param("world_boss_awardxishu1");
            $this->add2 = Game::getcfg_param("world_boss_awardxishu2");
            $this->haogan = Game::getcfg_param("world_boss_haogan");
            $this->jb = Game::getcfg_param("world_boss_jiban");
            $this->cost1 = Game::getcfg_param("world_boss_cost_src");
            $this->cost2 = Game::getcfg_param("world_boss_cost_gold");
        }
    }

    public function go_fight($hid, $ftimes){
        $this->getParam();
        $per = 1;
        //增加羁绊消耗材料
        $Act6001Model = Master::getAct6001($this->uid);
        //
        $Sev2Model = Master::getSev2();
        $sevdate = $Sev2Model->outof;//直接调用mk_outf 没经过缓存

        switch($ftimes){
            case 2:
                //减去黄金
                if (!empty($this->cost2)){
                    Master::sub_item($this->uid,KIND_ITEM,1, $this->cost2);
                }
                $per = $this->add2;
                $Act6001Model->addHeroJB($sevdate['heroId'], $per * $this->jb);
                break;
            default:
                //减去银两
                if (!empty($this->cost1)){
                    Master::sub_item($this->uid,KIND_ITEM,3, $this->cost1);
                }
                $per = $this->add1;
                $Act6001Model->addHeroJB($sevdate['heroId'], $per * $this->jb);
                break;
        }

        $lv = $Act6001Model->getHeroJBLv($hid) % 1000;
        $l = count($this->counts);
        $c = $lv > $l?$this->counts[$l-1]:$this->counts[$lv - 1];

        parent::go_fight($hid, intval($c));
        return $per;
    }

    public function cone_back($hid){
        $this->getParam();

        $Act6001Model = Master::getAct6001($this->uid);
        $lv = $Act6001Model->getHeroJBLv($hid) % 1000;
        $l = count($this->counts);
        $c = $lv > $l?$this->counts[$l-1]:$this->counts[$lv - 1];
        $l = count($this->cBuys);
        $c2 = $lv > $l?$this->cBuys[$l-1]:$this->cBuys[$lv - 1];
        //减去黄金
        if (!empty($this->costbuy)){
            Master::sub_item($this->uid,KIND_ITEM,1, $this->costbuy);
        }
        parent::cone_back($hid, intval($c) + intval($c2), 0);
    }
}
