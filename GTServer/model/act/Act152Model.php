<?php
require_once "ActBaseModel.php";
/*
 * 百服开服充值不断,福利礼包不停
 */
class Act152Model extends ActBaseModel
{
    public $atype = 152;//活动编号
    public $comment = "百服开服充值不断,福利礼包不停";
    public $b_mol = "";//返回信息 所在模块
    public $b_ctrl = "";//子类配置



    /*
     * 初始化结构体
     * 累计数量
     * 领奖档次
     */
    public $_init =  array(
        'id' => 0,    //用于重置
        'num' => 0,   //活动期间充值的元宝数
        'get' => array(),  //奖励发放档次
    );

    /*
     * @param $num 获赠数量
     */
    public function add($num){


        $baifu = Game::baifu_rwd();
        if( empty($baifu['id']) || empty($baifu['order']) ){
            return false;
        }

        if($baifu['id'] != $this->info['id']){
            $this->info = array(
                'id' => $baifu['id'],    //用于重置
                'num' => 0,   //活动期间充值的元宝数
                'get' => array(),  //奖励发放档次
            );
        }

        $this->info['num'] += $num;

        foreach ($baifu['order'] as $k => $v){

            //还没达到
            if($k >  $this->info['num']){
                continue;
            }
            //已经发过
            if(in_array($k,$this->info['get'])){
                continue;
            }

            //发放
            $this->info['get'][] = $k;
            $tip = sprintf($baifu['content'],$this->info['num']);
            Master::sendMail($this->uid, $baifu['title'], $tip,1,$v);
        }
        $this->save();
    }

    /*
     * 返回活动信息
     */
    public function back_data(){

    }

}
