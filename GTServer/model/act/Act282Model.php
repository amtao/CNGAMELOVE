<?php
require_once "ActHDBaseModel.php";

/*
 * 活动282
 */
class Act282Model extends ActHDBaseModel
{
	public $atype = 282;//活动编号
	public $comment = "惩戒来福";
	public $b_mol = "penalize";//返回信息 所在模块
	public $b_ctrl = "cfg";//子类配置
	public $hd_id = 'huodong_282';//活动配置文件关键字
	public $date;
	
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
	);
	
	/*
	 * 鞭打
	 * id 道具id
	 * */
	public function play($id){
	    $this->date = date('Ymd',time());
	    //判断活动是否结束
    	if( parent::get_state() == 0 || parent::get_state() == 2){
	        Master::error(ACTHD_OVERDUE);
	    }
	    
	    $h = date('H',time());
	    if($h<9 || $h>22){
	        Master::error(HD_TYPE8_TIME_LIMIT);
	    }
	    
	    $itemcfg_info = Game::getcfg_info('item',$id);
	    $type = $itemcfg_info['type'][0];
	    $score = $itemcfg_info['type'][1];
	    if(empty($score) || $type !='hd282'){
	        Master::error(HD_TYPE8_USE_ITEM_ERROR);
	    }
	    
	    $Sev36Model = Master::getSev36($this->hd_cfg['info']['id']);
	    if($this->hd_cfg['boss'] - $Sev36Model->info[$this->date] <=0){
	        Master::error(HD_TYPE8_KILL_END);
	    }
	    $Sev36Model->add($score);
	    $Sev36Model->back_data();
	    
	    Master::back_win("penalize","damage","score",$score);
	    
        //扣除仓库道具
	    $Act106Model = Master::getAct106($this->uid);
	    $Act106Model->sub_hdItems($id);
	    
	    //添加积分
	    $Act107Model = Master::getAct107($this->uid);
	    $Act107Model->add_score($score);
	 
	    //加上排行分数
	    $Redis112Model = Master::getRedis112($this->hd_cfg['info']['id']);
	    $Redis112Model->zIncrBy($this->uid,$score);
	    //添加联盟排名信息
	    $Act40Model = Master::getAct40($this->uid);
	    if(!empty($Act40Model->info['cid'])){
	        $Redis113Model = Master::getRedis113($this->hd_cfg['info']['id']);
	        $Redis113Model->zIncrBy($Act40Model->info['cid'],$score);
			Game::cmd_other_flow($Act40Model->info['cid'] , 'club', 'huodong_282_'.$this->hd_cfg['info']['id'], array($this->uid), 35, 1, $score, $Redis113Model->zScore($Act40Model->info['cid']));
	    }
	}
	
	/*
	 * 商品购买
	 * id 商品列表档次 id
	 * */
	public function buyone($id){
	    if( parent::get_state() == 0){
	        Master::error(ACTHD_OVERDUE);
	    }
	     
	    //判断id是否可以兑换
	    $shop_cfg = $this->hd_cfg['shop'];
	    if(empty($shop_cfg)){
	        Master::error(HD_TYPE8_DONT_SHOPING);
	    }
	    
	    foreach ($shop_cfg as $item){
	        $shop[$item['id']] = $item;
	    }
	    if(empty($shop[$id]) || empty($shop[$id]['need'])){
	        Master::error(HD_TYPE8_SHOP_NO_FUND);
	    }
	    $Act104Model = Master::getAct104($this->uid);
	    if($shop[$id]['is_limit'] == 1 && $shop[$id]['limit'] <= $Act104Model->info[$id]){
	        Master::error(HD_TYPE8_EXCEED_LIMIT);
	    }
	    
	    //扣除
	    Master::sub_item($this->uid,KIND_ITEM,$shop[$id]['need']['id'],$shop[$id]['need']['count']);
	    //购买
	    if($shop[$id]['is_limit'] == 1){
	        $Act104Model->add($id);
	    }
	    
	    $items = $shop[$id]['items'];
	    if(empty($items['kind'])){
	        $items['kind'] = 1;
	    }
	    
	    Master::add_item($this->uid,11,$items['id'],$items['count']);
	    
	}
	/*
	 * 商品兑换
	 * 兑换列表档次id
	 * */
	public function exchange($id){
	    
	    if( parent::get_state() == 0){
	        Master::error(ACTHD_OVERDUE);
	    }
	     
	    //判断id是否可以兑换
	    
	    if(empty($this->hd_cfg['exchange']) ){
	        Master::error(HD_TYPE8_EXCHANGE_NO_FUND);
	    }
	    foreach ($this->hd_cfg['exchange'] as $ite){
	        $exchange[$ite['id']] = $ite;
	    }
	    if(empty($exchange[$id]) ){
	        Master::error(HD_TYPE8_EXCHANGE_NO_FUND);
	    }
	    
	    $Act105Model = Master::getAct105($this->uid);
	    if($exchange[$id]['is_limit'] == 1 && $exchange[$id]['limit'] <= $Act105Model->info[$id]){
	        Master::error(HD_TYPE8_EXCEED_LIMIT);
	    }
	    $Act107Model = Master::getAct107($this->uid);
	    $Act107Model->add_score($exchange[$id]['need'],'hfscore');
	    $items = $exchange[$id]['item'];//要兑换的信息
	    if(empty($items['kind'])){
	        $items['kind'] = 1;
	    }
	    $Act105Model->add_items($id);
	    Master::add_item($this->uid,$items['kind'],$items['id'],$items['count']);
	}
	
	/*
	 * 构造输出
	 */
	public function data_out(){
	    $this->date = date('Ymd',time());
	    //活动状态
	    if( parent::get_state() == 0){
	        Master::error(ACTHD_OVERDUE);
	    }
	    //boss血量
	    $Sev36Model = Master::getSev36($this->hd_cfg['info']['id']);
	    $Sev36Model->back_data();
	    
	    //积分信息
	    $Act107Model = Master::getAct107($this->uid);
	    $Act107Model->back_data();
	    
		//获取商城列表
		$Act104Model = Master::getAct104($this->uid);
		$Act104Model->back_data();
		
		//获取兑换列表
		$Act105Model = Master::getAct105($this->uid);
		$Act105Model->back_data();

		//仓库信息
		$Act106Model = Master::getAct106($this->uid);
		$Act106Model->back_data();
		
		//日志
		$Sev37Model = Master::getSev37($this->hd_cfg['info']['id']);
		$Sev37Model->bake_data();
		  
		//活动信息
		$hd_cfg['info'] = $this->hd_cfg['info'];
	    $hd_cfg['id'] = $hd_cfg['no'];
	    $hd_cfg['rwd'] = $this->hd_cfg['rwd'];
	    $hd_cfg['boss'] = $this->hd_cfg['boss'];
	    $hd_cfg['story'] = $this->hd_cfg['story'];
	    $hd_cfg['play_time'] = array('startTime'=>9,'endTime'=>22);
	    Master::back_data($this->uid,$this->b_mol,'cfg',$hd_cfg);
	}
	
	
	public function get_news(){
	     return 0;
	}
	
	
	/*
	 * 排行榜 和奖励
	 * */
	public function paihang(){
	    //个人排行榜
	    $Redis112Model = Master::getRedis112($this->hd_cfg['info']['id']);
	    $Redis112Model->back_data();
	    $Redis112Model->back_data_my($this->uid);
	    
	    //联盟排行榜
	    $Redis113Model = Master::getRedis113($this->hd_cfg['info']['id']);
	    $Redis113Model->back_data();
	    $Act40Model = Master::getAct40($this->uid);
	    $cid = $Act40Model->info['cid'];
	    if(!empty($cid)){
	        $Redis113Model->back_data_my($cid);
	    }
	}
	/*
	 * 击杀boss奖励
	 * */
	public function KillRwd(){
	    $time = date('Ymd',time());
	    $Sev36Model = Master::getSev36($this->hd_cfg['info']['id']);

	    if($this->hd_cfg['boss'] - $Sev36Model->info[$time] > 0){
	        Master::error(HD_TYPE8_DONT_LINGQU);
	    }
	    //判断自己是否领取过
	    $Act107Model = Master::getAct107($this->uid);
	    if($Act107Model->info['kill'][$time]){
	        Master::error(HD_TYPE8_HAVE_LINGQU);
	    }
	    $Act107Model->addKillRwd($this->hd_cfg['kill_boss']);
	}
	
	public function back_data_hd() {
	    self::data_out();
	}
	/*
	 * 离开联盟扣除积分
	 * */
	public function delClubScore($cid){
		$status = parent::get_state();
		if($status == 0 || $status == 2) return;
		//获取当前用户在这个联盟的积分
		$Act107Model = Master::getAct107($this->uid);
		if(empty($Act107Model->info['hdscore'])) return;
		$Redis113Model = Master::getRedis113($this->hd_cfg['info']['id']);
		$Redis113Model->zIncrBy($cid,-$Act107Model->info['hdscore']);
		Game::cmd_other_flow($cid , 'club', 'huodong_282_'.$this->hd_cfg['info']['id'], array($this->uid), 35, 1, -$Act107Model->info['hdscore'], $Redis113Model->zScore($cid));
	}
	/*
	 * 新加联盟积分
	 * */
	public function addClubScore($cid){
		$status = parent::get_state();
		if($status == 0 || $status == 2) return;
		$Act107Model = Master::getAct107($this->uid);
		$Act107Model->addClubScore($cid);
	}
	/*
	 * 解散帮会 移除信息
	 * */
	public function delClub($cid){
		$status = parent::get_state();
		if($status == 0 || $status == 2) return;
		$Redis113Model = Master::getRedis113($this->hd_cfg['info']['id']);
		$Redis113Model->del_member($cid);
	}
}
