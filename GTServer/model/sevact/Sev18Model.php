<?php
/*
 * 联盟-红包
 */
require_once "SevBaseModel.php";
class Sev18Model extends SevBaseModel
{
	public $comment = "联盟-红包";
	public $act = 18;//活动标签

	public $_init = array(//初始化数据
        'redlist' => array(),//红包列表
        'robLog' => array(),//领取记录
    );

    /**
     *  添加红包
     *  单个玩家购买特效之后可以发放红包
     *  一个玩家一场宴会只可以发放一次 
     */
    public function add($id,$uid){

        $partyBuffCfg = Game::getcfg_info('party_buff',$id);
        $items = $partyBuffCfg['gift'][0];
        $randArr = Game::random_splite($items['count'],$partyBuffCfg['gift_count']);

        if(empty($this->info['redlist'][$uid])){
            $this->info['redlist'][$uid] = array('name' => '','items' => array());
        }else{
            return;
        }
        $fUserModel = Master::getUser($uid);
        $this->info['redlist'][$uid]['name'] = $fUserModel->info['name'];
        foreach($randArr as $k => $v){
            $kind = empty($items['kind']) ? 1:$items['kind'];
            $redArr = array('itemid' => $items['id'],'count' => $v,'kind' => $kind);
            $this->info['redlist'][$uid]['items'][] = $redArr;
        }
        $this->save();
    }

    /**
     * 抢红包
     * $robUid 抢谁的红包
     * $myUid  谁抢的
     */
    /*public function robRedBag($robUid,$myUid){
        if(empty($this->info['redlist'][$robUid])){
            Master::error(CLUB_PARTY_NO_RED_BAG);
        }
        if(empty($this->info['robLog'][$robUid])){
            $this->info['robLog'][$robUid] = array();
        }
        foreach($this->info['robLog'][$robUid] as $k => $v){
            if($myUid == $v['uid']){
                Master::error(CLUB_PARTY_RED_BAG_SAME);
            }
        }
        $robCount = count($this->info['robLog'][$robUid]);
        if($robCount >= count($this->info['redlist'][$robUid]['items'])){
            Master::error(CLUB_PARTY_RED_BAG_NULL);
        }
        $addItmes = $this->info['redlist'][$robUid]['items'][$robCount];
       
        Master::add_item($myUid,$addItmes['kind'],$addItmes['itemid'],$addItmes['count']);
        $UserModel = Master::getUser($myUid);
        $data = array(
            'uid' => $myUid,
            'name' => $UserModel->info['name'],
            'itemid' => $addItmes['itemid'],
            'count' => $addItmes['count'],
            'kind' => $addItmes['kind'],
        );
        $Act769Model = Master::getAct769($myUid);
        $Act769Model->setRobCount();
        
        $this->info['robLog'][$robUid][] = $data;
        $this->save();
    }*/

    public function removeRobedBag(){
        foreach($this->info['redlist'] as $robUid => $robInfo){
            $robCount = count($this->info['robLog'][$robUid]);
            if($robCount >= count($this->info['redlist'][$robUid]['items'])){
                unset($this->info['redlist'][$robUid]);
                continue;
            }
        }
        $this->save();
    }

    public function robRedBag($myUid){
      
        $robCount = 0;
        $oldRobLists = array();
        
        foreach($this->info['redlist'] as $robUid => $robInfo){
            $robUid = $robUid;
            $robName = $robInfo['name'];
            if(empty($this->info['robLog'][$robUid])){
                $this->info['robLog'][$robUid] = array();
            }
            $isPick = false;
            foreach($this->info['robLog'][$robUid] as $k => $v){
                if($v['uid'] == $myUid){
                    $isPick = true;
                }
            }
            if($isPick){
                continue;
            }
            $robCount = count($this->info['robLog'][$robUid]);
            foreach($this->info['robLog'][$robUid] as $k => $v){
                if($myUid == $v['uid']){
                    continue;
                }
            }
            if($robCount >= count($this->info['redlist'][$robUid]['items'])){
                unset($this->info['redlist'][$robUid]);
                continue;
            }
            $addItmes = $this->info['redlist'][$robUid]['items'][$robCount];
            Master::add_item($myUid,$addItmes['kind'],$addItmes['itemid'],$addItmes['count']);
            $UserModel = Master::getUser($myUid);
            $data = array(
                'uid' => $myUid,
                'name' => $UserModel->info['name'],
                'itemid' => $addItmes['itemid'],
                'count' => $addItmes['count'],
                'kind' => $addItmes['kind'],
            );
            $Act769Model = Master::getAct769($myUid);
            $Act769Model->setRobCount();
            $this->info['robLog'][$robUid][] = $data;
            $robUid = $robUid;
            $robCount = count($this->info['robLog'][$robUid]);
            $oldRobList = $this->info['robLog'][$robUid];
            if($robCount >= count($this->info['redlist'][$robUid]['items'])){
                unset($this->info['redlist'][$robUid]);
            }
            $oldRobLists[$robUid]['name'] = $robName;
            $oldRobLists[$robUid]['list'] = $oldRobList;
            break;
        }
        $this->save();
        return $oldRobLists;
    }

    /*
     * 返回协议信息
     */
    public function bake_data(){
        $this->outof = $this->info;
        Master::back_data(0,'club','redBag',$this->outof);
    }
}