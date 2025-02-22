<?php
/*
 * 新年活动-奖励日志
 */
require_once "SevListBaseModel.php";
class Sev201Model extends SevListBaseModel
{
    public $comment = "新年活动-奖励日志";
    public $act = 201;//活动标签
    public $b_mol = 'newyear';
    public $b_ctrl = 'rwdLog';
    protected $_use_lock = false;//是否加锁
    public $_init = array(//初始化数据
        /*
         * array(
         * 	'uid' => 10086
         *  'itemid' => 33,//获得什么道具
         *  'num' => 1,数量
         * )
         */
    );

    /*
     * 构造业务输出数据
     */
    public function mk_outf(){
        $outf = array();
        $temparra = array_reverse($this->info);//倒序输出
        foreach($temparra as $k => $v){
            if (isset($v['uName'])) {
                $name = $v['uName'];
            } else {
                $fuserInfo = Master::fuidInfo($v['uid']);
                $name = $fuserInfo['name'];
            }
            $fuidInfo['name'] = $name;
            $fuidInfo['item'] = $v['itemid'];
            $fuidInfo['num'] = $v['num'];
            $fuidInfo['uitem'] = empty($v['kill']) ? 0 : $v['kill'];
            $outf[] = $fuidInfo;
        }
        return $outf;
    }

    /*
     * 添加一条奖励信息
     */
    public function add($uid,$itemid,$num,$kill){
        $fuserInfo = Master::fuidInfo($uid);
        $this->info[] = array(
            'uid' => $uid,
            'uName' => $fuserInfo['name'],
            'itemid' => $itemid,
            'num' => $num,
            'kill' => $kill
        );
        //截取数据表
        $max_num = 30;
        if (count($this->info) > $max_num){
            $this->info = array_slice($this->info,-$max_num,$max_num,1);
        }
        $this->save();
    }

    /*
     * 返回协议信息
     */
    public function bake_data(){
        $data = $this->get_outf();
        Master::back_data(0,$this->b_mol,$this->b_ctrl,$data);
    }
}
