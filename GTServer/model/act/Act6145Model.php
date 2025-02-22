<?php
require_once "ActBaseModel.php";
/*
 * 伙伴表情包
 */
class Act6145Model extends ActBaseModel
{
    public $atype = 6145;//活动编号
    
    public $comment = "伙伴表情包";
    public $b_mol = "hero";//返回信息 所在模块
    public $b_ctrl = "heroEmoji";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'emojis' => array(),//拥有的表情包类型
    );

    //检测初始免费给的表情包
    public function checkInitEmojis(){
        $heroEmojisCfg = Game::getcfg('hero_emojis');
        foreach($heroEmojisCfg as $v){
            if($v['unlock_type'] == 1){
                if(empty($this->info['emojis'])){
                    $this->info['emojis'] = array();
                }
                if(!in_array($v['id'],$this->info['emojis'])){
                    array_push($this->info['emojis'],$v['id']);
                }
            }
        }
        $this->save();
    }

    //添加表情包
    public function addEmojis($emojis){
        $heroEmojisCfg = Game::getcfg_info('hero_emojis',$emojis);
        if(in_array($emojis,$this->info['emojis'])){
            return;
        }
        array_push($this->info['emojis'],$emojis);
        $this->save();
    }
}