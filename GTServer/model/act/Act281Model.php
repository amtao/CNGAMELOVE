<?php
require_once "ActHDBaseModel.php";

/*
 * 重阳节活动
 */
class Act281Model extends ActHDBaseModel
{
	public $atype = 281;//活动编号
	public $comment = "重阳节活动";
	public $b_mol = "doubleNinth";//返回信息 所在模块
	public $b_ctrl = "cfg";//子类配置
	public $hd_id = 'huodong_281';//活动配置文件关键字
	public $item_type = 'hd281';
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
	        Master::error(HD281_TYPE8_TIME_LIMIT);
	    }
	    
	    $itemcfg_info = Game::getcfg_info('item',$id);
	    $type = $itemcfg_info['type'][0];
	    $score = $itemcfg_info['type'][1];
	    if(empty($score) || $type != $this->item_type){
	        Master::error(HD_TYPE8_USE_ITEM_ERROR);
	    }
	    
	    $Sev45Model = Master::getSev45($this->hd_cfg['info']['id']);
	    if($this->hd_cfg['boss'] - $Sev45Model->info[$this->date] <=0){
	        Master::error_msg(HD281_TYPE8_KILL_END);
	        $Sev45Model->back_data();
	    }else{
    	    $Sev45Model->add($score);
    	    $Sev45Model->back_data();
    	    
    	    Master::back_win($this->b_mol,"damage","score",$score);
    	    
            //扣除仓库道具
    	    $Act120Model = Master::getAct120($this->uid);
    	    $Act120Model->sub_hdItems($id);
    	    
    	    //添加积分
    	    $Act121Model = Master::getAct121($this->uid);
    	    $Act121Model->add_score($score);
    	 
    	    //加上排行分数
    	    $Redis117Model = Master::getRedis117($this->hd_cfg['info']['id']);
    	    $Redis117Model->zIncrBy($this->uid,$score);
    	    //添加联盟排名信息
    	    $Act40Model = Master::getAct40($this->uid);
    	    if(!empty($Act40Model->info['cid'])){
    	        $Redis118Model = Master::getRedis118($this->hd_cfg['info']['id']);
    	        $Redis118Model->zIncrBy($Act40Model->info['cid'],$score);
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
	    $Act118Model = Master::getAct118($this->uid);
	    if($shop[$id]['is_limit'] == 1 && $shop[$id]['limit'] <= $Act118Model->info[$id]){
	        Master::error(HD_TYPE8_EXCEED_LIMIT);
	    }
	    
	    //扣除
	    Master::sub_item($this->uid,KIND_ITEM,$shop[$id]['need']['id'],$shop[$id]['need']['count']);
	    //购买
	    if($shop[$id]['is_limit'] == 1){
	        $Act118Model->add($id);
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
	    
	    $Act119Model = Master::getAct119($this->uid);
	    if($exchange[$id]['is_limit'] == 1 && $exchange[$id]['limit'] <= $Act119Model->info[$id]){
	        Master::error(HD_TYPE8_EXCEED_LIMIT);
	    }
	    $Act121Model = Master::getAct121($this->uid);
	    $Act121Model->add_score($exchange[$id]['need'],'hfscore');
	    $items = $exchange[$id]['item'];//要兑换的信息
	    if(empty($items['kind'])){
	        $items['kind'] = 1;
	    }
	    $Act119Model->add_items($id);
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
	    $Sev45Model = Master::getSev45($this->hd_cfg['info']['id']);
	    $Sev45Model->back_data();
	    
	    //积分信息
	    $Act121Model = Master::getAct121($this->uid);
	    $Act121Model->back_data();
	    
		//获取商城列表
		$Act118Model = Master::getAct118($this->uid);
		$Act118Model->back_data();
		
		//获取兑换列表
		$Act119Model = Master::getAct119($this->uid);
		$Act119Model->back_data();

		//仓库信息
		$Act120Model = Master::getAct120($this->uid);
		$Act120Model->back_data();
		
		//日志
		$Sev46Model = Master::getSev46($this->hd_cfg['info']['id']);
		$Sev46Model->bake_data();
		
		//累计充值领取档次
		$Act122Model = Master::getAct122($this->uid);
		$Act122Model->back_data();
		  
		//活动信息
		$hd_cfg['info'] = $this->hd_cfg['info'];
	    $hd_cfg['id'] = $hd_cfg['no'];
	    $hd_cfg['rwd'] = $this->hd_cfg['rwd'];
	    $hd_cfg['recharge'] = $this->hd_cfg['recharge'];
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
	    $Redis117Model = Master::getRedis117($this->hd_cfg['info']['id']);
	    $Redis117Model->back_data();
	    $Redis117Model->back_data_my($this->uid);
	    
	    //联盟排行榜
	    $Redis118Model = Master::getRedis118($this->hd_cfg['info']['id']);
	    $Redis118Model->back_data();
	    $Act40Model = Master::getAct40($this->uid);
	    $cid = $Act40Model->info['cid'];
	    if(!empty($cid)){
	        $Redis118Model->back_data_my($cid);
	    }
	}
	/*
	 * 击杀boss奖励
	 * */
	public function KillRwd(){
	    $time = date('Ymd',time());
	    $Sev45Model = Master::getSev45($this->hd_cfg['info']['id']);

	    if($this->hd_cfg['boss'] - $Sev45Model->info[$time] > 0){
	        Master::error(HD281_TYPE8_DONT_LINGQU);
	    }
	    //判断自己是否领取过
	    $Act121Model = Master::getAct121($this->uid);
	    if($Act121Model->info['kill'][$time]){
	        Master::error(HD_TYPE8_HAVE_LINGQU);
	    }
	    $Act121Model->addKillRwd($this->hd_cfg['kill_boss']);
	}
	
	public function back_data_hd() {
	    self::data_out();
	}
	
}
