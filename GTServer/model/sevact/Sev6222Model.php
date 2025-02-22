<?php
/*
 * 清明节
 */
require_once "SevBaseModel.php";
class Sev6222Model extends SevBaseModel
{
    public $comment = "清明节-奖励日志";
    public $act = 6222;//活动标签
    public $b_mol = 'qingming';
    public $b_ctrl = 'rwdLog';
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
            $fuidInfo['item'] = $v['item'];
            $outf[] = $fuidInfo;
        }
        return $outf;
    }

    /*
     * 添加一条奖励信息
     */
    public function add($uid,$item){

        $this->info[] = array(
            'name' => $uid,
            'item' => $item,
        );
        //截取数据表
        $max_num = 50;
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