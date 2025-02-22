<?php
/*
 * 连续每日充值
 */
require_once "SevBaseModel.php";
class Sev6168Model extends SevBaseModel
{
    public $comment = "天天充值活动信息";
    public $act = 6168;
    public $b_mol = "edczhuodong";//返回信息 所在模块
    public $b_ctrl = "everyday";//子类配置
    public $hd_id = 'huodong_6168';//活动配置文件关键字
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
//        'key'=>0,
//        'time' =>0,
//        'items'=>array(),
    );


    /**
     * 设置当天奖励信息
     * @return bool
     */
    public function setReward()
    {
        if (empty($this->info)){
            $this->info['key'] = 1;
            $this->info['time'] = Game::get_now();
            $this->info['items'] = $this->hd_cfg['rwd'][1];
            $this->save();
        }else{
            if ($this->info['time'] < Game::day_0()){
                $key = empty( $this->hd_cfg['rwd'][$this->info['key']+1] )?1:$this->info['key']+1;
                $this->info['key'] = $key;
                $this->info['time'] = Game::get_now();
                $this->info['items'] = $this->hd_cfg['rwd'][$key];
                $this->save();
            }
        }


    }
    /*
	 * 构造业务输出数据
	 */
    public function mk_outf(){
        $outf = array();
        $this->setReward();
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        $hd_cfg['rwd'] = $this->info['items'];
        unset($hd_cfg['info']['no']);
        $outf['cfg'] = $hd_cfg;
        return $outf;

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

}