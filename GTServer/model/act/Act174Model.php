<?php
require_once "ActBaseModel.php";
/*
 * 成就
 */
class Act174Model extends ActBaseModel
{
	public $atype = 174;//活动编号
	
	public $comment = "分享成就";
	public $b_mol = "share";//返回信息 所在模块
	public $b_ctrl = "achievement";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
	    'shareNum' => 0, // 分享次数
        'rwds' => array(), // 已领奖id
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$this->outf = $this->info;
	}

	public function rwd($id){
        $shareAchievementRwd = Game::getcfg_info('shareachievementrwd',$id);
	    if (in_array($id,$this->info['rwds'])) {
	        Master::error(REWARD_IS_GET);
        }

        foreach ($shareAchievementRwd['rwd'] as $val) {
            Master::add_item($this->uid, $val['kind'], $val['id'], $val['count']);
        }

        $this->info['rwds'][] = $id;
        $this->save();
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->info);
	}

	public function addShareNum() {
        $shareAchievementRwd = Game::getcfg('shareachievementrwd');
        $len = count($shareAchievementRwd);
        $maxNum = $shareAchievementRwd[$len]['need'];

        if ($this->info['shareNum'] >= $maxNum) {
            Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->info);
            return;
        }

        $this->info['shareNum']++;
        $this->save();
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->info);
    }


    public function back_data_u() {
        parent::back_data_u();
    }
}
















