<?php
require_once "ActBaseModel.php";
/*
 *  伙伴商城购买
 */
class Act2005Model extends ActBaseModel
{
	public $atype = 2005;//活动编号

	public $comment = "伙伴商城购买道具";
	public $b_mol = "hero";//返回信息 所在模块
	public $b_ctrl = "heroshop";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(

    );
    //购买商店中的物品
    public function buyItem($id){
        $heroshopCfg = Game::getcfg_info('hero_shop',$id);
        if($heroshopCfg['unlock_type'] != 0){
            $checkType = $this->getCheckType($heroshopCfg['fenye']);
            $Act2003Model = Master::getAct2003($this->uid);
            $jibanArr = $Act2003Model->info['unlockInfo'][$heroshopCfg['belong_hero']][$checkType];
            foreach($heroshopCfg['wupin'] as $item){
                if(!in_array($item['id'],$jibanArr)){
                    Master::error(FETTER_LEVEL_NOT_BUY);
                }
            }
        }
        if(empty($this->info['buy'][$id])){
            $this->info['buy'][$id] = 0;
        }
        if($heroshopCfg['limit'] != 0 && $this->info['buy'][$id] >= $heroshopCfg['limit']){
            Master::error(LIMIT_ITEM_MAX);
        }
        Master::sub_item($this->uid,KIND_ITEM,10,$heroshopCfg['price']);
        Master::add_item3($heroshopCfg['wupin']);
        $this->info['buy'][$id]++;
        $this->save();
    }

    public function getCheckType($fenye){
        $type = 0;
        switch ($fenye) {
            case 1:
                $type = 7;
            case 2:
                $type = 1;
                break;
            case 3:
                $type = 2;
                break;
            case 4:
                $type = 4;
                break;
            
            default:
                break;
        }
        return $type;
    }

    public function make_out(){
		$this->outf = $this->info;
    }
	
}
