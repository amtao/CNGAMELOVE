<?php
/*
 * 行商全服价格随机
 */
require_once "SevListBaseModel.php";
class Sev707Model extends SevListBaseModel
{
	public $comment = "行商全服价格随机";
	public $act = 707;//活动标签
	public $b_mol = "business";//返回信息 所在模块
	public $b_ctrl = "randPrice";//返回信息 所在控制器
	protected $_use_lock = false;//是否加锁
	public $_init = array(//初始化数据
		/*
		 * array(
		 *
		 *
		 * 
		 * )
		 */
        'info' => array(),
        'refreshTime' => 0,
        'itemIndex' => array(),
    );
    public $outof = NULL;
	
    /*
     * 构造业务输出数据
     */
    public function mk_outf(){
        if(empty($this->info['info'])){
            $this->randItmes();
            $this->randElement();
        }
        $this->outof = $this->info;
        return $this->outof;
    }

    //随机行商所需东西
    public function randElement(){
        $xsChengShiCfg = Game::getcfg('xs_chengshi');
        if(empty($this->info['itemIndex'])){
            $this->randItmes();
        }
        $this->randPrice($xsChengShiCfg);
        
        $this->info['refreshTime'] = Game::get_now();
        $this->save();
    }

    //全服随机价格
    public function randPrice($xsChengShiCfg){
        foreach ($xsChengShiCfg as $key => $value) {
            $buyPriceArr = array();
            $salePriceArr = array();
            $buyPriceArr = $this->randOnePrice($value['pricebuy'],$value['id'],true);
            $salePriceArr = $this->randOnePrice($value['pricesale'],$value['id'],false);
            if(empty($this->info['info'][$value['id']]['buyPrice'])){
                $this->info['info'][$value['id']]['buyPrice'] = array();
            }
            if(empty($this->info['info'][$value['id']]['salePrice'])){
                $this->info['info'][$value['id']]['salePrice'] = array();
            }
            $this->info['info'][$value['id']]['buyPrice'] = $buyPriceArr;
            $this->info['info'][$value['id']]['salePrice'] = $salePriceArr;
        }
    }

    //随机具体价格 分开随机
    public function randOnePrice($price,$cityId,$isBuy){
        $len = count($price);
        $randPriceArr = array();
        $indexArr = $this->info['itemIndex'][$cityId];
        $tempArr = $indexArr['buyIndex'];
        if(!$isBuy){
            $tempArr = $indexArr['saleIndex'];
        }
        foreach($tempArr as $v){
            $randPrice = rand($price[$v][0],$price[$v][1]);
            array_push($randPriceArr,$randPrice);
        }
        return $randPriceArr;
    }

    //全服随机道具
    public function randItmes(){
        $xsChengShiCfg = Game::getcfg('xs_chengshi');
        foreach($xsChengShiCfg as $key => $value){
            $buyItemArr = array();
            $saleItemArr = array();
            $buyItemArr = $this->randOneItem($value['itembuy'],$value['id'],"buyIndex");
            $saleItemArr = $this->randOneItem($value['itemsale'],$value['id'],"saleIndex");
            if(empty($this->info['info'][$value['id']]['buyItem'])){
                $this->info['info'][$value['id']['buyItem']] = array();
            }
            if(empty($this->info['info'][$value['id']]['saleItem'])){
                $this->info['info'][$value['id']]['saleItem'] = array();
            }
            $this->info['info'][$value['id']]['buyItem'] = $buyItemArr;
            $this->info['info'][$value['id']]['saleItem'] = $saleItemArr;
        }
        $this->save();
    }

    public function randOneItem($items,$cityId,$indexName){
        $len = count($items);
        $itemArr = array();
        $indexArr = array();
        $number = range(0,$len-1);
        $indexArr = array_rand($number,3);
        foreach($indexArr as $index){
            $itemArr[] = $items[$index];
        }
        if(empty($this->info['itemIndex'])){
            $this->info['itemIndex'] = array();
        }
        $this->info['itemIndex'][$cityId][$indexName] = $indexArr;
        return $itemArr;
    }
    
    /*
     * 返回协议信息
     */
    public function bake_data(){
        $data = self::mk_outf();
        Master::back_data(0,$this->b_mol,$this->b_ctrl,$data);
    }
	
}
