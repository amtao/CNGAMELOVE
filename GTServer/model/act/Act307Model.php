<?php
require_once "ActBaseModel.php";
/*
 * 跨服大理寺领取全服奖励
 */
class Act307Model extends ActBaseModel
{
	public $atype = 307;//活动编号
	public $b_mol = "kuayamen";//返回信息 所在模块
	public $b_ctrl = "lingqu";//返回信息 所在控制器
	public $comment = "跨服大理寺-领取全服奖励";
	public $hd_id = "huodong_300";
	public $hd_cfg;
	
	public function __construct($uid){
	    $this->uid = $uid;
		Common::loadModel('HoutaiModel');
	    $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
	    if(!empty($this->hd_cfg['info']['id'])){
	        parent::__construct($uid,$this->hd_cfg['info']['id']);
	    }
	}
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'state' => 0
	);
	/*
	 * 添加领取记录
	 * */
	public function add(){
	    if(empty($this->info['state'])){
	        //获取全服礼物配置
	        $gift = $this->hd_cfg['rwd']['sever'];
	        //获取当前当前用户服务器的排名
	        $SevCfgObj = Common::getSevCfgObj(Game::get_sevid($this->uid));
	        $Redis305Model = Master::getRedis305($this->hd_cfg['info']['id']);
	        $rank = $Redis305Model->get_rank_id($SevCfgObj->getHE());
	        foreach ($gift as $v){
	            if($v['rand']['rs']<=$rank && $v['rand']['re']>=$rank){
					foreach ($v['member'] as $item){
						Master::add_item($this->uid,empty($item['kind'])?1:$item['kind'],$item['id'],$item['count']);
					}
	            }
	        }
	        $this->info['state'] = 1;
	        $this->save();
	    }
	}
	
	//构造输出函数
	public function make_out(){
	    $this->outf = $this->info;
	}
	
	/*
	 * 是否可以参与
	 * */
	public function is_lingqu(){
	    if($this->info['state'] == 1){
	        return true;
	    }
	    return false;
	}
}
