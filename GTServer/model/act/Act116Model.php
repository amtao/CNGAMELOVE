<?php
require_once "ActBaseModel.php";
/*
 * 国庆活动-仓库
 */
class Act116Model extends ActBaseModel
{
	public $atype = 116;//活动编号
	public $b_mol = "nationalDay";//返回信息 所在模块
	public $b_ctrl = "bag";//返回信息 所在控制器
	public $comment = "国庆活动-仓库";
	public $hd_id = "huodong_283";
	public $item_type = "hd283";
	public $hd_cfg;
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		/*
		 * 'id' => num
		 * */
	);
	/*
	 * 扣除仓库道具
	 * */
	public function sub($id,$num=1) {
	    if($this->info[$id]<$num){
	        Master::error(USER_ITEMS_SHORT);
	    }
	    $this->info[$id] -= $num;
	    $this->save();
		Game::cmd_flow(6,$id,-$num,$this->info[$id]);
	}
	
	/*
	 * 添加仓库道具
	 * */
	public function add($id,$num=1) {
	    if(empty($num)){
	        Master::error(USER_ITEMS_NUM_ERROR);
	    }
		if(empty($this->info[$id])){
	        $this->info[$id] = 0;
	    }
	    $this->info[$id] += $num;
	    $this->save();
		Game::cmd_flow(6,$id,$num,$this->info[$id]);
	}
	
	/*
	 * 使用活动道具
	 * */
	public function sub_hdItems($id,$num=1) {
	    $itemcfg_info = Game::getcfg_info('item',$id);
		$type = $itemcfg_info['type'][0];
	    if($type != $this->item_type){
	        Master::error(ACT_HD_NO_ACT_ITEM);
	    }
	    self::sub($id,$num);
	    //随机奖励
	    $this->randreward($itemcfg_info,$num);
	}
	/*
	 * 随机奖励
	 * */
	public function randreward($itemcfg_info,$num=1){
	    //获得随机物品
	    $allitems = array();
	    for($i=0 ; $i < $num ; $i++){
	        $rk = Game::get_rand_key(100,$itemcfg_info['type'][2],'prob');
	        $add_itemid = $itemcfg_info['type'][2][$rk]['id'];
	        $add_itemnum = $itemcfg_info['type'][2][$rk]['count'];
	        $score = $itemcfg_info['type'][1];
	        if(empty($allitems[$add_itemid])){
	            $allitems[$add_itemid] = 0;
	        }
	        $allitems[$add_itemid] += $add_itemnum;
	    }
	    Common::loadModel('HoutaiModel');
	    $hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
	    $Sev44Model = Master::getSev44($hd_cfg['info']['id']);
	    foreach($allitems as $ak => $av){
	        
	        Master::add_item($this->uid,KIND_ITEM,$ak,$av);
	        $itemcfg_info1 = Game::getcfg_info('item',$ak);
	        $type = $itemcfg_info1['type'][3];
	        if($type == 'rizhi'){
	            $Sev44Model->add($this->uid, $itemcfg_info['id'], $ak,$av);
	        }
	        $reward[] = array('id'=>$ak,'count'=>$av,'kind'=>1);
	    }
	    if(!empty($reward)){
	        Master::back_win($this->b_mol,"reward","score",$score);
	        Master::back_win($this->b_mol,"reward","hdscore",$score);
	        Master::back_win($this->b_mol,"reward","items",$reward);
	    }
	    
	}
	
	public function make_out(){
	    if(empty($this->info)){
	        $outof = array();
	    }else{
	        foreach($this->info as $id => $num){
	            if($num >0){
	                $outof[] = array('id'=>$id,'count'=> $num,'kind'=>11);
	            }
	        }
	    }
	    //默认输出直接等于内部存储数据
	    $this->outf = empty($outof) ? array() : $outof;
	}
}
