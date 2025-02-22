<?php 
require_once "ActBaseModel.php";
/*
 * 锦衣裁剪
 */

class Act756Model extends ActBaseModel{
    
    public $atype = 756;//活动编号

	public $comment = "锦衣裁剪";
	public $b_mol = "clothe";//返回信息 所在模块
    public $b_ctrl = "brocade";//返回信息 所在控制器
    
    public $_init = array(
        'suitBrocadeLv' => array(),//套装的锦衣裁剪等级
        'extraProp' => array(),//额外属性增加
    );

    //锦衣裁剪升级
    public function brocadeUpLv($suitId){
        $Act6140Model = Master::getAct6140($this->uid);
        $clothes = $Act6140Model->info['clothes'];
        $clothSuitCfg = Game::getcfg_info('clothe_suit',$suitId);
        $result = array_diff($clothSuitCfg['clother'],$clothes);
        if(!empty($result)){
            Master::error(CLOTHE_SUIT_NOT_ENOUGH);
        }
        if(empty($this->info['suitBrocadeLv'][$suitId])){
            $this->info['suitBrocadeLv'][$suitId] = 0;
        }
        $nextLv = $this->info['suitBrocadeLv'][$suitId] + 1;
        $brocadeCfg = Game::getcfg_info('userSuitLv2',$suitId);
        $maxLv = end($brocadeCfg)['lv'];
        if($nextLv > $maxLv){
            Master::error(CLOTHE_JINYI_LV_MAX);
        }
        foreach($brocadeCfg[$nextLv]['cost'] as $items){
            Master::sub_item2($items);
        }
        
        $this->info['suitBrocadeLv'][$suitId] = $nextLv;
        $this->save();
        //升级之后解锁属性
        $this->propAdd($brocadeCfg[$nextLv],$suitId);
    }

    //属性增加
    /**
    * 1 获得特殊装扮（特效）
    * 2 套装衣服属性m增加X点
    * 3 华服值增加X点
    * 4 解锁心忆槽位
    * 5 战斗时伙伴气势增加X
    * 6 政务次数上限增加X
    * 7 每日随机问候获得奖励次数增加X
    * 8 伙伴邀约次数上限增加X
     */
    public function propAdd($oBrocadeCfg,$suitId){
        $type = $oBrocadeCfg['type'];
        switch ($type) {
            case '1':
                $userClotheCfg = Game::getcfg_info('clothe_suit',$suitId);
                $clotheId = $userClotheCfg['clother'][0];
                if(empty($this->info['extraProp'][$type][$clotheId])){
                    $this->info['extraProp'][$type][$clotheId] = $oBrocadeCfg['rwd'][0];
                }
                break;
            case '2':
                $clotheSuitCfg = Game::getcfg_info('clothe_suit',$suitId);
                $clotheNum = count($clotheSuitCfg['clother']);
                if(empty($this->info['extraProp'][$type])){
                    $this->info['extraProp'][$type] = array(1 => 0,2 => 0,3 => 0,4 => 0);
                }
                $epType = $oBrocadeCfg['rwd'][0];
                $epValue = $oBrocadeCfg['rwd'][1];
                $this->info['extraProp'][$type][$epType] += $epValue*$clotheNum;
                break;
            case '3':
                $Act6140Model = Master::getAct6140($this->uid);
                $Act6140Model->info['score'] += $oBrocadeCfg['rwd'][0];
                $Act6140Model->save();
                $Redis6140Model = Master::getRedis6140();
                $Redis6140Model->zAdd($this->uid,$Act6140Model->info['score']);
                break;
            case '4':
                $Act757Model = Master::getAct757($this->uid);
                $Act757Model->checkSlotUnlock($suitId);
                break;
            case '5':
            case '6':
            case '7':
            case '8':
                if(empty($this->info['extraProp'][$type])){
                    $this->info['extraProp'][$type] = 0;
                }
                $this->info['extraProp'][$type] += $oBrocadeCfg['rwd'][0];
                break;
            case '9'://百分比 记得除100
                if(empty($this->info['extraProp'][$type][$oBrocadeCfg['rwd'][0]])){
                    $this->info['extraProp'][$type][$oBrocadeCfg['rwd'][0]] = 0;
                }
                $addValue = $oBrocadeCfg['rwd'][1]/100;
                $this->info['extraProp'][$type][$oBrocadeCfg['rwd'][0]] += $addValue;
            default:
                break;
        }
        $this->save();
    }

    public function getPropCount($type){
        
        if($type == 2 && empty($this->info['extraProp'][$type])){
            $this->info['extraProp'][$type] = array(1 => 0,2 => 0,3 => 0,4 => 0);
        }else{
            if(empty($this->info['extraProp'][$type])){
                $this->info['extraProp'][$type] = 0;
            }
        }
        return $this->info['extraProp'][$type];
    }



    public function make_out(){
        $this->outf = $this->info;
    }

}
