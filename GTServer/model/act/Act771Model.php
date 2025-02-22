<?php
require_once "ActBaseModel.php";
/*
 * 公会宴会-花签
 */
class Act771Model extends ActBaseModel
{
    public $atype = 771;//活动编号

    public $comment = "公会宴会-花签";
    public $b_mol = "club";//返回信息 所在模块
    public $b_ctrl = "party";//返回信息 所在控制器

    public $_init = array(
        'isThrow' => 0,//是否投过壶
        'isPick' => 0,//是否领取奖励
    );

    //是否投过壶
    public function setFpoint(){
        if($this->info['isThrow'] >= 1){
            Master::error(CLUB_PARTY_HAS_THROW);
        }
        $this->info['isThrow'] = 1;
        $this->save();
    }

    //领取投壶奖励
    public function pickAward($cid){
        if($this->info['isPick'] >= 1){
            Master::error(HAS_PICK_AWARD);
        }
        if(empty($this->info['isPick'])){
            $this->info['isPick'] = 0;
        }
        $isPick = false;
        $Sev100Model = Master::getSev100($cid);
        for($i = 1; $i <= 3; $i++){
            $awardInfo = $Sev100Model->info['awardInfo'][$i];
            if(empty($awardInfo[$this->uid])){
                continue;
            }
            $isPick = true;
            $iteminfo = $awardInfo[$this->uid];
            Master::add_item($this->uid,$iteminfo['kind'],$iteminfo['itemid'],$iteminfo['count']);
        }
        if(!$isPick){
            Master::error(CLUB_PARTY_NO_THROW);
        }
 
        $this->info['isPick'] = 1;
        $this->save();
    }

    public function removeData(){
        $this->info = $this->_init;
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}