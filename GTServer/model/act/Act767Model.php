<?php
require_once "ActBaseModel.php";
/*
 * 公会宴会-提交资源
 */
class Act767Model extends ActBaseModel
{
    public $atype = 767;//活动编号

    public $comment = "公会宴会-提交资源";
    public $b_mol = "club";//返回信息 所在模块
    public $b_ctrl = "party";//返回信息 所在控制器

    public $_init = array(
        'resourceList' => 0,//资源列表
        'submitTimes' => 0,//提交次数
        'buyTimes' => 0,//购买次数
        'refreshTimes' => 0,//刷新次数
    );

    //提交
    public function submit(){
        $Act40Model = Master::getAct40($this->uid);
        $cid = $Act40Model->info['cid'];
        if(empty($cid)){
			Master::error(CLUB_IS_NULL);
		}
        $UserModel = Master::getUser($this->uid);
        $vipLv = $UserModel->info['vip'];
        $vipCfg = Game::getcfg_info('vip',$vipLv);
        if(empty($this->info['submitTimes'])){
            $this->info['submitTimes'] = 0;
        }
        if(empty($this->info['buyTimes'])){
            $this->info['buyTimes'] = 0;
        }
        if($this->info['submitTimes'] >= $this->info['buyTimes'] + $vipCfg['partytask']){
            Master::error(CLUB_PARTY_SUBMIT_MAX);
        }
        $totalAdd = 0;
        $itemArr = array();
        $consumeArr = array();
        foreach($this->info['resourceList'] as $k => $v){
            $partyTasksCfg = Game::getCfg_info('party_task',$k);
            $cost = $partyTasksCfg['unit']*$v['count'];
            $consumeArr[$k] += $cost;
            foreach($v['getRwd'] as $id => $add){
                if($add['itemid'] == 120){
                    $totalAdd += $add['count']; 
                }else{
                    $itemArr[$add['itemid']]['kind'] = $add['kind'];
                    $itemArr[$add['itemid']]['count'] += $add['count'];
                }
            }
        }

        foreach ($consumeArr as $itemid => $itemcount) {
            Master::sub_item($this->uid,KIND_ITEM,$itemid,$itemcount);
        }

        foreach($itemArr as $k => $v){
            Master::add_item($this->uid,$v['kind'],$k,$v['count']);
        }
      
        $Sev17Model = Master::getSev17($cid);
        $Sev17Model->addResource($totalAdd);

        $this->info['submitTimes']++;
        
        $this->refreshList();

        $this->save();
    }

    //购买提交次数
    public function buyCount(){
        $Act40Model = Master::getAct40($this->uid);
        $cid = $Act40Model->info['cid'];
        if(empty($cid)){
			Master::error(CLUB_IS_NULL);
		}
        $UserModel = Master::getUser($this->uid);
        $vipLv = $UserModel->info['vip'];
        $vipCfg = Game::getcfg_info('vip',$vipLv);
        if($this->info['buyTimes'] >= $vipCfg['partytask_buy']){
            Master::error(BUY_COUNT_MAX);
        }
        $cost = Game::getcfg_param('club_partytask');
        $cost += $this->info['buyTimes']*$cost;
        Master::sub_item($this->uid,KIND_ITEM,1,$cost);
        $this->info['buyTimes']++;
        $this->save();
    }

    //每个玩家提交资源数量不同
    //每日刷新次数
    public function randResource($isRefresh = false){
        if(!empty($this->info['resourceList']) && !$isRefresh){
            return;
        }
        if($isRefresh){
            $cost = Game::getcfg_param('club_partyrefresh');
            $cost += $this->info['refreshTimes']*$cost;
            Master::sub_item($this->uid,KIND_ITEM,1,$cost);
            $this->info['refreshTimes']++;
        }
        $this->refreshList();
        $this->save();
    }

    //刷新资源列表
    public function refreshList(){
        $this->info['resourceList'] = array();
        $partyTasks = Game::getcfg('party_task');
        $itemIdArr = array_keys($partyTasks);
        $tempArr = Game::array_rand($itemIdArr,2);
        foreach($tempArr as $k => $v){
            //随机份数
            $randCount = rand($partyTasks[$v]['count'][0],$partyTasks[$v]['count'][1]);
            if(empty($this->info['resourceList'][$v])){
                $this->info['resourceList'][$v] = array('count' => 0,'getRwd' => array());
            }
            $this->info['resourceList'][$v]['count'] = $randCount;
            $items = array();
            foreach($partyTasks[$v]['rwd'] as $k => $_items){
                $item = array();
                $item['itemid'] = $_items['id'];
                $item['count'] = ceil($_items['count']*$randCount);
                $item['kind'] = $_items['kind'];
                $items[] = $item;
            }
            $this->info['resourceList'][$v]['getRwd'] = $items;
        }
    }

    public function make_out(){
        $this->outf = $this->info;
    }
}