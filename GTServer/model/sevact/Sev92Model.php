<?php
/*
 * 跑马灯--系统
 */
require_once "SevListBaseModel.php";
class Sev92Model extends SevListBaseModel
{
	public $comment = "跑马灯--系统";
	
    public $b_mol = "user";//返回信息 所在模块
    public $b_ctrl = "system";//返回信息 所在控制器
	public $act = 92;//活动标签
	protected $_use_lock = false;//是否加锁
	
	public $_init = array(//初始化数据

	);
	
	/*
     * 添加一条信息
     */
    public function add_msg($params){

        $cfg_data = Game::get_peizhi('paoMaDeng');
        $cfg = empty($cfg_data['system'])?array():$cfg_data['system'];

        if( empty($type) || empty($cfg[$type]) ){
            return false;
        }







    	$data = array(
            'ef' => empty($cfg[$type]['ef'])?1:$cfg[$type]['ef'],  //特效: 1:默认
            'ob' => 2, //产生对象:1:玩家,2:系统,3:客服
            'time' => Game::get_now(), //时间
			'msg' => empty($cfg[$type]['msg'])?1:$cfg[$type]['msg'],
		);
		parent::list_push($data);

    }




}





