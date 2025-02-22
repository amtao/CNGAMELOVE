<?php
require_once "ActBaseModel.php";
/*
 * 成就
 */
class Act173Model extends ActBaseModel
{
	public $atype = 173;//活动编号
	
	public $comment = "每日分享";
	public $b_mol = "share";//返回信息 所在模块
	public $b_ctrl = "daily";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
	    'shareNum' => 0, // 每日分享次数
        'coolingTime' => 0, // 冷却时间
        'rwdNum' => 0, // 领奖次数
        'rwds' => array(), // 已领奖id
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$this->outf = $this->info;
	}

    public function rwd($id) {
        $shareDailyRwd = Game::getcfg('sharedailyrwd');
        $rwdNum = $this->info['rwdNum'];
        if ($rwdNum >= $this->info['shareNum'] || $id > $this->info['shareNum'] || in_array($id,$this->info['rwds'])) {
            Master::error(REWARD_IS_GET);
        }

        if (!isset($shareDailyRwd['reward'][$id])) {
            Master::error(CONFIG_IS_ERROR);
        }

        foreach ($shareDailyRwd['reward'][$id] as $val) {
            Master::add_item($this->uid, $val['kind'], $val['id'], $val['count']);
        }

        $this->info['rwdNum']++;
        $this->info['rwds'][] = $id;
        $this->save();
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->info);
    }


	public function addShareNum() {
        $reqTime = Game::get_now();

        if ($reqTime < $this->info['coolingTime']) {
            Master::error(SHARE_IS_COOLING);
        }

        $shareDailyRwd = Game::getcfg('sharedailyrwd');

        $this->info['coolingTime'] = $reqTime + $shareDailyRwd['coolingTime'];

        if ($this->info['shareNum'] < count($shareDailyRwd['reward'])) {
            $this->info['shareNum']++;
        }

        $this->save();

        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->info);
    }


    public function back_data_u() {
        parent::back_data_u();
    }
}
















