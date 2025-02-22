<?php
require_once "ActBaseModel.php";
/*
 * 御花园
 */
class Act6193Model extends ActBaseModel
{
	public $atype = 6193;//活动编号
	
	public $comment = "御花园";
    public $b_mol = "flower";//返回信息 所在模块
    public $b_ctrl = "cd";//返回信息 所在控制器
    public $label = "flower";//倒计时标记
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        'num' => 0,
        'time' => 0,
        'count' => 0,
    );

    public function make_out()
    {
        $this->outf = $this->updateCount();
        $this->_save();
    }

    public function updateNum(){
        $this->updateCount();
        $this->save();
    }

    public function updateCount(){
        $cd = Game::getcfg_param("flower_visit_time");
        $max = Game::getcfg_param("flower_count");
        $hf_num = Game::hf_num($this->info['time'], $cd, $this->info['num'], $max);
        if ($this->info['time'] < Game::day_0()){
            $this->info['count'] = 0;
        }
        $this->info['time'] = $hf_num['stime'];
        $this->info['num'] = $hf_num['num'];
        return array(
            'next' => $hf_num['next'],//下次绝对时间
            'num' => $hf_num['num'],//剩余次数
            'label' => $this->label,
            'isopen' => $this->info['count'],
            );
    }

    public function stealOut(){
        $this->updateCount();
        if ($this->info['num'] < 1){
            $cost = Game::getCfg_formula()->flower_cost($this->info['count']+1);
            Master::sub_item($this->uid, KIND_ITEM, 4, $cost);
            $this->info['count'] += 1;
        }
        else {
            $this->info['num'] -= 1;
        }

        $this->save();
    }


}














