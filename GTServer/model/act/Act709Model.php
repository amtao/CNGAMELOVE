<?php

require_once "ActBaseModel.php";
/**
 * 行商--购买信息
 */
class Act709Model extends ActBaseModel{
    public $atype = 709;

    public $comment = "行商-购买信息";
    public $b_mol = "business";//返回信息 所在模块
    public $b_ctrl = "buyInfo";//返回信息 所在控制器

    public $_init = array(
        /**
         * buyInfo array() itemid=>count
         */
        'buy' => array(),
        'buyTotal' => 0,
        'saleTotal' => 0,
    );

    //购买行商中的物品
    public function buyBusinessItem($index,$count){
        //当前城市id
        $Act707Model = Master::getAct707($this->uid);
        $cityId = $Act707Model->info['currentCity'];

        //获取道具背包信息
        $Sev707Model = Master::getSev707();
        $detailInfo = $Sev707Model->mk_outf();
        $itmes = $detailInfo['info'][$cityId]['buyItem'][$index];
        $price = $detailInfo['info'][$cityId]['buyPrice'][$index];

        if($Act707Model->info['goldLeaf'] < $price*$count){
            Master::error(BUSINESS_GOLD_LEAF_NOT_ENOUGH);
        }
        $Act707Model->info['goldLeaf'] -= $price*$count;
        if(empty($this->info['buy'][$cityId][$itmes['id']])){
            $this->info['buy'][$cityId][$itmes['id']] = 0;
        }
        $afterCount = $count + $this->info['buy'][$cityId][$itmes['id']];
        if($itmes['limit'] != 0 && $afterCount > $itmes['limit']){
            Master::error(BUSINESS_LIMIT_MAX);
        }
        $this->info['buyTotal'] += $price*$count;
        //获取背包-对背包进行操作
        $Act708Model = Master::getAct708($this->uid);
        $Act708Model->addBagItem($itmes['id'],$count);

        $this->info['buy'][$cityId][$itmes['id']] += $count;

        Master::$bak_data['a']["msgwin"]["items"][] = array('kind' => $itmes['kind'],'id' => $itmes['id'],'count' => $count);

        $this->save();        
        $Act707Model->save();
    }

    //卖出行商中的物品
    public function saleBusinessItem($index,$count){
        //当前城市id
        $Act707Model = Master::getAct707($this->uid);
        $cityId = $Act707Model->info['currentCity'];

        //获取道具背包信息
        $Sev707Model = Master::getSev707();
        $detailInfo = $Sev707Model->mk_outf();
        $itmes = $detailInfo['info'][$cityId]['saleItem'][$index];
        $price = $detailInfo['info'][$cityId]['salePrice'][$index];

        $Act707Model->info['goldLeaf'] += $price*$count;

        // if(empty($this->info['buy'][$cityId][$itmes['id']])){
        //     Master::error(BUSINESS_NOT_HAVE_ITEMS);
        // }

        $this->info['saleTotal'] += $price*$count;

        //获取背包-对背包进行操作
        $Act708Model = Master::getAct708($this->uid);
        $Act708Model->delBagItem($itmes['id'],$count);

        $this->save();
        $Act707Model->save();
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
