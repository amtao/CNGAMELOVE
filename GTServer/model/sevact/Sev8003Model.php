<?php
/*
 * 许愿池
 */
require_once "SevBaseModel.php";
class Sev8003Model extends SevBaseModel
{
    public $comment = "许愿池";
    public $act = 8003;
    public $b_mol = "wishingWell";//返回信息 所在模块
    public $b_ctrl = "well";//子类配置
    public $hd_id = 'huodong_8003';//活动配置文件关键字
    public $hd_cfg;
    protected $_server_type = 3;//1：合服，2：跨服，3：全服，4：指定跨服，5：本服，6：指定服务器

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
        'count' => 0,
    );

    /*
     * 添加一条投票信息
     */
    public function add($num){

        $this->info['count'] += $num;
        $this->save();
    }

    /*
     * 添加一条投票信息
     */
    public function getCount(){

        return $this->info['count'];
    }
}