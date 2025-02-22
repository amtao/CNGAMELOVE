<?php
require_once "ActBaseModel.php";
/*
 *  伙伴培养消耗道具信息
 */
class Act2000Model extends ActBaseModel
{
	public $atype = 2000;//活动编号

	public $comment = "门客道具培养情况";
	public $b_mol = "hero";//返回信息 所在模块
	public $b_ctrl = "useItem";//返回信息 所在控制器

	/*
	 * 初始化结构体
	*/
	public $_init =  array(
        'useInfo' => array(),
    );
    
    public function checkIsEnough($heroId,$itemId,$itemCount){
        $HeroModel = Master::getHero($this->uid);
        $hero_info = $HeroModel->check_info($heroId);
        $Act2003Model = Master::getAct2003($this->uid);
        $consumeItemInfo = $Act2003Model->info['unlockInfo'][$heroId][6][0];
        // $heroStarCfg = Game::getcfg_info("hero_star",$hero_info['star']);
        $starItemCount = 0;
        // foreach($heroStarCfg['itemLimit'] as $item){
        //     if($item['itemid'] == $itemId){
        //         $starItemCount = $item['count'];
        //     }
        // }
        foreach($consumeItemInfo as $item){
            if($item['itemid'] == $itemId){
                $starItemCount = $item['count'];
            }
        }
        if($starItemCount <= 0){
            Master::error(HERO_STAR_CFG_ERROR);
        }
        $useInfo = $this->info['useInfo'];
        $list = $useInfo[$heroId];
        if (empty($list)){
            if ($itemCount > $starItemCount) {
                Master::error(HERO_STAR_USE_MAX);
            }else {
                return true;
            }
        }
        foreach($list as $k => $v){
			if ($k == $itemId && ($v + $itemCount) > $starItemCount){
                Master::error(HERO_STAR_USE_MAX);
			}
        }
    }

    public function addUseInfo($heroId,$itemId,$itemCount){
        if ($itemCount <= 0){
			return;
        }
        $useInfo = $this->info['useInfo'];
        if (empty($useInfo[$heroId])){
			$useInfo[$heroId][$itemId] = 0;
		}
        $useInfo[$heroId][$itemId] += $itemCount;
        $this->info['useInfo'] = $useInfo;
		$this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
        Master::$bak_data['u'][$this->b_mol][$this->b_ctrl] = $this->outf;
    }
	
}
