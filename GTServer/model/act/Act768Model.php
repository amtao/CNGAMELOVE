<?php
require_once "ActBaseModel.php";
/*
 * 公会宴会-更换乐师
 */
class Act768Model extends ActBaseModel
{
    public $atype = 768;//活动编号

    public $comment = "公会宴会-更换乐师";
    public $b_mol = "club";//返回信息 所在模块
    public $b_ctrl = "party";//返回信息 所在控制器

    public $_init = array(
        'musician' => 0,//乐师
        'buff' => 0,
    );

    //进入宴会时随机乐师
    public function randMusician(){
        
        if(!empty($this->info['musician'])){
            return;
        }
        $this->info['musician'] = 0;
        $partyMusicCfg = Game::getcfg('party_music');
        $musicArr = array_keys($partyMusicCfg);
        $index = array_rand($musicArr,1);
        $this->info['musician'] = $musicArr[$index];
        $this->save();
    }

    //更换乐师
    public function changeMusician($id){
        $partyMusicCfg = Game::getcfg_info('party_music',$id);
        $this->info['musician'] = $id;
        $this->save();
    }

    //购买特效
    public function buyBuff($id){
        $partyBuffCfg = Game::getcfg_info('party_buff',$id);
        if(!empty($this->info['buff'])){
            Master::error(CLUB_PARTY_BUFF_HAS_BUY);
        }
        foreach($partyBuffCfg['cost'] as $k => $v){
            Master::sub_item2($v);
        }
        $this->info['buff'] = $id;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}