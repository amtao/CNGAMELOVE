<?php
require_once "ActBaseModel.php";
/*
 *  历练书信
 */
class Act6134Model extends ActBaseModel
{
	public $atype = 6134;//活动编号

	public $comment = "历练信件";
	public $b_mol = "feige";//返回信息 所在模块
	public $b_ctrl = "sonFeige";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(

	);

    /*
	 * 储存书信数据
     * @param $sid      徒弟id
     * @param $mail     书信id
     * @param $mail     当前城市id
     * @param $time     时间标识
	 */

    public function back_data()
    {
        //多次调用 数据会累加  所以得先用空数组清空一次 再返回数据
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,array());
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
    }

    public function mailDelivery($sid,$mail,$city,$time){
        $email_list = Game::getcfg("emailItem");
        $group = null;
        if (stripos($group, "e") != 0) {
            Master::error(STORY_DATA_EMAIL_FIND1);
        }
        foreach ($email_list as $v){
            if ($v['id'] == $mail){
                $group = Game::getcfg_info("emailGroup", $v['group']);
                break;
            }
        }
        if ($group['fromtype'] != 3 && $group['type'] != 7){
            Master::error(EMAIL_TYPE_ERROR);
        }
        $this->info[] = array('sid'=> $sid,'id'=> $mail,'city'=> $city,'select' => array(),'time'=> $time);
        $this->save();

    }

    //判断书信是否已存在
    public function isExist($sid,$time){

        if (!empty($this->info)){

            foreach ($this->info as $k=>$v){

                if($v['sid'] == $sid && $v['time'] == $time){

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 领取奖励 并改变对应城市信件状态
     * @param $sid        //徒弟id
     */
    public function addReward($sid,$id,$time){
        $email_list = Game::getcfg("emailItem");
        $group = null;
        if (stripos($group, "e") != 0) {
            Master::error(STORY_DATA_EMAIL_FIND2);
        }
        foreach ($email_list as $v){
            if ($v['award1'] == $id || $v['award2'] == $id){
                $group = Game::getcfg_info("emailGroup", $v['group']);
                break;
            }
        }
        if (empty($group)){
            Master::error(STORY_DATA_EMAIL_FIND3);
        }
        if ($group['fromtype']!=3 && $group['type']!=7){
            Master::error(STORY_DATA_TYPE_ERROR);
        }

        $index = 0;
        foreach ($this->info as $k=>$v){
            if ($v['sid'] == $sid && $v['time'] == $time){
                $index = $k;
            }
        }

        if ($index == 0 && !empty($this->info[$index]['select'])){
            Master::error(NOT_DATA_EMAIL_FIND);
        }

        if (empty($this->info[$index])){
            Master::error(NOT_DATA_EMAIL_FIND);
        }

        if (!empty($this->info[$index]['select']) || count($this->info[$index]['select']) > 0 || in_array($id, $this->info[$index]['select'])){
            Master::error(STORY_DATA_EMAIL_SELECT);
        }

        $this->info[$index]['select'][] = $id;
        $this->save();

    }

    /**
     * 一键领取奖励 并改变对应城市信件状态
     * @param $sid        //徒弟id
     */
    public function yjAddReward(){
        if (empty($this->info)){
            Master::error(SON_LI_LIAN_NO_MESSAGE);
        }
        $email_list = Game::getcfg("emailItem");
        $rwds = array();
        foreach ($this->info as $k => $v){
            if (empty($v['select'])){
                $rand = 'award'.rand(1,2);
                $xuangxiang = $email_list[$v['id']][$rand];
                $this->info[$k]['select'][] = $xuangxiang;
                $rwds[] = $xuangxiang;

                //改变已读书信的状态
                $Act6133Model = Master::getAct6133($this->uid);
                $Act6133Model->clearMsgId($v['sid'],$v['time']);
            }
        }
        if (empty($rwds)){
            Master::error(SON_LI_LIAN_NO_MESSAGE);
        }
        $this->save();
        return $rwds;
    }

    //返回所有书信的数量
    public function mailTotal(){
        if (count($this->info)>200){
            return false;
        }else{
            return true;
        }
    }

    //删除已读书信
    public function clearReadMail(){
        if (!empty($this->info)){
            $mails = $this->info;
            foreach ($mails as $k => $v){
                if (!empty($v['select'])){
                    unset($mails[$k]);
                }
            }
            $this->info = $mails;
            $this->save();
        }
    }

}
