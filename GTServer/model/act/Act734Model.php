<?php 
require_once "ActBaseModel.php";
/*
 * 伙伴邀约--成就
 */

class Act734Model extends ActBaseModel{
    
    public $atype = 734;//活动编号

	public $comment = "伙伴风物志-收集奖励";
	public $b_mol = "hero";//返回信息 所在模块
    public $b_ctrl = "collect";//返回信息 所在控制器
    
    public $_init = array(
        'collectAward' => array(),
    );

	/**
	 * 新的收集物插入
	 */
    public function setCollects($itemid,$num){

        if(empty($this->info['collectAward'][$itemid])){
            $this->info['collectAward'][$itemid] = array('num' => 0,'rwd'=>0);
        }  
        $this->info['collectAward'][$itemid]['num'] = $num;
        $this->save();
    }

	/**
	 *
	 * 领取收集奖励 
	 * 逐条领取
	 */
    public function rwd($itemid){
        if (empty($this->info['collectAward'][$itemid])){
			Master::error('collect_err_'.$itemid);
		}
		
		//当前领奖档次
		$this->info['collectAward'][$itemid]['rwd'];
		//领奖目标档次
		$rwdid = $this->info['collectAward'][$itemid]['rwd'] + 1;
		
		//配置
		$collectRwdCfg = Game::getcfg_info('collection_rwd',$itemid);
		
		//配置溢出 已经领完?
		if (!isset($collectRwdCfg[$rwdid])){
			Master::error(ACT_36_LINGWAN.$rwdid);
		}
		
		//判断是否达到了下一档次奖励
		if ($collectRwdCfg[$rwdid]['need'] > $this->info['collectAward'][$itemid]['num']){
//			Master::error(ACHIEVEMENT_UN_TO_ACHIEVE);
            Master::error();
		}
		
		//发放收集达成奖励
		foreach ($collectRwdCfg[$rwdid]['rwd'] as $v){
			Master::add_item2($v);
		}
		
		//更新领奖记录
		$this->info['collectAward'][$itemid]['rwd'] = $rwdid;
		$this->save();
		$Act733Model = Master::getAct733($this->uid);
        $Act733Model->back_data();
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
