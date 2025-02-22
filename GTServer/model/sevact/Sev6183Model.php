<?php
/*
 * 连续每日充值
 */
require_once "SevBaseModel.php";
class Sev6183Model extends SevBaseModel
{
    public $comment = "雪人信息";
    public $act = 6183;
    public $b_mol = "dxrhuodong";//返回信息 所在模块
    public $b_ctrl = "snowman";//子类配置
    public $hd_id = 'huodong_6183';//活动配置文件关键字
    public $hd_cfg;

    public function __construct($hid,$cid)
    {
        //获取活动配置
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        if (empty($this->hd_cfg)){
            return ;
        }
        parent::__construct($hid,$cid);

    }
    
    /*
	 * 初始化数据
	 */
    public $_init = array(
        'lv'   =>1,
        'Hurt' =>0,
        'log'  =>array(),
    );

    /**
     * 堆雪人
     * @return bool
     */
    public function setHurt($num)
    {
        $this->info['Hurt'] += $num;
        $hurt = $this->info['Hurt'];
        foreach ($this->hd_cfg['boss'] as $k=>$v){
            if($hurt >= $v['hp']){
                $hurt -= $v['hp'];
                continue;
            }
            $this->info['lv'] = $v['lv'];
            break;
        }
        $this->save();
    }

    /*
	 * 构造业务输出数据
	 */
    public function mk_outf(){
        $outf = array();
        $hd_cfg = array();
        $hurt = $this->info['Hurt'];
        $lv = $this->info['lv'];
        $max_lv = count($hd_cfg = $this->hd_cfg['boss'])-1;
        if (empty($this->info['Hurt'])){
            $hd_cfg = $this->hd_cfg['boss'][1];
            $hd_cfg['val'] = 0;
        }else{
            foreach ($this->hd_cfg['boss'] as $k=>$v){
                if($hurt >= $v['hp']){
                    $hurt -= $v['hp'];
                }
            }
            $hd_cfg['lv'] = $lv;
            $hd_cfg['val'] = $hurt;
            $hd_cfg['hp'] = empty($this->hd_cfg['boss'][$lv]['hp'])?$this->hd_cfg['boss'][$max_lv]['hp']:$this->hd_cfg['boss'][$lv]['hp'];
            $hd_cfg['skin'] = empty($this->hd_cfg['boss'][$lv]['skin'])?$this->hd_cfg['boss'][$max_lv]['skin']:$this->hd_cfg['boss'][$lv]['skin'];

        }
        $outf['bossinfo'] = $hd_cfg;
        return $outf;

    }

    /*
     * 添加一条投票信息
     */
    public function add($uid,$itemid){

        $this->info['log'][] = array(
            'name' => $uid,
            'itemid' => $itemid,
        );
        //截取数据表
        $max_num = 8;
        if (count($this->info['log']) > $max_num){
            $this->info['log'] = array_slice($this->info['log'],-$max_num);
        }
        $this->save();
    }

    /*
     * 构造业务输出数据
     */
    public function log_outf(){
        $log = array();
        $temparra = $this->info['log'];//倒序输出
        foreach($temparra as $k => $v){

            $UserModel = Master::getUser($v['name']);
            $name = $UserModel->info['name'];

            $fuidInfo['name'] = $name;
            $fuidInfo['itemid'] = $v['itemid'];

            $log[] = $fuidInfo;
        }
        return $log;
    }

    /*
     * 返回协议信息
     */
    public function back_data(){
        $data = $this->mk_outf();
        if($data){
            Master::back_data(0,$this->b_mol,$this->b_ctrl,$data);
        }
    }

    /*
     * 返回协议信息
     */
    public function back_log_data(){
        $log = $this->log_outf();
        if($log){
            Master::back_data(0,$this->b_mol,'records',$log);
        }
    }

}