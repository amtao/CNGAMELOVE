<?php
require_once "ActBaseModel.php";
/*
 * 御花园
 */
class Act6192Model extends ActBaseModel
{
	public $atype = 6192;//活动编号
	
	public $comment = "御花园";
    public $b_mol = "flower";//返回信息 所在模块
    public $b_ctrl = "level";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'lv' => 1,
        'exp' => 0,
        'chenlu' => 0,
        'gx' => 0,
    );

    /*
	 * 构造输出结构体
	 */
    public function make_out($flag = false){
        //默认输出直接等于内部存储数据
        if ($flag){
            $data = $this->info;
            $Act6190Model = Master::getAct6190($this->uid);
            $data['isNewChenlu'] = $Act6190Model->isNews();
            $Act6191Model = Master::getAct6191($this->uid);
            $data['isNewFlower'] = $Act6191Model->isNews();
            $this->outf = $data;
        }
        else {
            $this->outf = $this->info;
        }
    }

    public function addExp($exp){
        $lastLv = $this->info['lv'];
//        $lvSys = Game::getcfg_info("flowerLv", $this->info['lv']);
        $lvs = Game::getcfg("flowerLv");
//        if ($lvSys['chance'] > rand(0, 10000)){
//            $exp = $exp * 2;
//        }
        $this->info['exp'] += $exp;
        $lvMax = 1;
        $flag = false;
        foreach ($lvs as $lSys){
            $lvMax = $lSys['lv'] > $lvMax?$lSys['lv']:$lvMax;
            if ($lSys['exp'] > $this->info['exp']){
                $this->info['lv'] = $lSys['lv'];
                $flag = true;
                break;
            }
        }
        if (!$flag){
            $this->info['lv'] = $lvMax;
        }
        if ($lastLv != $this->info['lv']){
            $Redis6192Model = Master::getRedis6192();
            $Redis6192Model->zAdd($this->uid,$this->info['lv']);
        }
        $this->save();
    }

    public function addChenlu($value){
        $this->info['chenlu'] += $value;
        Game::cmd_flow(6192, 10001, $value, $this->info['chenlu']);
        if ($this->info['chenlu'] < 0){
            Master::error(FLOWER_CHENLU_LIMIT);
        }
        $this->save();
        if ($value<0){
            //活动消耗 - 御花园偷取晨露
            $HuodongModel = Master::getHuodong($this->uid);
            $HuodongModel->chongbang_huodong('huodong6216',$this->uid,abs($value));
        }
    }

    public function addGX($value){
        $this->info['gx'] += $value;
        $Redis6190Model = Master::getRedis6190();
        $Redis6190Model->zAdd($this->uid, $this->info['gx']);
        $this->save();
    }

}














