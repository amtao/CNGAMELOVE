<?php
require_once "ActBaseModel.php";
/*
 * 成就
 */
class Act737Model extends ActBaseModel
{
	public $atype = 737;//活动编号
	
	public $comment = "邀约成就任务";
	public $b_mol = "invite";//返回信息 所在模块
	public $b_ctrl = "achieve";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
        'taskInfo' => array(),
    );
    
    /**
     * taskType
     *   1-钓到所有种类的鱼
     *   2-收集到所有种类的珍馐  
     *   3-钓到品质为4的鱼X次
     *   4-收集到品质为4的珍馐X次
     *   5-收集鱼骨头X次
     *   6-收集XX城市珍馐特产
     *   7-收集指定类型（fishtype）的鱼类
     */
    
    //单个参数的任务计数
    public function setTask($type){
        $collectAchieveCfg = Game::getcfg("collection_achieve");
        foreach($collectAchieveCfg as $v){
            if($v['type'] == $type){
                if(empty($this->info['taskInfo'][$v['id']])){
                    $this->info['taskInfo'][$v['id']] = array("count" => 0,"isPick" => 0);
                }
                $this->info['taskInfo'][$v['id']]["count"]++;
            }
        }
        $this->save();
    }

    /**
     * 两个参数的任务计数
     * $paramX 为每个类型的独立判断
     */
    public function setTwoParamTask($type,$paramX){
        $collectAchieveCfg = Game::getcfg("collection_achieve");
        foreach($collectAchieveCfg as $v){
            if($v['type'] == $type){
                if(count($v['need']) <= 1){
                    continue;
                }
                if($v['need'][0] != $paramX){
                    continue;
                }
                if(empty($this->info['taskInfo'][$v['id']])){
                    $this->info['taskInfo'][$v['id']] = array("count" => 0,"isPick" => 0);
                }
                $this->info['taskInfo'][$v['id']]["count"]++;
            }
        }
        $this->save();
    }

    //领取任务奖励
	public function rwd($id){
        if(empty($this->info['taskInfo'][$id])){
            Master::error(INVITE_TASK_ERR);
        }
        if($this->info['taskInfo'][$id]['isPick'] == 1){
            Master::error(HAS_PICK_AWARD);
        }
        $collectAchieveCfg = Game::getcfg_info("collection_achieve",$id);
        $standardCount = $collectAchieveCfg['need'][0];
        
        if(count($collectAchieveCfg['need']) > 1){
            $standardCount = $collectAchieveCfg['need'][1];
        }
        if($this->info['taskInfo'][$id]['count'] < $standardCount){
            Master::error(INVITE_TASK_NOT_PICK);
        }
        Master::add_item3($collectAchieveCfg['rwd']);
        $this->info['taskInfo'][$id]['isPick'] = 1;
        $this->save();
    }
}