<?php
/*
 * 世界BOSS 葛二蛋
 */
require_once "SevBaseModel.php";
class Sev6190Model extends SevBaseModel
{
	public $comment = "世界树";
	public $act = 6190;//活动标签
	public $_init = array(//初始化数据
        'level' => 1,
        'all' => 0,
	);
	//下次开战时间日期  时间点数 使用配置
    public function __construct(){
        parent::__construct();
        $this->mk_outf();
    }
	
	public $outof = NULL;

	public function hit($count){
        $sys = Game::getcfg_info('worldtree', $this->info['level']);
        $per = $sys['point'];
        $c = $per * $count;

        if (!empty($sys['dew']) && $count + $this->info['all'] >= $sys['dew']){
            $this->info['level'] += 1;
            $sys1 = Game::getcfg_info('worldtree', $this->info['level']);
            $per1 = $sys1['point'];
            $r = $count + $this->info['all'] - $sys['dew'];
            $this->info['all'] = $r;
            $c += ($per1 - $per) * $r;
        }
        else {
            $this->info['all'] = empty($sys['dew'])?0:$this->info['all']+$count;
        }
        $this->save();
        return $c;
    }
	
}
