<?php
/**
 * Created by PhpStorm.
 * User: CP
 * Date: 2017/11/28
 * Time: 11:42
 */
//国子监
class GuozijianMod extends Base
{
    public function __construct($uid)
    {
        parent::__construct($uid);
        $UserModel = Master::getUser($this->uid);
        $flag = Game::is_limit_level('gzj', $this->uid, $UserModel->info['level']);
        if ($flag == 2 && $UserModel->info['level'] < 11) {
            //默认限制从4开启
            Master::error(GZJ_NO_OPEN);
        }
    }

    public function gzj(){
        //位子数量
        $Act75Model = Master::getAct75($this->uid);
        $Act75Model->back_data();
        //获取个人座位信息
        $Act76Model = Master::getAct76($this->uid);
        $Act76Model->back_data();
        //已打工列表
        $Act78Model = Master::getAct78($this->uid);
        $Act78Model->back_data();
        //送礼列表
        $Act79Model = Master::getAct79($this->uid);
        $Act79Model->back_data();
    }

    /**
     * 国子监-学习位置购买
     */
    public function addDesk(){
        $Act75Model = Master::getAct75($this->uid);
        $Act75Model->add_desk();
    }

    /**
     * 国子监-开始学习
     * @param $params
     */
    public function startStudy($params){
        $wid = Game::intval($params,'wid');
        $sid = Game::intval($params,'sid');
        $Act76Model = Master::getAct76($this->uid);
        $Act76Model->start_work($wid,$sid);
    }

    /**
     * 行贿
     * @param $params
     */
    public function bribery($params){
        $sid = Game::intval($params,'sid');
        $level = Game::intval($params,'level');//档次
        $Act77Model = Master::getAct77($this->uid);
        $Act77Model->bribery($sid,$level);
        $Act76Model = Master::getAct76($this->uid);
        $Act76Model->back_data();
    }

    /**
     * 国子监-毕业
     * @param $params
     */
    public function overWork($params){
        $wid = Game::intval($params,'wid');
        $Act76Model = Master::getAct76($this->uid);
        $Act76Model->over_work($wid);
    }

    /**
     * 国子监-一起毕业
     */
    public function alloverWork(){
        $Act76Model = Master::getAct76($this->uid);
        $Act76Model->allover_work();
    }

    /**
     * 获取每日奖励(单人)
     */
    public function getdayreward($params){
        $sid = Game::intval($params,'sid');
        $Act77Model = Master::getAct77($this->uid);
        $Act77Model->get_day_reward($sid);
        $Act76Model = Master::getAct76($this->uid);
        $Act76Model->back_data();
    }

    /**
     * 国子监- 一键领取每日奖励
     */
    public function alldayreward(){
        $Act77Model = Master::getAct77($this->uid);
        $Act77Model->all_get_day_reward();
        $Act76Model = Master::getAct76($this->uid);
        $Act76Model->back_data();
    }
}