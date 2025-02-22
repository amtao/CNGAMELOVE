<?php
require_once "ActBaseModel.php";
/*
 *  羁绊数据
 */
class Act6003Model extends ActBaseModel
{
	public $atype = 6003;//活动编号

	public $comment = "玩家首次找到门客寻访";
	public $b_mol = "jingYing";//返回信息 所在模块
	public $b_ctrl = "weipai";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
        'coin' => array(),  //
        'food' => array(),  //
        'army' => array(),  //
	);

	public function getAddEp($type){
        $keys = array('', '', 'coin', 'food', 'army');
        $key = $keys[$type];
        $arr = $this->info[$key];
        $TeamModel= Master::getTeam($this->uid);

        $ep = 0;

        foreach ($arr as $id){
            if ($id === 0)continue;
            $hero = $TeamModel->info['heros'][$id];
            $e = empty($hero)?0:$hero['aep']['e'.$type];
            if (is_string($e)){
                $ep += intval($e);
            }
            else {
                $ep += $e;
            }
        }
        return $ep;
    }

    public function replaceHero($type, $heroId1, $heroId2, $heroId3){
        $HeroModel = Master::getHero($this->uid);
        if ($heroId1 != 0)$HeroModel ->check_info($heroId1);
        if ($heroId2 != 0)$HeroModel ->check_info($heroId2);
        if ($heroId3 != 0)$HeroModel ->check_info($heroId3);

        $keys = array('', '', 'coin', 'food', 'army');
        $key = $keys[$type];
	    $arr = [];

        if ($heroId1 == 0 && $heroId2 == 0 && $heroId3 == 0) {
            $this->info[$key] = $arr;
            $this->save();
            return;
        }

        foreach($this->info as $k => $items){
	        if ($k == $key)continue;
	        if (in_array($heroId1, $items)){
	            $heroId1 = 0;
            }
            if (in_array($heroId2, $items)){
                $heroId2 = 0;
            }
            if (in_array($heroId3, $items)){
                $heroId3 = 0;
            }
        }

        if ($heroId1 == 0 && $heroId2 == 0 && $heroId3 == 0)return;
        if ($heroId1 != 0 && $this->isCanAdd(count($arr)+1, $type)){
            $arr[] = $heroId1;
        }
        if ($heroId2 != 0 && $this->isCanAdd(count($arr)+1, $type)){
            $arr[] = $heroId2;
        }
        if ($heroId3 != 0 && $this->isCanAdd(count($arr)+1, $type)){
            $arr[] = $heroId3;
        }

        $this->info[$key] = $arr;
        $this->save();
    }

    private function isCanAdd($c, $type){
        //配置
        $jyweipai = Game::getcfg_info('jyWeipai',$type);

        //配置溢出 已经领完?
        if (!isset($jyweipai[$c])){
            Master::error(OPERATE_JY_WEIPAI_LIMIT.$c);
        }
        if (!$this->isOpen($jyweipai[$c])) {
            Master::error(OPERATE_JY_WEIPAI_UNOPEN);
        }
        return true;
    }

    private function isOpen($weipai){
	    switch($weipai['condition']){
            case 1:
                $Act39Model = Master::getAct39($this->uid);
                return $Act39Model->info['id'] > $weipai['value'];
            case 2:
                $ss = explode(',', $weipai['value']);
                $cj_rwd_info = Game::getcfg_info('cj_info',$ss[0]);
                $act36Model = Master::getAct36($this->uid);
                return $cj_rwd_info == null || $cj_rwd_info[$ss[1]]['need'] <= $act36Model->info[$ss[0]]['num'];
        }
	    return true;
    }


}
