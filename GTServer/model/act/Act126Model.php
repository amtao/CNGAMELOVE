<?php
require_once "ActBaseModel.php";
/*
 * 感恩节活动 - 积分
 */
class Act126Model extends ActBaseModel
{
    public $atype = 126;//活动编号

    public $comment = "感恩节活动 - 积分";
    public $b_mol = "ThanksDay";//返回信息 所在模块
    public $b_ctrl = "score";//返回信息 所在控制器
    public $hd_id = 'huodong_284';//活动配置文件关键字
    public $hd_cfg;

    /**
     * @param unknown_type $uid   玩家id
     * @param unknown_type $id    活动id
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
         *  'kill' => array(
         *     '时间' => 1,  //杀死boss 是否领取
         *  ),
         * */
    );

    /*
     * 添加积分
     *
     * */
    public function add_score($score,$type='hdscore')
    {
        $up_date = array('hdscore','hfscore');
        if(empty($score) && !in_array($type, $up_date)){
            Master::error(ACT_HD_INFO_ERROR);
        }
        if($type =='hfscore' && ($this->info['hdscore']-$this->info['hfscore'])<$score){
            Master::error(BOITE_EXCHANGE_SCORE_SHORT);
        }
        if($type == 'hdscore'){
            //加上排行分数
            $Redis119Model = Master::getRedis119($this->hd_cfg['info']['id']);
            $Redis119Model->zIncrBy($this->uid,$score);
            //添加联盟排名信息
            $Act40Model = Master::getAct40($this->uid);
            if(!empty($Act40Model->info['cid'])){
                $Redis120Model = Master::getRedis120($this->hd_cfg['info']['id']);
                $Redis120Model->zIncrBy($Act40Model->info['cid'],$score);
                Game::cmd_other_flow($Act40Model->info['cid'] , 'club', 'huodong_284_'.$this->hd_cfg['info']['id'], array($this->uid), 31, 1, $score, $Redis120Model->zScore($Act40Model->info['cid']));
            }
        }
        $this->info[$type] += $score;
        $this->save();
    }

    /*
     * 添加联盟积分
     * */
    public function addClubScore($cid){
        if(empty($this->info['hdscore'])) return;
        $Redis120Model = Master::getRedis120($this->hd_cfg['info']['id']);
        $Redis120Model->zIncrBy($cid,$this->info['hdscore']);
        Game::cmd_other_flow($cid , 'club', 'huodong_284_'.$this->hd_cfg['info']['id'], array($this->uid), 31, 1, $this->info['hdscore'], $Redis120Model->zScore($cid));
        $this->save();
    }
    /*
     *迁移联盟记录数据
     * */
    public function removeClubHis($cid){
        if(empty($this->info['club'][$cid])) return;
        $this->info['history'][] = array('cid' => $cid,'score' => $this->info['club'][$cid]);
        $this->save();
    }
    /*
     * 领取killboss奖励
     *
     * */
    public function addKillRwd($boss_cfg){
        if(!empty($boss_cfg)){
            $rk = Game::get_rand_key(100,$boss_cfg,'prob_100');
            if(!empty($boss_cfg[$rk])){
                Master::add_item2($boss_cfg[$rk]);
            }
        }

        $time = date('Ymd',time());
        $this->info['kill'][$time] = 1;
        $this->save();
    }

    /*
     * 构造输出结构体
     */
    public function make_out(){
        $outof = array();
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
        $time = date('Ymd',time());

        $kill_rwd = empty($this->info['kill'][$time]) ? 0 : $this->info['kill'][$time];

        //默认输出直接等于内部存储数据
        $this->outf = array('hdscore'=> $hdscore,'score'=> $score,'kill_rwd' => $kill_rwd);
    }
}
