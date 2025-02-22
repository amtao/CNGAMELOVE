<?php
/*
 * 盛装出席通关记录
 */
require_once "SevListBaseModel.php";
class Sev6123Model extends SevListBaseModel
{
	public $comment = "盛装出席通关记录";
	public $act = 6123;//活动标签
	
	public $b_mol = "clothepve";//返回信息 所在模块
    public $b_ctrl = "logs";//返回信息 所在控制器
    protected $_use_lock = false;//是否加锁
	
	public $_init = array(//初始化数据
		/*
		array(
			'id' => $this->uid,	//id
			'uid' => $this->uid,	//uid
			'name' =>
			'score' =>
			'head' =>
			'body' =>
			'ear' =>
		    'background' =>
		    'effect' =>
		    'animal' =>
		));
		time 时间
		 */
	);

    /*
     * 列表构造输出
     */
    public function list_mk_outf($v_info){
        $userInfo = Master::fuidInfo($v_info['uid']);
        $userInfo['clothe']['head'] = $v_info['head'];
        $userInfo['clothe']['body'] = $v_info['body'];
        $userInfo['clothe']['ear'] = $v_info['ear'];
        $userInfo['clothe']['background'] = $v_info['background'];
        $userInfo['clothe']['effect'] = $v_info['effect'];
        $userInfo['clothe']['animal'] = $v_info['animal'];
        $v_info['fuser'] = $userInfo;
        return $v_info;
    }

	public function get_referr($uid, $id){
	    $list = array();
        foreach ($this->info as $k => $v) {
           if ($v['gate'] == $id){
               $list[] = $v;
           }
        }
        $rand = rand(0, count($list)-1);
        $info = $list[$rand];
        if (!empty($list[$rand])){
            $userInfo = Master::fuidInfo($info['uid']);
            $userInfo['clothe']['head'] = $info['head'];
            $userInfo['clothe']['body'] = $info['body'];
            $userInfo['clothe']['ear'] = $info['ear'];
            $userInfo['clothe']['background'] = $info['background'];
            $userInfo['clothe']['effect'] = $info['effect'];
            $userInfo['clothe']['animal'] = $info['animal'];
            $info['fuser'] = $userInfo;
        }
        else {
            $info = array();
        }
        Master::back_data($uid,$this->b_mol, 'referr', $info);
    }
    
	/*
	 * 添加一条击杀记录
	 */
	public function add_msg($data){
		$data['time'] = Game::get_now();
		parent::list_push($data);
	}
}
