<?php
require_once "ActHDBaseModel.php";

/*
 * 活动291
 */
class Act291Model extends ActHDBaseModel
{
	public $atype = 291;//活动编号
	public $comment = "双旦-砸蛋活动";
	public $b_mol = "sdhuodong";//返回信息 所在模块
	public $b_ctrl = "zadan";//子类配置
	public $hd_cfg ;//活动配置
	public $hd_id = 'huodong_291';//活动配置文件关键字
	protected $_rank_id = 291;
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
        'time' => 0, //每日重置单抽是否消费提示
        'cons' => 1, //单抽是否消费提示0:不提示 1:提示
	);
	
	
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
	 * 构造输出结构体
	 */
	public function make_out(){

        //构造输出
        $this->outf = array();
        if( parent::get_state() == 0 ){
            Master::error(ACTHD_ACTIVITY_UNOPEN.__LINE__);
        }

        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        $this->outf['cfg'] = $hd_cfg;  //活动期间花费多少元宝

        //更新設置
        if(!Game::is_today($this->info['time'])){
            $this->info['cons'] = 1;
        }

        $this->outf['cons'] = $this->info['cons'];  //单抽是否消费提示0:不提示 1:提示
	}

    /**
     * 砸蛋
     * $cons
     */
    public function zadan($num){

        if( parent::get_state() != 1 ){
            Master::error(ACTHD_OVERDUE);
        }
        //扣元宝
        $pay = $num == 1? $this->outf['cfg']['rwd']['need']:$this->outf['cfg']['rwd']['needTen'];
        Master::sub_item($this->uid,KIND_ITEM,1,$pay);
        //奖励
        $list = array();
        foreach($this->outf['cfg']['rwd']['list'] as $v){
            $list[$v['dc']] = $v;
        }
        //圣诞铃铛奖励
        $ld_list = array();
        foreach($this->outf['cfg']['rwd']['lingdang'] as $v){
            $ld_list[$v['dc']] = $v;
        }

        //统计走马灯
        $zmd_list = array();
        //道具弹窗
        $w_list = array();
        //摇奖循环
        for($i = 1 ; $i<= $num ; $i ++){
            //砸蛋随机奖励
            $rid =  Game::get_rand_key(10000,$list,'prob_10000');
            $zdItem = $list[$rid]['items'];
            Master::add_item2($zdItem);
            //必获圣诞铃铛
            $ldRid =  Game::get_rand_key(10000,$ld_list,'prob_10000');
            $ldItem = $ld_list[$ldRid]['items'];
            Master::add_item2($ldItem);

            //统计走马灯
            $zmd_list[$rid] = empty($zmd_list[$rid])?1:$zmd_list[$rid]+1;

            //列入前端弹窗列表
            $zdItem['tip'] = empty($list[$rid]['tip'])?0:$list[$rid]['tip'];
            $w_list[] = $zdItem;

            $ldItem['tip'] = 0;
            $w_list[] = $ldItem;
        }

        //列入跑马灯
        $UserModel = Master::getUser($this->uid);
        $name = Game::filter_char($UserModel->info['name']);    //玩家名字
        $z_list = array();
        $z_list['name'] = $name;
        foreach($zmd_list as $k => $v){
            $z_list['list'][] = array(
                'id' => $k,
                'count' => $v,
            );
        }
        $this->add_pmd($z_list);

        //获得道具弹窗
        Master::$bak_data['a']['sdhuodong']['win'] = $w_list;

    }


    /**
     * 单抽是否消费提示0:不提示 1:提示
     * $cons
     */
    public function set_cons($cons = 0){

        if( parent::get_state() != 1 ){
            Master::error(ACTHD_OVERDUE);
        }

        $this->info['cons'] = $cons;
        $this->info['time'] = $_SERVER['REQUEST_TIME'];
        $this->save();

        $data = array();
        $data['cons'] = $this->info['cons'];
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$data,true);
    }

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取
        return $news;
    }


    /**
     * 列入跑马灯
     * $data  : 信息
     */
    public function add_pmd($data){

        $Sev81Model = Master::getSev82($this->hd_cfg['info']['id']);
        $Sev81Model->add_msg($data);
    }

    /**
     * 输出跑马灯
     * $uid : 玩家id
     */
    public function out_pmd($uid,$init = 0){
        $Sev81Model = Master::getSev82($this->hd_cfg['info']['id']);
        //初始化
        if($init){
            $Sev81Model->list_init($uid);
        }
        $Sev81Model->list_click($uid);
    }

}




