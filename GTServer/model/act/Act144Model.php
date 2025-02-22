<?php
require_once "ActBaseModel.php";
/*
 * 新年活动 - 积分
 */
class Act144Model extends ActBaseModel
{
    public $atype = 144;//活动编号

    public $comment = "新年活动-积分";
    public $b_mol = "newyear";//返回信息 所在模块
    public $b_ctrl = "score";//返回信息 所在控制器
    public $hd_id = 'huodong_298';//活动配置文件关键字
    public $hd_cfg;

    /**
     * @param int $uid   玩家id
     */
    public function __construct($uid)
    {
        $this->uid = intval($uid);
        //获取活动配置
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        if(!empty($this->hd_cfg['info']['id'])){
            parent::__construct($uid,$this->hd_cfg['info']['id']);//执行基类的构造函数
        }
    }

    /*
     * 初始化结构体
     */
    public $_init =  array(
        /*
         * 'hdscore' =>0,//活动分数
         *  'hfscore' => 0, //花费积分
         * */
    );

    /*
     * 添加积分
     *
     * */
    public function add_score($score,$type='hdscore')
    {
        if(empty($this->hd_cfg)){
            Master::error(ACTHD_OVERDUE);
        }
        $up_date = array('hdscore','hfscore');
        if(empty($score) && !in_array($type, $up_date)){
            Master::error(ACT_HD_INFO_ERROR);
        }
        if($type =='hfscore' && ($this->info['hdscore']-$this->info['hfscore'])<$score){
            Master::error(BOITE_EXCHANGE_SCORE_SHORT);
        }
        if($type == 'hdscore'){
            $Redis127Model = Master::getRedis127($this->hd_cfg['info']['id'].'_'.Game::get_today_long_id());
            $Redis127Model->zIncrBy($this->uid,$score);
            $Redis128Model = Master::getRedis128($this->hd_cfg['info']['id']);
            $Redis128Model->zIncrBy($this->uid,$score);
            $itemid = 42;
        }else{
            $itemid = 43;
        }
        $this->info[$type] += $score;
        $this->save();

        Game::cmd_flow($itemid,$type,$score,$this->info[$type],$this->uid);
    }

    /*
     * 构造输出结构体
     */
    public function make_out(){
        if(empty($this->info['hdscore'])){
            $hdscore = 0;
            $score = 0;
        }else{
            $hdscore = $this->info['hdscore'];
            if(empty($this->info['hfscore'])){
                $this->info['hfscore'] = 0;
            }
            $score = floor($this->info['hdscore'] - $this->info['hfscore']);
        }

        //默认输出直接等于内部存储数据
        $this->outf = array('hdscore'=> $hdscore,'score'=> $score);
    }
}
