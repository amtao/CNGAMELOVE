<?php
require_once "ActBaseModel.php";
/*
 * 祈福
 */
class Act6154Model extends ActBaseModel
{
	public $atype = 6154;//活动编号
	
	public $comment = "祈福";
    public $b_mol = "user";//返回信息 所在模块
    public $b_ctrl = "qifu";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'free' => 0,
        'buy' => 0,
        'lastTime' => 0,
        'ten' => 0, //免费10倍
        'isten' => 0,
    );

    /*
	 * 构造输出结构体
	 */
    public function make_out(){
        //构造输出
        if ($this->info["lastTime"] < Game::day_0()){
            $this->updateFree();
        }
        $this->outf = $this->info;
    }

    private function updateFree(){
        $info = $this->info;
        $info['free'] = 0;
        $info['buy'] = 0;
        $info['lastTime'] = Game::get_now();
        $this->info = $info;
    }

    public function qifu($type){
        $info = $this->info;
        if ($info['lastTime'] < Game::day_0()){
            $this->updateFree();
        }

        $UserModel = Master::getUser($this->uid);
        $level = $UserModel->info['level'];
        $guan_cfg = Game::getcfg_info('guan', $level);//官阶配置

        $recordCost = 0;
        if ($info['free'] >= $guan_cfg['pray']){
            $buy = $info['buy'] + 1;
            $cost = $buy*(1 + floor($buy / 10))*2;
            Master::sub_item($this->uid,KIND_ITEM,1, $cost);
            $recordCost = $cost;

            $info['buy'] = $info['buy'] + 1;
            //日常任务
            // $Act35Model = Master::getAct35($this->uid);
            // $Act35Model->do_act(13,1);
            $Act39Model = Master::getAct39($this->uid);
            $Act39Model->task_add(156,1);

            //御花园
            // $Act6190Model = Master::getAct6190($this->uid);
            // $Act6190Model->addType(11, 1);

            //咸鱼日志
            Common::loadModel('XianYuLogModel');
            XianYuLogModel::qifu($UserModel->info['platform'], $this->uid,$type,1);

            Game::flow_php_record($this->uid, 6, $type, 1, '', $cost);
        }
        else {
            $info['free'] = $info['free'] + 1;
            Common::loadModel('XianYuLogModel');
            XianYuLogModel::qifu($UserModel->info['platform'], $this->uid,$type,0);
        }

        $isTen = false;
        if ($info['ten'] == Game::getcfg_param("qifu_ten_count")){
            $info['ten'] = 0;
            $isTen = true;
        }
        else {
            $info['ten'] = $info['ten'] + 1;
        }

        $isProTen = false;
        $info['isten'] = 0;
        if (rand(1,10000) < Game::getcfg_param("qifu_ten_pro")){
            $isProTen = true;
            $info['isten'] = 1;
        }
        if ($type != 2 && $type != 3 && $type != 4)return;

        //计算资源
        $b = Game::getcfg_param("qifu_ten");
        $epValue = $this->get_onetime_Num($type);
        $resourceNum = ceil($this->get_onetime_Num($type)*0.4 + 6000);
        if($epValue > 50000){
            $resourceNum = ceil(26000 + pow($epValue,0.8));
            $resourceNum = floor($resourceNum/100)*100;
        }
        $zy_one =  $resourceNum * Game::getcfg_param("qifu_base_prop") / 10000;
        $beishu = $isTen?$b:0;
        $beishu = $isProTen?$beishu + $b:$beishu;
        $beishu = $beishu == 0?1:$beishu;
        $zy = $zy_one * $beishu;

        Master::add_item($this->uid,1, $type, ceil($zy));
        $this->info = $info;
        $this->save();


        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(57,1);
        $Act39Model->task_refresh(57);

        //活动消耗 - 限时祈福次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6171',1);
    }

    public function qifuTen($type){
        $info = $this->info;
        if ($info['lastTime'] < Game::day_0()){
            $this->updateFree();
        }

        if ($type != 2 && $type != 3 && $type != 4)return;

        $UserModel = Master::getUser($this->uid);
        $level = $UserModel->info['level'];
        $guan_cfg = Game::getcfg_info('guan', $level);//官阶配置

        $allCost = 0;
        $allZy = 0;
        $taskAdd = 0;
        for ($i=0; $i < 10; $i++) {

            if ($info['free'] >= $guan_cfg['pray']){

                $taskAdd++;
                $buy = $info['buy'] + 1;
                $cost = $buy*(1 + floor($buy / 10))*2;
                $allCost += $cost;
                $info['buy'] = $info['buy'] + 1;

                //咸鱼日志
                Common::loadModel('XianYuLogModel');
                XianYuLogModel::qifu($UserModel->info['platform'], $this->uid, $type, 1);

                Game::flow_php_record($this->uid, 6, $type, 1, '', $cost);
            }
            else {
                $info['free'] = $info['free'] + 1;
                Common::loadModel('XianYuLogModel');
                XianYuLogModel::qifu($UserModel->info['platform'], $this->uid,$type,0);
            }

            $isTen = false;
            if ($info['ten'] == Game::getcfg_param("qifu_ten_count")){
                $info['ten'] = 0;
                $isTen = true;
            }
            else {
                $info['ten'] = $info['ten'] + 1;
            }

            $isProTen = false;
            $info['isten'] = 0;
            if (rand(1,10000) < Game::getcfg_param("qifu_ten_pro")){
                $isProTen = true;
                $info['isten'] = 1;
            }

            //计算资源
            $b = Game::getcfg_param("qifu_ten");
            $epValue = $this->get_onetime_Num($type);
            $resourceNum = ceil($this->get_onetime_Num($type)*0.4 + 6000);
            if($epValue > 50000){
                $resourceNum = ceil(26000 + pow($epValue,0.8));
                $resourceNum = floor($resourceNum/100)*100;
            }
            $zy_one = $resourceNum * Game::getcfg_param("qifu_base_prop") / 10000;
            $beishu = $isTen?$b:0;
            $beishu = $isProTen?$beishu + $b:$beishu;
            $beishu = $beishu == 0?1:$beishu;
            $zy = $zy_one * $beishu;

            $allZy += ceil($zy);
        }

        if ($allCost > 0) {
            Master::sub_item($this->uid,KIND_ITEM,1, $allCost);
        }

        if ($taskAdd > 0) {

            //日常任务
            $Act35Model = Master::getAct35($this->uid);
            $Act35Model->do_act(13,$taskAdd);

            //御花园
            // $Act6190Model = Master::getAct6190($this->uid);
            // $Act6190Model->addType(11, $taskAdd);
        }

        Master::add_item($this->uid, 1, $type, $allZy);
        $this->info = $info;
        $this->save();
        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(57,10);
        $Act39Model->task_refresh(57);

        //活动消耗 - 限时祈福次数
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->xianshi_huodong('huodong6171',10);
    }

    public function qifuCost(){
        $info = $this->info;
        if ($info['lastTime'] < Game::day_0()){
            $this->updateFree();
        }

        $UserModel = Master::getUser($this->uid);
        $level = $UserModel->info['level'];
        $guan_cfg = Game::getcfg_info('guan', $level);//官阶配置

        $allCost = 0;
        for ($i=0; $i < 10; $i++) {

            if ($info['free'] >= $guan_cfg['pray']){

                $buy = $info['buy'] + 1;
                $cost = $buy*(1 + floor($buy / 10))*2;
                $allCost += $cost;
                $info['buy'] = $info['buy'] + 1;
            }
            else {
                $info['free'] = $info['free'] + 1;
            }
        }

        return $allCost;
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

