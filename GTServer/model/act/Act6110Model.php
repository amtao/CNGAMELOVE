<?php
require_once "ActBaseModel.php";
/*
 * 书院学习记录
 */
class Act6110Model extends ActBaseModel
{
	public $atype = 6110;//活动编号
	
	public $comment = "珍宝馆";
    public $b_mol = "treasure";//返回信息 所在模块
    public $b_ctrl = "base";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'clearTime' => 0, 
        'groups' => array(),
        'treasure' => array(),
        'score'=>0,
    );

    /*
     * 构造输出结构体
     * 修改保存结构体
     */
    public function make_out()
    {
        $outf = array();
        $info = $this->info;
        $outf['isClear'] = $info['clearTime'] < Game::day_0()?0:1;
        $outf['score'] = $info['score'];
        $this->outf = $outf;
    }

    /*
     * 返回活动信息
     */
    public function back_data(){
        Master::back_data($this->uid,$this->b_mol,"treasure",$this->info['treasure']);
        Master::back_data($this->uid,$this->b_mol,"groups",$this->info['groups']);
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
    }

    /*
     * 打扫
     */
    public function clear(){
        $info = $this->info;
        if ($info['clearTime'] >= Game::day_0()){
            Master::error(TREASURE_CLEAR_TODAY);
        }
        $info['clearTime'] = Game::get_now();
        $trea_reward = Game::getcfg_info('treaReward', count($info['treasure']));

        foreach($trea_reward['reward'] as $v)
        {
            Master::add_item2($v);
        }

        $this ->info = $info;
        $this ->save();
  
    }

    public function addItem($id, $isClip=false){
        $trea = $this->info['treasure'];
//        foreach ($trea as $t){
//            if ($t['id'] == $id){
//                Master::error(TREASURE_REWARD_DUP."2".TREASURE_REWARD_DUP);
//            }
//        }
        //初始化冲榜积分
        $HuodongModel = Master::getHuodong($this->uid);
        $score = empty($this->info['score'])?0:$this->info['score'];
        $treasure = Game::getcfg_info('treasure', $id);
        $isHave = $this->isFind($id);
        if ($isClip){
            Master::sub_item($this->uid,KIND_ITEM, $treasure["tagid"],$treasure["tagnum"]);
        }
        else {
            Master::sub_item($this->uid,KIND_ITEM, $treasure["itemid"],1);
        }
        if ($isHave){
            $this->info['score'] = (empty($this->info['score'])?0:$this->info['score']) + $treasure['twopoints'];
            foreach($treasure['tworeward'] as $v)
            {
                Master::add_item2($v);
            }
        }
        else {
            $this->info['score'] = (empty($this->info['score'])?0:$this->info['score']) + $treasure['points'];
            foreach($treasure['reward'] as $v)
            {
                Master::add_item2($v);
            }
        }

        if (!$isHave){
            $trea[] = array('id'=>$id);
            $this->info['treasure'] = $trea;
        }

        $this->save();


        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(51,1);
        $Act39Model->task_refresh(51);

        //国力庆典
        $Act6205Model = Master::getAct6205($this->uid);
        $Act6205Model->add(1);

        $Redis6110Model = Master::getRedis6110();
        $Redis6110Model->zAdd($this->uid,$this->info['score']);

        //御花园
        // $Act6190Model = Master::getAct6190($this->uid);
        // $Act6190Model->addType(15, 1);

        $HuodongModel->chongbang_huodong('huodong6135',$this->uid,$score);

    }

    public function isFind($id){
        $treas = $this->info['treasure'];
        foreach ($treas as $t){
            if ($t['id'] == $id)return true;
        }
        return false;
    }

    public function getReward($id){
        //判断是否领取过
        $trea_group = Game::getcfg_info('treaGroup', $id);
        $groups = $this->info['groups'];
        $treasurs = $this->info['treasure'];
        foreach ($groups as $g){
            if ($g['id'] == $id){
                Master::error(TREASURE_REWARD_DUP);
            }
        }

        $treasure_cfg = Game::getcfg('treasure');
        foreach($treasure_cfg as $k => $v){
            if ($v['serierid'] == $id){
                foreach ($treasurs as $t){
                    if ($t['id'] == $id){
                        Master::error(TREASURE_REWARD_ENOUGH);
                    }
                }
            }
        }

        foreach($trea_group['reward'] as $v)
        {
            Master::add_item2($v);
        }

        $groups[] = array('id'=>$id);
        $this->info['groups'] = $groups;
        $this->_save();

        Master::back_data($this->uid,$this->b_mol,"groups",$groups);
    }


}














