<?php
require_once "ActBaseModel.php";
/*
 *  门客羁绊解锁条件
 */
class Act2003Model extends ActBaseModel
{
	public $atype = 2003;//活动编号

	public $comment = "门客羁绊解锁";
	public $b_mol = "hero";//返回信息 所在模块
	public $b_ctrl = "jibanUnlock";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
        'unlockInfo' => array(),
    );

    //检测羁绊解锁条件
    public function checkJibanUnlock(){
        $HeroModel = Master::getHero($this->uid);
        foreach($HeroModel->info as $heroid=>$v){
            $this->checkHeroJibanUnlock($heroid);   
        }
    }

    //检测单个hero的羁绊解锁条件
    public function checkHeroJibanUnlock($heroid){
        $jibanUnlockCfg = Game::getcfg_info('jiban_unlock',$heroid);
        $Act6001Model = Master::getAct6001($this->uid);
        $lv = $Act6001Model->getHeroJBLv($heroid);
        foreach($jibanUnlockCfg as $jibanlv => $v){
            if($v['type'] == 3 || $v['type'] == 0){
                continue;
            }
            if(empty($this->info['unlockInfo'][$heroid][$v['type']])){
                $this->info['unlockInfo'][$heroid][$v['type']] = array();
            }
            $typeArr = $this->info['unlockInfo'][$heroid][$v['type']];
            $Act6001Model = Master::getAct6001($this->uid);
            if($lv >= $jibanlv){
                //type 6培养药品使用上限值
                if($v['type'] == 6){
                    if(empty($typeArr)){
                        array_push($this->info['unlockInfo'][$heroid][$v['type']],$v['set']);
                    }else{
                        $this->info['unlockInfo'][$heroid][$v['type']][0] = $v['set'];
                    }
                }else if(!in_array($v['set'][0],$typeArr)){
                    array_push($this->info['unlockInfo'][$heroid][$v['type']],$v['set'][0]);
                }
                
            }
        }
        $this->save();
    }

    public function make_out(){
		$this->outf = $this->info;
    }
	
}
