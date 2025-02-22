<?php
require_once "ActBaseModel.php";

class Act175Model extends ActBaseModel
{
	public $atype = 175;//活动编号
	
	public $comment = "关注/收藏/分享奖励";
	public $b_mol = "behavior";//返回信息 所在模块
	public $b_ctrl = "list";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(

	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$this->outf = $this->info;
	}

    public function rwd($id) {
        $behaviorRwd = Game::getcfg_info("behaviorrwd",$id);
        if (!isset($this->info[$id])) {
            $this->info[$id] = array(
                'id' => $id,
                'status' => 0, // 状态
                'rwdStatus' => 0, // 领奖状态
            );
        }

        if (!$this->info[$id]['status']) {
            Master::error(IS_NOT_COMPLETE);
        }

        if ($this->info[$id]['rwdStatus']) {
            Master::error(REWARD_IS_GET);
        }

        foreach ($behaviorRwd as $val) {
            Master::add_item($this->uid, $val['kind'], $val['id'], $val['count']);
        }

        $this->info[$id]['rwdStatus'] = 1;
        $this->save();

        Master::back_data($this->uid,$this->b_mol,'info',$this->info);
    }

	public function complete($id) {
        Game::getcfg_info("behaviorrwd",$id);
        if (!isset($this->info[$id])) {
            $this->info[$id] = array(
                'id' => $id,
                'status' => 0, // 状态
                'rwdStatus' => 0, // 领奖状态
            );
        }

        if ($this->info[$id]['status']) {
            Master::back_data($this->uid,$this->b_mol,'info',$this->info[$id]);
            return;
        }

        $this->info[$id]['status'] = 1;
        $this->save();
        Master::back_data($this->uid,$this->b_mol,'info',$this->info);
    }


    public function back_data_u() {
        parent::back_data_u();
    }
}
















