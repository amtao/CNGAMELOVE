<?php
require_once "ActBaseModel.php";
/*
 * 联盟个人信息----贡献兑换商店
 * 每日更新
 */
class Act41Model extends ActBaseModel
{
	public $atype = 41;//活动编号

	public $comment = "联盟个人信息-贡献兑换商店";
	public $b_mol = "club";//返回信息 所在模块
	public $b_ctrl = "memberShop";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array();

	/**
	 * 兑换 联盟物品
	 * @param unknown_type $id  档次id
	 */
	public function shop_buy($gid){

		//获取公会等级
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		if(empty($cid)){
			Master::error(CLUB_NO_HAVE_JOIN);
		}
		$ClubModel = Master::getClub($cid);
		$buildInfo = $ClubModel->getBuildInfo(2);
		$level  =  $buildInfo["lv"];
		//如果没兑换过   初始化兑换次数
		if(empty($this->info[$gid])){
			$this->info[$gid] = 0;
		}

		//判断是否已经开锁
		$cfg_shop_id = Game::getcfg_info('club_shop',$gid);
		if($level < $cfg_shop_id['need_lv']){
			Master::error(CLUB_EXCHANGE_GOODS_UNLOCK);
		}

		//判断是否已经达到购买上限
		$num = $cfg_shop_id['limit_get']; //获取上限次数
		if($this->info[$gid] >= $num ){
			Master::error(CLUB_EXCHANGE_GOODS_MAX);
		}

		//扣除贡献值
		foreach($cfg_shop_id['cost'] as $pay){
			Master::sub_item($this->uid,$pay['kind'],$pay['id'],$pay['count']);
		}

		//获取物品
		Master::add_item3($cfg_shop_id['get']);

		//加次数
		$this->info[$gid] ++;

		//删除缓存
		$ClubModel = Master::getClub($cid);
		$ClubModel->delete_cache();

		$this->save();
	}
}
















