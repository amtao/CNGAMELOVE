<?php
//学院模块
class FlowerMod extends Base
{
	/*
	 *
	 */
	public function rwd($params){
        $id = Game::intval($params,'id');
		$Act6190Model = Master::getAct6190($this->uid);
        $Act6190Model->shou($id);
        $Act6194Model = Master::getAct6194($this->uid);
        $Act6194Model -> back_data();
	}

    public function yjRwd(){
        Master::vip_limit($this->uid,4,'LOOK_FOR_VIP_LEVEL_SHORT');
        $Act6190Model = Master::getAct6190($this->uid);
        $Act6190Model->shou(0,true);
        $Act6194Model = Master::getAct6194($this->uid);
        $Act6194Model -> back_data();
    }

	public function info(){
        //御花园
        $Act6190Model = Master::getAct6190($this->uid);
        $Act6190Model -> back_data(true);
        $Act6191Model = Master::getAct6191($this->uid);
        $Act6191Model -> back_data();
        $Act6192Model = Master::getAct6192($this->uid);
        $Act6192Model -> back_data();
        $Act6193Model = Master::getAct6193($this->uid);
        $Act6193Model -> back_data();
        $Act6194Model = Master::getAct6194($this->uid);
        $Act6194Model -> back_data();

        $sev6190Model = Master::getSev6190();
        Master::back_data($this->uid,'flower','worldtree',$sev6190Model->info);

        $Redis6192Model = Master::getRedis6192();
        $rank = $Redis6192Model->get_rank_id($this->uid);
        if (empty($rank)){
            $Redis6192Model->zAdd($this->uid,$Act6192Model->info['lv']);
        }
    }


    /*
     *
     */
    public function steal($params){
        $id = Game::intval($params,'id');
        $uid = Game::intval($params,'uid');
        $Act6190Model = Master::getAct6190($uid);
        $steal = $Act6190Model->steal($id, $this->uid);
        if ($steal == 0){
            Master::error(FLOWER_STEAL_SHOU);
        }else if ($steal == -1){
            Master::error_msg(FLOWER_STEAL_HELP);
        }
        $Act6194Model = Master::getAct6194($this->uid);
        $Act6194Model -> back_data();
        //限时-御花园偷取晨露
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6212',1);

        //活动消耗 - 御花园偷取晨露
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->chongbang_huodong('huodong6215',$this->uid,1);

        //舞狮大会 - 御花园偷取晨露
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(19,1);
    }

    /*
     *
     */
    public function plant($params){
        $id = Game::intval($params,'id');
        $pid = Game::intval($params,'uid');
        $Act6191Model = Master::getAct6191($this->uid);
        $Act6191Model->plant($id, $pid);
        $Act6194Model = Master::getAct6194($this->uid);
        $Act6194Model -> back_data();
        //限时-种植次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6213',1);

        //舞狮大会 - 御花园种植次数
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(21,1);
    }

    /*
     *
     */
    public function yjPlant($params){
        Master::vip_limit($this->uid,5,'LOOK_FOR_VIP_LEVEL_SHORT');
        $arr = Game::arrayval($params,'arr');
        if (empty($arr)){
            Master::error(PARAMS_ERROR);
        }
        $Act6191Model = Master::getAct6191($this->uid);
        $Act6191Model->yjplant($arr);
        $Act6194Model = Master::getAct6194($this->uid);
        $Act6194Model -> back_data();
        //限时-种植次数
        $num = count($arr);
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6213',$num);

        //舞狮大会 - 御花园种植次数
        $Act6224Model = Master::getAct6224($this->uid);
        $Act6224Model->task_add(21,$num);
    }

    /*
     *
     */
    public function plantRwd($params){
        $id = Game::intval($params,'id');
        $Act6191Model = Master::getAct6191($this->uid);
        $Act6191Model->shou($id);
        $Act6194Model = Master::getAct6194($this->uid);
        $Act6194Model -> back_data();
    }

    /*
     *
     */
    public function yjPlantRwd(){
        Master::vip_limit($this->uid,4,'LOOK_FOR_VIP_LEVEL_SHORT');
        $Act6191Model = Master::getAct6191($this->uid);
        $Act6191Model->shou(0,true);
        $Act6194Model = Master::getAct6194($this->uid);
        $Act6194Model -> back_data();
    }

    /*
     *
     */
    public function open($params){
        $id = Game::intval($params,'id');
        $Act6191Model = Master::getAct6191($this->uid);
        $Act6191Model->kai($id);
        $Act6194Model = Master::getAct6194($this->uid);
        $Act6194Model -> back_data();
    }

    /*
     *
     */
    public function rank(){
        $Redis6192Model = Master::getRedis6192();
        $Redis6192Model->back_data();
        $Redis6192Model->back_data_my($this->uid);//我的排名
        $Act6194Model = Master::getAct6194($this->uid);
        $Act6194Model -> back_data();
    }

    public function flush($params){
        $id = Game::intval($params,'id');
        if ($id == $this->uid)return;
        $Redis6192Model = Master::getRedis6192();
        $uid = $Redis6192Model->rand_f_uid($this->uid);
        if ($uid == 0){
            Master::error(FLOWER_STEAL_LIMIT);
        }
        $uid = $id == 0?$uid:$id;

        $Act6193Model = Master::getAct6193($this->uid);
        $Act6193Model -> stealOut();

        $Act6190Model = Master::getAct6190($uid);
        $ptime = empty($Act6190Model->info['protect']['ctime'])?0:$Act6190Model->info['protect']['ctime'];
        $data = array(
            'types' => $Act6190Model->info['cPoints'],
            'ptime' => Game::is_over($ptime)?0:$ptime,
            'fuser' => Master::fuidInfo($uid)
        );

        Master::back_data($this->uid, 'flower',"steal",$data);

        $Act6194Model = Master::getAct6194($this->uid);
        $Act6194Model -> back_data();
    }

    public function treeRank(){
        $Redis6190Model = Master::getRedis6190();
        $Redis6190Model->back_data();
        $Redis6190Model->back_data_my($this->uid);//我的排名
    }

    public function wordlTree($params){
        $id = Game::intval($params,'id');
        if (empty($id) || $id < 0)return;

        $sev6190Model = Master::getSev6190();
        $c = $sev6190Model->hit($id);

        Master::sub_item($this->uid, KIND_OTHER, 10001, $id);

        Master::add_item($this->uid, KIND_ITEM, 2, $c);
        Master::add_item($this->uid, KIND_ITEM, 3, $c);
        Master::add_item($this->uid, KIND_ITEM, 4, $c);

        if ($id >= 5000){
            $Sev6012Model = Master::getSev6012();
            $msg = "#worldtree#::".$id;
            $Sev6012Model->add_msg($this->uid, $msg, 3);
        }

        $Act6192Model = Master::getAct6192($this->uid);
        $Act6192Model -> addGX($id);

        Master::back_data($this->uid,'flower','worldtree',$sev6190Model->info);
    }

    /*
     * 晨露保护罩
     */
    public function protectCover($params){
        $id = Game::intval($params,'id');
        $type = Game::intval($params,'type');
        $Act6190Model = Master::getAct6190($this->uid);
        $Act6190Model->openProtect($id,$type);
    }
}
