<?php
require_once "ActBaseModel.php";
/*
 * 国子监-已打工列表
 */
class Act78Model extends ActBaseModel
{
    public $atype = 78;//活动编号

    public $comment = "国子监-已毕业列表";
    public $b_mol = "gzj";//返回信息 所在模块
    public $b_ctrl = "graduation";//返回信息 所在控制器

    /*
     * 初始化结构体
     */
//    public $_init =  array(//
//        'sid' => time
//    );

    /**
     * 构造输出函数
     */
    public function make_out()
    {
        $over = array();
        if(!empty($this->info)){
            $over = array_keys($this->info);
        }
        $SonModel = Master::getSon($this->uid);
        $list = $SonModel->getJiehunList();
        $outf = array_diff($list,$over);
        $this->outf = array();
        if(!empty($outf)){
            foreach ($outf as $sid){
                $this->outf[] = array('sid'=> $sid);
            }
        }
    }

    /**
     * 判断是否监学过
     * @param $sid
     */
    public function isRead($sid){
        if(isset($this->info[$sid])){
            Master::error(GZJ_CHILD_HAVE_GRADUATED);
        }
        $this->info[$sid] = Game::get_now();
        $this->save();
    }

}
