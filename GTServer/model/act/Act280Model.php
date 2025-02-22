<?php
require_once "ActHDBaseModel.php";

/*
 * 活动280
 */
class Act280Model extends ActHDBaseModel
{
	public $atype = 280;//活动编号
	public $comment = "新官上任";
	public $b_mol = "xghuodong";//返回信息 所在模块
	public $b_ctrl = "cfg";//子类配置
	public $hd_id = 'huodong_280';//活动配置文件关键字
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
	    if(empty($score) || $type !='hd280'){
	        Master::error(HD_TYPE8_USE_ITEM_ERROR);
	    }
	    
	    $Sev32Model = Master::getSev32($this->hd_cfg['info']['id']);
	    if($this->hd_cfg['boss'] - $Sev32Model->info[$this->date] <=0){
	        Master::error(HD_TYPE8_KILL_END);
	    }
	    
	    $Sev32Model->add($score);
	    $Sev32Model->back_data();
	    
	    Master::back_win("xghuodong","damage","score",$score);
	    
        //扣除仓库道具
	    $Act102Model = Master::getAct102($this->uid);
	    $Act102Model->sub_hdItems($id);
	    
	    //添加积分
	    $Act103Model = Master::getAct103($this->uid);
	    $Act103Model->add_score($score);
	 
	    //加上排行分数
	    $Redis106Model = Master::getRedis106($this->hd_cfg['info']['id']);
	    $Redis106Model->zIncrBy($this->uid,$score);
	    //添加联盟排名信息
	    $Act40Model = Master::getAct40($this->uid);
	    if(!empty($Act40Model->info['cid'])){
	        $Redis107Model = Master::getRedis107($this->hd_cfg['info']['id']);
	        $Redis107Model->zIncrBy($Act40Model->info['cid'],$score);
			Game::cmd_other_flow($Act40Model->info['cid'] , 'club', 'huodong_280_'.$this->hd_cfg['info']['id'], array($this->uid), 36, 1, $score, $Redis107Model->zScore($Act40Model->info['cid']));
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
	    $Act100Model = Master::getAct100($this->uid);
	    if($shop[$id]['is_limit'] == 1 && $shop[$id]['limit'] <= $Act100Model->info[$id]){
	        Master::error(HD_TYPE8_EXCEED_LIMIT);
	    }
	    
	    //扣除
	    Master::sub_item($this->uid,KIND_ITEM,$shop[$id]['need']['id'],$shop[$id]['need']['count']);
	    //购买
	    if($shop[$id]['is_limit'] == 1){
	        $Act100Model->add($id);
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
	    
	    $Act101Model = Master::getAct101($this->uid);
	    if($exchange[$id]['is_limit'] == 1 && $exchange[$id]['limit'] <= $Act101Model->info[$id]){
	        Master::error(HD_TYPE8_EXCEED_LIMIT);
	    }
	    $Act103Model = Master::getAct103($this->uid);
	    $Act103Model->add_score($exchange[$id]['need'],'hfscore');
	    $items = $exchange[$id]['item'];//要兑换的信息
	    if(empty($items['kind'])){
	        $items['kind'] = 1;
	    }
	    $Act101Model->add_items($id);
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
	    $Sev32Model = Master::getSev32($this->hd_cfg['info']['id']);
	    $Sev32Model->back_data();
	    
	    //积分信息
	    $Act103Model = Master::getAct103($this->uid);
	    $Act103Model->back_data();
	    
		//获取商城列表
		$Act100Model = Master::getAct100($this->uid);
		$Act100Model->back_data();
		
		//获取兑换列表
		$Act101Model = Master::getAct101($this->uid);
		$Act101Model->back_data();

		//仓库信息
		$Act102Model = Master::getAct102($this->uid);
		$Act102Model->back_data();
		
		//日志
		$Sev30Model = Master::getSev30($this->hd_cfg['info']['id']);
		$Sev30Model->bake_data();
		  
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
	    $Redis106Model = Master::getRedis106($this->hd_cfg['info']['id']);
	    $Redis106Model->back_data();
	    $Redis106Model->back_data_my($this->uid);
	    
	    //联盟排行榜
	    $Redis107Model = Master::getRedis107($this->hd_cfg['info']['id']);
	    $Redis107Model->back_data();
	    $Act40Model = Master::getAct40($this->uid);
	    $cid = $Act40Model->info['cid'];
	    if(!empty($cid)){
	        $Redis107Model->back_data_my($cid);
	    }
	}
	/*
	 * 击杀boss奖励
	 * */
	public function KillRwd(){
	    $time = date('Ymd',time());
	    $Sev32Model = Master::getSev32($this->hd_cfg['info']['id']);

	    if($this->hd_cfg['boss'] - $Sev32Model->info[$time] > 0){
	        Master::error(HD_TYPE8_DONT_LINGQU);
	    }
	    //判断自己是否领取过
	    $Act103Model = Master::getAct103($this->uid);
	    if($Act103Model->info['kill'][$time]){
	        Master::error(HD_TYPE8_HAVE_LINGQU);
	    }
	    $Act103Model->addKillRwd($this->hd_cfg['kill_boss']);
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
		$Act103Model = Master::getAct103($this->uid);
		if(empty($Act103Model->info['hdscore'])) return;
		$Redis107Model = Master::getRedis107($this->hd_cfg['info']['id']);
		$Redis107Model->zIncrBy($cid,-$Act103Model->info['hdscore']);
		Game::cmd_other_flow($cid , 'club', 'huodong_280_'.$this->hd_cfg['info']['id'], array($this->uid), 36, 1, -$Act103Model->info['hdscore'], $Redis107Model->zScore($cid));
	}
	/*
	 * 新加联盟积分
	 * */
	public function addClubScore($cid){
		$status = parent::get_state();
		if($status == 0 || $status == 2) return;
		$Act103Model = Master::getAct103($this->uid);
		$Act103Model->addClubScore($cid);
	}
	/*
	 * 解散帮会 移除信息
	 * */
	public function delClub($cid){
		$status = parent::get_state();
		if($status == 0 || $status == 2) return;
		$Redis107Model = Master::getRedis107($this->hd_cfg['info']['id']);
		$Redis107Model->del_member($cid);
	}
	
}
