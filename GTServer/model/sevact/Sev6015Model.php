<?php
/*
 * 世界BOSS 葛二蛋
 */
require_once "SevBaseModel.php";
class Sev6015Model extends SevBaseModel
{
	public $comment = "抢汤圆";
	public $act = 6015;//活动标签
	public $_init = array(//初始化数据
        'lastTime' => 0,
        'allhp' => array(),
        'hits' => array(),
	);
	//下次开战时间日期  时间点数 使用配置
	
	public $outof = NULL;

	//
	//今天 未开战 ? 战斗中 ? 已击杀?
	//对比日期ID 如果不是今天
	//每天的超时操作?
    public function __construct($hid = 1,$cid = 1,$serverID = null)
    {
		parent::__construct($hid, $cid, $serverID);
		$this->mk_outf();
	}

	/*
	 * 构造业务输出数据
	 */
	public function updateValue(){
        Common::loadModel('HoutaiModel');
        $hd_cfg = HoutaiModel::get_huodong_info('huodong_6015');
        if (empty($hd_cfg))return;
        if (empty($this->info['lastTime'] ) || $this->info['lastTime'] < Game::day_0()){
            $this->info['allhp'] = array();
            $this->info['hits'] = array();
            $this->info['lastTime'] = Game::get_now();
            foreach ($hd_cfg['times'] as $v){
                $this->info['allhp'][$v['need']] = $v['all'];
                $this->info['hits'][$v['need']] = 0;
            }
            $this->save();
        }
    }

	public function mk_outf(){
	    $this->updateValue();

        $this->outof = array();
        Common::loadModel('HoutaiModel');
        $hd_cfg = HoutaiModel::get_huodong_info('huodong_6015');
        if (empty($hd_cfg))return;

        $day_0 = Game::day_0();
        $time = Game::get_now();
        $curData = $hd_cfg['times'][0];
        foreach ($hd_cfg['times'] as $v){
            if ($day_0 + $v['need'] * 3600 <= $time){
                $curData = $v;
            }
        }

        $this->outof = array(
            'allhp' => $curData['all'],
            'hit' => $this->info['hits'][$curData['need']],
        );
	}

	public function hit($count){
        Common::loadModel('HoutaiModel');
        $hd_cfg = HoutaiModel::get_huodong_info('huodong_6015');
        if (empty($hd_cfg))return false;
        $this->updateValue();

        $day_0 = Game::day_0();
        $time = Game::get_now();
        $curData = $hd_cfg['times'][0];
        foreach ($hd_cfg['times'] as $v){
            if ($day_0 + $v['need'] * 3600 <= $time){
                $curData = $v;
            }
        }

        $id = $curData['need'];

        $last = empty($this->info['hits'][$id])?0:$this->info['hits'][$id];
        if ($last + $count >= $this->info['allhp'][$id]){
            $count = $this->info['allhp'][$id] - $last;
        }
        if ($count == 0)return 0;
        $this->info['hits'][$id] = $last + $count;

        $this->save();
        $this->mk_outf();
        return $count;
    }
	
	
}
