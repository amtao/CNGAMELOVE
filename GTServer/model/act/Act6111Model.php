<?php
require_once "ActBaseModel.php";
/*
 * 书院学习记录
 */
class Act6111Model extends ActBaseModel
{
	public $atype = 6111;//活动编号
	
	public $comment = "珍宝馆";
    public $b_mol = "treasure";//返回信息 所在模块
    public $b_ctrl = "treatidy";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'curgate' => 1,
        'curlost' => 0,
        'count' => 0,
        'pics' => array(),
        'lastTime' => 0,
        'dayOver'=>0,
        'buyCount' => 0,
    );

    /*
    * 构造输出结构体
    * 修改保存结构体
    */
    public function make_out()
    {
        $this->flush();
        $this->outf = $this->info;
    }

    private function genPics($gate){
        $tidy = Game::getcfg_info("treaTidy", $gate);
        $c = $tidy['row'] * $tidy['column'] / 2;
        $pics = [];
        for ($i = 0; $i < $c; $i++){
            $pics[] = $i+1;
            $pics[] = $i+1;
        }
        shuffle($pics);
        return $pics;
    }

    public function trun($index1, $index2){
        $info = $this->info;
        $tidys = Game::getcfg("treaTidy");
        $tidy = $tidys[$info['curgate']];
        $pics = $info['pics'];
        $act6110Model = Master::getAct6110($this->uid);
        if ($tidy['open'] > $act6110Model->info['score']){
            Master::error(TREASURE_SCORE_LIMIT);
        }

        if ($index1 == $index2 || $pics[$index2] == 0 || $pics[$index1] == 0){
            return;
        }
        $buyCount = empty($info['buyCount'])?0:$info['buyCount'];
        if ($info['curlost'] - $buyCount > $tidy['chance']){
            Master::error(TREASURE_GATE_LOST);
        }

        if ($pics[$index1] == $pics[$index2] ){
            $pics[$index1] = $pics[$index2] = 0;
        }
        else {
            $info['curlost'] += 1;
        }

        $isover = true;
        for ($i = 0; $i < count($pics); $i++){
            if ($pics[$i] != 0){
                $isover = false;
                break;
            }
        }

        if (!$isover && $pics[$index1] == $pics[$index2]){
            $id = rand(2, 4);
            $count = $this->get_onetime_Num($id);
            $count = Game::getCfg_formula()->linklink_right($count);
            Master::add_item($this->uid, 1, $id, $count);
        }

        if ($isover){
            $info['curgate'] += 1;
            $info['curlost'] = 0;
            $info['buyCount'] = 0;
            $info['dayOver'] += 1;
            if ($info['lastTime'] < Game::day_0()){
                $info['dayOver'] = 1;
            }

            $Redis6111Model = Master::getRedis6111();
            $Redis6111Model->zAdd($this->uid, $info['dayOver']);
            if (empty($tidys[$info['curgate']])){
                $pics = array();
            }
            else {
                $pics = $this->genPics($info['curgate']);
            }
            $this->genRwd($tidy);
        }

        $info['pics'] = $pics;
        $this->info = $info;
        $this->save();

        if ($isover){
            $this->gateOver();
        }
    }

    private function gateOver(){

        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(58,1);

        //活动消耗 - 限时奖励-珍宝阁累计整理关卡次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6170',1);

        //御花园
        // $Act6190Model = Master::getAct6190($this->uid);
        // $Act6190Model->addType(9, 1);

        //舞狮大会 - 通过整理珍宝关卡
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(12,1);

        //咸鱼日志
        Common::loadModel('XianYuLogModel');
        $UserModel = Master::getUser($this->uid);
        if(empty($buyCount)){
            XianYuLogModel::treasure($UserModel->info['platform'],$this->uid, $this->info['curgate'],0,1);
        }else{
            XianYuLogModel::treasure($UserModel->info['platform'],$this->uid, $this->info['curgate'],1,1);
        }
    }

    private function genRwd($tidy){
        //发放奖励
        Master::add_rwd_singe($this->uid, $tidy['rwd']);
        Master::add_rwd_singe($this->uid, $tidy['rwd_2']);
    }

    public function flush($add = 0){
        $info = $this->info;
        if ($info['lastTime'] < Game::day_0()){
            $info['lastTime'] = Game::get_now();
            $info["curgate"] = 1;
            $info['curlost'] = 0;
            $info['buyCount'] = 0;
            $info['dayOver'] = $add == 0?0:$info['dayOver'];
            $info['count'] = $add == 0?0:$info['count']+1;
            $info['pics'] = $this->genPics($info["curgate"]);
            $this->info = $info;
            $this->save();
        }
    }

    public function addCount(){
        if ($this->info['buyCount'] > Game::getcfg_param("gongdou_shangbang_id")){
            Master::error(TREASURE_BUY_COUNT_LIMIT);
        }
        $cost = Game::getCfg_formula()->tidy_chance_price($this->info['buyCount']);
        if ($cost > 0) {
            Master::sub_item($this->uid, KIND_ITEM, 1, $cost);
        }
        $this->info['buyCount'] = empty($this->info['buyCount'])?1:$this->info['buyCount']+1;
        $this->save();

    }

    public function reset(){
        if ($this->info['lastTime'] < Game::day_0()){
            $this->flush();
            return;
        }

        if ($this->info['curlost'] == 0 && $this->info["curgate"] == 1)return;
        $cost = Game::getCfg_formula()->linklink_times($this->info['count']);
        if ($cost > 0) {
            Master::sub_item($this->uid, KIND_ITEM, 1, $cost);
        }
        $this->info['lastTime'] = 0;
        $this->flush(1);


        //咸鱼日志
        Common::loadModel('XianYuLogModel');
        $UserModel = Master::getUser($this->uid);
        XianYuLogModel::treasure($UserModel->info['platform'],$this->uid,$this->info["curgate"],1,3);

        //御花园
        // $Act6190Model = Master::getAct6190($this->uid);
        // $Act6190Model->addType(18, 1);
    }

    public function win(){
        $info = $this->info;
        $curgate = $this->info['curgate'];
        $tidys = Game::getcfg("treaTidy");
        $tidy = $tidys[$info['curgate']];
        $act6110Model = Master::getAct6110($this->uid);
        if ($tidy['open'] > $act6110Model->info['score']){
            Master::error(TREASURE_SCORE_LIMIT);
        }

        if ($tidy['pass'] > 0){
            Master::sub_item($this->uid, KIND_ITEM, 1, $tidy['pass']);
        }
        $info['curgate'] += 1;
        $info['curlost'] = 0;
        $info['buyCount'] = 0;
        $info['dayOver'] += 1;
        if ($info['lastTime'] < Game::day_0()){
            $info['dayOver'] = 1;
        }

        $Redis6111Model = Master::getRedis6111();
        $Redis6111Model->zAdd($this->uid, $info['dayOver']);

        if (empty($tidys[$info['curgate']])){
            $info['pics'] = array();
        }
        else {
            $info['pics'] = $this->genPics($info['curgate']);
        }
        $this->genRwd($tidy);
        $this->info = $info;
        $this->save();

        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(58,1);

        //活动消耗 - 限时奖励-珍宝阁累计整理关卡次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6170',1);

        //御花园
        // $Act6190Model = Master::getAct6190($this->uid);
        // $Act6190Model->addType(19, 1);
        // $Act6190Model->addType(9, 1);

        //咸鱼日志
        Common::loadModel('XianYuLogModel');
        $UserModel = Master::getUser($this->uid);
        XianYuLogModel::treasure($UserModel->info['platform'],$this->uid,$curgate,1,2);

    }

    /*
	 * 计算一次征收的资源数量
	 */
    private function get_onetime_Num($id){
        //获取阵法信息
        $team = Master::get_team($this->uid);
        $act6003Model = Master::getAct6003($this->uid);
        return $team['allep'][$id] + $act6003Model->getAddEp($id);
    }
}














