<?php
require_once "ActBaseModel.php";
/*
 * 伙伴知己-闲谈
 */
class Act6138Model extends ActBaseModel
{
    public $atype = 6138;//活动编号
    public $comment = "伙伴知己-闲谈";
    public $b_mol = "hero";//返回信息 所在模块
    public $b_ctrl = "heroChat";//子类配置
    public $prob = 10;//概率增长值

    /*
     * 初始化结构体
     */
    public $_init =  array(
//                'hero'=>array(heroid=>array('time'=>时间戳,'prob'=>概率,'get'=>领奖状态),),
//                'wife'=>array(wifeid=>array('time'=>时间戳,'prob'=>概率,'get'=>领奖状态),),
    );


    //构造输出函数 $id:伙伴知己id  $type:[hero,wife]伙伴或知己类型
    public function chat($id,$type){
        //先随机一个数
        $probnum = rand(1,100);
        //获取当前概率
        $prob = isset($this->info[$type][$id]['prob'])?$this->info[$type][$id]['prob']:$this->prob;
        if ($prob > 100){
            $prob = 0;
        }
        //闲谈时间限制
//        if (isset($this->info[$type][$id]['time']) && Game::get_now() < $this->info[$type][$id]['time']+60){
//            Master::error('闲谈可不能太频繁哦~');
//        }
        //每天重置
        if (isset($this->info[$type][$id]['time']) && $this->info[$type][$id]['time'] < Game::day_0()){
            $this->info[$type][$id]['get'] = 0;
            $this->info[$type][$id]['prob'] = $this->prob;
        }

        //获取剧情配置信息
        $story =array();
        $storys = Game::getcfg($type.'talkstory');
        foreach ($storys as $val){
            if ($val[$type.'id'] == $id){
                $story = $val;
            }
        }
        if (empty($story)){
            Master::error(NOT_XIANTAN_CONFIG_DATA);
        }
        $key = count($story['storyid1'])-1;
        //返回闲谈类型
        if ($probnum <=$prob){
            //触发剧情且领过奖励
            if (isset($this->info[$type][$id]['get']) && !empty($this->info[$type][$id]['get'])){
                $storyid = $story['storyid2'][rand(0,$key)];
                $this->info[$type][$id]['prob'] = 0;
                Master::back_data($this->uid,$type,$type.'Chat',array('chatType'=>1,'stroyid'=>$storyid));
            }else{
                //触发剧情未领过奖励
                $storyid = $story['storyid1'][rand(0,$key)];
                $this->info[$type][$id]['prob'] = 0;
                Master::back_data($this->uid,$type,$type.'Chat',array('chatType'=>2,'stroyid'=>$storyid));
            }
        }else{
            //没触发剧情
            Master::back_data($this->uid,$type,$type.'Chat',array('chatType'=>0,'stroyid'=>0));
            $this->info[$type][$id]['prob'] = $prob + $this->prob;
        }
        $this->info[$type][$id]['time'] = Game::get_now();

        $this->info[$type][$id]['get'] = empty($this->info[$type][$id]['get'])?0:$this->info[$type][$id]['get'];
        $this->save();

    }


    /*
     * 改变领奖状态
     */
    public function modify_get($id,$type){
        if (empty($this->info[$type][$id])){
            Master::error('PARAMS_ERRRO');
        }
        $this->info[$type][$id]['get'] = 1;
        $this->save();
    }


    /*
     * 定义保存操作
     */
    public function save(){
        //基于活动类的存储
        $ActModel = Master::getAct($this->uid,$this->atype);
        $ActModel->setAct($this->atype,array(
            'id'=>$this->hid,
            'data' => $this->info,
        ));
    }
}






