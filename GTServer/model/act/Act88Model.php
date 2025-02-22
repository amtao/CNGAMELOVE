<?php
require_once "ActBaseModel.php";
/*
 * 用户回归奖励
 */
class Act88Model extends ActBaseModel
{
	public $atype = 88;//活动编号
	public $comment = "用户回归奖励";
	public $b_mol = "backUserRwd";//返回信息 所在模块
	public $b_ctrl = "back";//子类配置


    /*
     * 初始化结构体
     */
    public $_init =  array(
        'back' => 0, //是不是回归用户  0:不是  1:是
        'time' => 0, //回归的日期
        'rwd'  => array(), //已发放的档次
    );

    /**
     * 回归操作
     * $atime : 上一次登陆时间
     * $regtime : 注册时间
     */
    public function do_login($atime,$regtime){
        //过滤新注册的号 60s缓冲
        if($atime <= $regtime + 60 ){
            return false;
        }
        $peizhi = Game::get_peizhi('backUserRwd');
        if(empty($peizhi)){
            return false;
        }

        //如果不是回归用户 或者  再次回归
        $time = $_SERVER['REQUEST_TIME'] - $atime;  //距离上一次多少时间
        if( $time > $peizhi['backDay'] * 24 * 60 * 60 ){

            //记录回归用户
            $this->info['back'] = 1;
            $this->info['time'] = $_SERVER['REQUEST_TIME'];
            $this->info['rwd'] = array();

            //发放邮件
            Master::sendMail($this->uid, $peizhi['title'], $peizhi['content'],0,0);

        }

        //回归第几天
        $bday = Game::day_count($this->info['time']) + 1;

        if(!empty($peizhi['list'][$bday])){
            //是否已经领取过了
            if(in_array($bday,$this->info['rwd'])){
                return false;
            }
            //奖励信息
            $info = $peizhi['list'][$bday];
            //发放邮件
            Master::sendMail($this->uid, $info['title'], $info['content'],1,$info['rwd']);

            //记录领取
            $this->info['rwd'][] = $bday;
        }
        $this->save();
    }

}







