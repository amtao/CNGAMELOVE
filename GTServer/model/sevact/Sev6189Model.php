<?php
/*
 * 感恩节活动-奖励日志
 */
require_once "SevListBaseModel.php";
class Sev6189Model extends SevListBaseModel
{
    public $comment = "点灯笼-特奖日志";
    public $act = 6189;//活动标签
    public $b_mol = 'ddhuodong';
    public $b_ctrl = 'records';
    protected $_use_lock = false;//是否加锁
    public $_init = array(//初始化数据
        /*
         * array(
         * 	'name' => //玩家id
         *  'pkID' => 12,//应援门客id
         *  'itemid' => 33,//增加的贡献
         * )
         */
    );

    /*
     * 构造业务输出数据
     */
    public function mk_outf(){
        $outf = array();
        $temparra = $this->info;//倒序输出
        foreach($temparra as $k => $v){

            $UserModel = Master::getUser($v['name']);
            $name = $UserModel->info['name'];

            $fuidInfo['name'] = $name;
            $fuidInfo['itemid'] = $v['itemid'];

            $outf[] = $fuidInfo;
        }
        return $outf;
    }

    /*
     * 添加一条投票信息
     */
    public function add($uid,$itemid){

        $this->info[] = array(
            'name' => $uid,
            'itemid' => $itemid,
        );
        //截取数据表
        $max_num = 10;
        if (count($this->info) > $max_num){
            $this->info = array_slice($this->info,-$max_num);
        }
        $this->save();
    }

    /*
     * 返回协议信息
     */
    public function bake_data(){
        $data = self::mk_outf();
        Master::back_data(0,$this->b_mol,$this->b_ctrl,$data);
    }
}
