<?php
require_once "ActBaseModel.php";
/*
 * 伙伴空间背景图
 */
class Act6144Model extends ActBaseModel
{
    public $atype = 6144;//活动编号
    
    public $comment = "伙伴空间背景";
    public $b_mol = "hero";//返回信息 所在模块
    public $b_ctrl = "heroBlank";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'blanks' => array(),//拥有的背景
        'useBlanks' => array(),//使用中的背景
    );

    //检测初始免费给的背景
    public function checkInitBlanks(){
        $heroBgCfg = Game::getcfg('hero_bg');
        foreach($heroBgCfg as $v){
            if($v['unlock_type'] == 1){
                if(empty($this->info['blanks'][$v['belong_hero']])){
                    $this->info['blanks'][$v['belong_hero']] = array();
                }
                if(!in_array($v['id'],$this->info['blanks'][$v['belong_hero']])){
                    array_push($this->info['blanks'][$v['belong_hero']],$v['id']);
                }
            }
        }
        $this->save();
    }

    //添加背景
    public function addBlanks($bgId){
        $heroBgCfg = Game::getcfg_info('hero_bg',$bgId);
        if(in_array($bgId,$this->info['blanks'][$heroBgCfg['belong_hero']])){
            return;
        }
        array_push($this->info['blanks'][$heroBgCfg['belong_hero']],$bgId);
        $this->save();
    }

    public function changeBlanks($heroId, $blankid){
        if(!in_array($blankid,$this->info['blanks'][$heroId])){
            Master::error(HAS_NO_BLANKS);
        }
        $this->info['useBlanks'][$heroId] = $blankid;

        $this->save();
    }

}