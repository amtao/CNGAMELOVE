<?php
require_once "ActBaseModel.php";
/*
 *  门客激活信物
 */
class Act2001Model extends ActBaseModel
{
	public $atype = 2001;//活动编号

	public $comment = "门客激活信物";
	public $b_mol = "hero";//返回信息 所在模块
	public $b_ctrl = "activationMail";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
        'tokens' => array(),
    );
    
    public function mailUpLevel($heroId,$mailId){
        $tokens = $this->info['tokens'];
        if(empty($tokens[$heroId][$mailId])){
            Master::error(HERO_MAIL_NOT_ACTIVATION);
        }
        $mToken = $tokens[$heroId][$mailId];
        $tokenCfg = Game::getcfg('tokenLvUp');
        if($mToken['lv'] >= end($tokenCfg)['level']){
            Master::error(HERO_MAIL_MAX_LV);
        }
        //获取信物升级消耗配置表
        $tokenLvCfg = Game::getCfg_info("tokenLvUp",$mToken['lv']+1);
        if (empty($tokenLvCfg)){
            Master::error(HERO_MAIL_MAX_LV);
        }
        if ($mToken['count'] < $tokenLvCfg['cost']){
            Master::error(ITEMS_NUMBER_SHORT);
        }
        $mToken['count'] -= $tokenLvCfg['cost'];
        //升级
        $mToken['lv'] += 1;
        $mToken['prop'] = self::calculationProp($mToken['prop'],$tokenLvCfg['attri']);

        $tokens[$heroId][$mailId] = $mToken;
        $this->info['tokens'] = $tokens;
        $this->save();
    }

    public function tokenActivation($heroId,$tokenId){
        $itemcfg_info = Game::getcfg_info("item",$tokenId);
        if(empty($itemcfg_info)){
            Master::error(PARAMS_ERROR."item".$tokenId);
        }
        $tokeninfo = $this->info['tokens'][$heroId][$tokenId];
        if(empty($tokeninfo)){
            Master::error(HERO_NOT_HAS_TOKEN);
        }
        $proplist = $tokeninfo['prop'];
        if($tokeninfo['isActivation'] == 0){
           $proplist = array(1 => 0,2 => 0,3 => 0,4 => 0);
           foreach($itemcfg_info['type'][2] as $tv){
                if($prop != 5){
                    $proplist[$tv['prop']] = $tv['value'];
                }else{
                    //分类嗑药
                    $yao = array();
                    for($i=0 ; $i < $itemCount ; $i++){
                        $r_id = rand(1,4);//随机属性
                        $yao[$r_id] = empty($yao[$r_id])?1:$yao[$r_id]+1;
                    }
                    //分类嗑药
                    foreach($yao as $yk => $yv){
                        $proplist[$yk] = $yv;
                    }
                }
            }
            $tokeninfo['isActivation'] = 1;
            $tokeninfo['prop'] = $proplist;
            $this->info['tokens'][$heroId][$tokenId] = $tokeninfo;
        }
        $this->save();
    }

    public function calculationProp($proplist,$arri){
        foreach($proplist as $k => $v){
            $proplist[$k] += Game::getCfg_formula()->token_lvUp($v,$arri);
        }
        return $proplist;
    }
    
    
    public function addMailToken($itemId,$itemCount){
        $HeroModel = Master::getHero($this->uid);
        $all_heros = $HeroModel->get_all_heros();

        $itemcfg_info = Game::getcfg_info("item",$itemId);
        if(empty($itemcfg_info)){
            Master::error(PARAMS_ERROR."item".$itemId);
        }
        $result = array();
        foreach($all_heros as $heroid){
            foreach($itemcfg_info['belong_hero'] as $v){
                if($v == $heroid){
                    array_push($result,$heroid);
                }
            }
        }
        $tokenInfo = $this->info['tokens'];
        foreach($result as $v){
            if (empty($tokenInfo[$v][$itemId])){
                $tokenInfo[$v][$itemId] = array('lv'=>1,'count'=>0,'isActivation'=>0);
                $tokenInfo[$v][$itemId]['prop'] = array();
            }
            $tokenInfo[$v][$itemId]['count'] += $itemCount;
            $this->info['tokens'] = $tokenInfo;
        }
		$this->save();
    }

    public function getTokenProp($heroId){
        $prop = array();
        if (empty($this->info['tokens'][$heroId])){
            return array(1 => 0,2 => 0,3 => 0,4 => 0);
        }
        foreach($this->info['tokens'][$heroId] as $v){
            $prop = Game::epadd($prop,$v['prop']);
        }
        return $prop;
    }

    public function getTokenIds($heroId){
        $ids = array();
        foreach($this->info['tokens'][$heroId] as $v){
            array_push($ids,$v);
        }
        return $ids;
    }

    public function make_out(){
        $this->outf = $this->info;
        Master::$bak_data['u'][$this->b_mol][$this->b_ctrl] = $this->outf;
    }
	
}
