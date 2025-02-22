<?php
require_once "ActHDBaseModel.php";

/*
 * 活动283
 */
class Act283Model extends ActHDBaseModel
{
	public $atype = 283;//活动编号
	public $comment = "国庆活动";
	public $b_mol = "nationalDay";//返回信息 所在模块
	public $b_ctrl = "cfg";//子类配置
	public $hd_id = 'huodong_283';//活动配置文件关键字
	public $item_type = 'hd283';
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
	    if(empty($score) || $type != $this->item_type){//替换需要改
	        Master::error(HD_TYPE8_USE_ITEM_ERROR);
	    }
	    
	    $Sev43Model = Master::getSev43($this->hd_cfg['info']['id']);
	    if($this->hd_cfg['boss'] - $Sev43Model->info[$this->date] <=0){
	        Master::error_msg(HD_TYPE8_KILL_END);
	        $Sev43Model->back_data();
	    }else{
    	    $Sev43Model->add($score);
    	    $Sev43Model->back_data();
    	    
    	    Master::back_win($this->b_mol,"damage","score",$score);
    	    
            //扣除仓库道具
    	    $Act116Model = Master::getAct116($this->uid);
    	    $Act116Model->sub_hdItems($id);
    	    
    	    //添加积分
    	    $Act117Model = Master::getAct117($this->uid);
    	    $Act117Model->add_score($score);
    	 
    	    //加上排行分数
    	    $Redis115Model = Master::getRedis115($this->hd_cfg['info']['id']);
    	    $Redis115Model->zIncrBy($this->uid,$score);
    	    //添加联盟排名信息
    	    $Act40Model = Master::getAct40($this->uid);
    	    if(!empty($Act40Model->info['cid'])){
    	        $Redis116Model = Master::getRedis116($this->hd_cfg['info']['id']);
    	        $Redis116Model->zIncrBy($Act40Model->info['cid'],$score);
    	    }
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
	    $Act114Model = Master::getAct114($this->uid);
	    if($shop[$id]['is_limit'] == 1 && $shop[$id]['limit'] <= $Act114Model->info[$id]){
	        Master::error(HD_TYPE8_EXCEED_LIMIT);
	    }
	    
	    //扣除
	    Master::sub_item($this->uid,KIND_ITEM,$shop[$id]['need']['id'],$shop[$id]['need']['count']);
	    //购买
	    if($shop[$id]['is_limit'] == 1){
	        $Act114Model->add($id);
	    }
	    
	    $items = $shop[$id]['items'];
	    if(empty($items['kind'])){
	        $items['kind'] = 1;
	    }
	    $item_cfg = Game::getcfg_info('item', $items['id']);
	    if($item_cfg['type'][1] == 'list'){
	        foreach ($item_cfg['type'][2] as $item){
	            Master::add_item($this->uid,empty($item['kind']) ? 1 : $item['kind'],$item['id'],$item['num']);
	        }
	    }else{
	        Master::add_item($this->uid,$items['kind'],$items['id'],$items['count']);
	    }
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
	    
	    $Act115Model = Master::getAct115($this->uid);
	    if($exchange[$id]['is_limit'] == 1 && $exchange[$id]['limit'] <= $Act115Model->info[$id]){
	        Master::error(HD_TYPE8_EXCEED_LIMIT);
	    }
	    $Act117Model = Master::getAct117($this->uid);
	    $Act117Model->add_score($exchange[$id]['need'],'hfscore');
	    $items = $exchange[$id]['item'];//要兑换的信息
	    if(empty($items['kind'])){
	        $items['kind'] = 1;
	    }
	    $Act115Model->add_items($id);
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
	    $Sev43Model = Master::getSev43($this->hd_cfg['info']['id']);
	    $Sev43Model->back_data();
	    
	    //积分信息
	    $Act117Model = Master::getAct117($this->uid);
	    $Act117Model->back_data();
	    
		//获取商城列表
		$Act114Model = Master::getAct114($this->uid);
		$Act114Model->back_data();
		
		//获取兑换列表
		$Act115Model = Master::getAct115($this->uid);
		$Act115Model->back_data();

		//仓库信息
		$Act116Model = Master::getAct116($this->uid);
		$Act116Model->back_data();
		
		//日志
		$Sev44Model = Master::getSev44($this->hd_cfg['info']['id']);
		$Sev44Model->bake_data();
		  
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
	    $Redis115Model = Master::getRedis115($this->hd_cfg['info']['id']);
	    $Redis115Model->back_data();
	    $Redis115Model->back_data_my($this->uid);
	    
	    //联盟排行榜
	    $Redis116Model = Master::getRedis116($this->hd_cfg['info']['id']);
	    $Redis116Model->back_data();
	    $Act40Model = Master::getAct40($this->uid);
	    $cid = $Act40Model->info['cid'];
	    if(!empty($cid)){
	        $Redis116Model->back_data_my($cid);
	    }
	}
	/*
	 * 击杀boss奖励
	 * */
	public function KillRwd(){
	    $time = date('Ymd',time());
	    $Sev43Model = Master::getSev43($this->hd_cfg['info']['id']);

	    if($this->hd_cfg['boss'] - $Sev43Model->info[$time] > 0){
	        Master::error(HD_TYPE8_DONT_LINGQU);
	    }
	    //判断自己是否领取过
	    $Act117Model = Master::getAct117($this->uid);
	    if($Act117Model->info['kill'][$time]){
	        Master::error(HD_TYPE8_HAVE_LINGQU);
	    }
	    $Act117Model->addKillRwd($this->hd_cfg['kill_boss']);
	}
	
	public function back_data_hd() {
	    self::data_out();
	}
	
}
