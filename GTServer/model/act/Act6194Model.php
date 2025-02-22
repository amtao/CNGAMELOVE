<?php
require_once "ActBaseModel.php";
/*
 * 御花园
 */
class Act6194Model extends ActBaseModel
{
	public $atype = 6194;//活动编号
	
	public $comment = "御花园";
    public $b_mol = "flower";//返回信息 所在模块
    public $b_ctrl = "logs";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'index' => 0,
        'logs'=>array(/*
         * uid
         * steal -1 为收取
         * time
         * fname
         * */),
    );

    /*
	 * 构造输出结构体
	 */
    public function make_out($isCheck = false){
        if (!$isCheck)return;
        //默认输出直接等于内部存储数据
        $data = array();
        if ($this->info['index'] == 0){
            $data = $this->info['logs'];
            $this->info['index'] = count($this->info['logs']);
            $this->_save();
        }
        else {
            $l = count($this->info['logs']);
            if ($l > $this->info['index']){
                for($i = $this->info['index']; $i < $l; $i++){
                    $data[] = $this->info['logs'][$i];
                }
                $this->info['index'] = $l;
                $this->_save();
            }
        }
        $arr = [];
        $names = array();
        foreach ($data as $v){
//            if (empty($v['fname'])){
            if (!empty($v['uid'])){
                if (empty($names[$v['uid']])){
                    $fUserModel = Master::getUser($v['uid']);
                    $fUser_info = $fUserModel->info;
                    $names[$v['uid']] = Game::filter_char($fUser_info['name']);
                }
                $v['fname'] = $names[$v['uid']];
            }
            $arr[] = $v;
        }
        $this->outf = $arr;
    }

    /*
	 * 返回活动信息
	 */
    public function back_data(){
        $this->make_out(true);
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
    }

    public function initIndex(){
        $this->info['index'] = 0;
        $this->_save();
    }

    public function addLog($uid, $steal){
        if (count($this->info['logs']) > 30){
            array_shift($this->info['logs']);
            $this->info['index'] -= 1;
        }
        $this->info['logs'][] = array(
            'uid'=>$uid,
            'steal'=>$steal,
            'time'=>Game::get_now(),
        );
        $this->_save();
    }
}














