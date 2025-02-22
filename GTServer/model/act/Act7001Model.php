<?php
require_once "ActHDBaseModel.php";

/*
 * kv展示
 */
class Act7001Model extends ActHDBaseModel
{
    public $atype = 7001;//活动编号
    public $comment = "kv展示";
    public $b_mol = "kvShow";//返回信息 所在模块
    public $b_ctrl = "cfg";//子类配置
    public $hd_id = 'huodong_7001';//活动配置文件关键字-编号

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'info'      => array(),  //信息
        'address'   => array(),  //资源列表
    );

    /*
	 * 构造输出结构体
	 */
    public function make_out(){

        //构造输出
        $this->outf = array();
        if( self::get_state() == 0 ){
            return;
        }
        $hd_cfg = $this->hd_cfg;
        unset($hd_cfg['info']);

        $this->outf = $hd_cfg;
        $this->back_data_hd();
    }
}
