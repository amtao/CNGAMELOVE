<?php
require_once "ActBaseModel.php";
/*
 * 新年活动 - 商城
 */
class Act143Model extends ActBaseModel
{
    public $atype = 143;//活动编号

    public $comment = "新年活动 - 商城";
    public $b_mol = "newyear";//返回信息 所在模块
    public $b_ctrl = "shop";//返回信息 所在控制器
    public $hd_id = 'huodong_298';//活动配置文件关键字
    public $hd_cfg;

    /*
     * 初始化结构体
     */
    public $_init =  array(
        /*
         * id=>num  商城档次id => 已购买数量
        */
    );

    /**
     * @param int $uid   玩家id
     */
    public function __construct($uid)
    {
        $this->uid = intval($uid);

        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);

        parent::__construct($uid,$this->hd_cfg['info']['id'].Game::get_today_id());//执行基类的构造函数
    }

    /*
     * 添加
     * */
    public function add($id,$num = 1)
    {
        if(!is_int($num)){
            Master::error(ACT_HD_ADD_SCORE_NO_INT);
        }
        $this->info[$id] = $this->info[$id] ?  $this->info[$id]+$num : $num;
        $this->save();
    }

    public function back_data()
    {
    }
}
