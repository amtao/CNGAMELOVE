<?php
require_once "ActBaseModel.php";
/*
 * 帮会红包-膜拜
 */
class Act49Model extends ActBaseModel
{
	public $atype = 49;//活动编号
	
	public $comment = "帮会红包-膜拜";
    public $b_mol = "hbhuodong";//返回信息 所在模块
    public $b_ctrl = "mobai";//返回信息 所在控制器
    public $hd_id = 'huodong_295';
    public $money = 10;

    //字段  money  stime etime
    public function __construct($uid)
    {
        $this->uid = intval($uid);
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        if(!empty($this->hd_cfg)){
            parent::__construct($uid, $this->hd_cfg['info']['id']);
        }
    }

    /*
     * 获取  最后一次登陆时间
     * */
    public function add()
    {
        if(empty($this->hd_cfg)){
            Master::error(ACTHD_OVERDUE);
        }
        if($this->info['day'] == Game::get_today_id()){
            Master::error(MOBAI_TIME_ERR);
        }
        $this->info['day'] = Game::get_today_id();
        Master::add_item($this->uid,KIND_ITEM,1,$this->money);
        $this->save();

        //主线任务
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(19,1);

    }

    public function make_out()
    {
        $outf = array(
            'state' => 1,
            'money' => $this->money,
        );
        if(empty($this->info['day']) || $this->info['day'] != Game::get_today_id()){
            $outf['state'] = 0;
        }
        $this->outf = $outf;
    }

}














