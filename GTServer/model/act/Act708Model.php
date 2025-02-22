<?php

require_once "ActBaseModel.php";
/**
 * 行商--背包
 */
class Act708Model extends ActBaseModel{
    public $atype = 708;

    public $comment = "行商-信息";
    public $b_mol = "business";//返回信息 所在模块
    public $b_ctrl = "bagInfo";//返回信息 所在控制器

    public $_init = array(
        /**
         * 背包 array() itemid=>count
         */
        'bag' => array(),
        'totalCount' => 0,
    );

    //插入道具
    public function addBagItem($itemid,$count){
        if(empty($this->info['bag'][$itemid])){
            $this->info['bag'][$itemid] = 0;
        }
        $this->info['bag'][$itemid] += $count;
        
        $maxCount = Game::getcfg_param('xingshang_beibao');
        //判断是否有月卡
        $Act68Model = Master::getAct68($this->uid);
        $buyMoonCard = $Act68Model->find_ka(1);
        if($buyMoonCard == 1){
            $maxCount = Game::getcfg_param('xingshang_beibao_yueka');
        }
        if(empty($this->info['totalCount'])){
            $this->info['totalCount'] = 0;
        }
        $this->info['totalCount'] += $count;

        if($this->info['totalCount'] > $maxCount){
            Master::error(BUSINESS_BAG_FULL);
        }
        $this->save();
    }

    //扣除背包中的道具
    public function delBagItem($itemid,$count){
        if($this->info['bag'][$itemid] < $count){
            Master::error(BUSINESS_SALE_ITEM_NOY_ENOUTH);
        }
        $this->info['bag'][$itemid] -= $count;
        $this->info['totalCount'] -= $count;
        if($this->info['bag'][$itemid] == 0){
            unset($this->info['bag'][$itemid]);
        }
        $this->save();
    }

    //清除数据
    public function remove_data(){
        $this->info = $this->_init;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }
}
