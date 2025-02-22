<?php
/*
 * 联盟-每日贡献列表信息
 */
require_once "SevBaseModel.php";
class Sev10Model extends SevBaseModel
{
	public $comment = "联盟-每日贡献列表信息";
	public $act = 10;//活动标签
	
	public $_init = array(//初始化数据
		/*
		array(
			uid => 贡献档次, // 0:未建设 1:初建   2:中建  3:高建  4:道具建设   5:高级道具建设
		),
		 */
	);
	
	public function __construct($hid,$cid){
		$serverID = empty($cid) ? null : Game::get_sevid_club($cid);
		parent::__construct($hid,$cid,$serverID);
	}
	
	
	/**
	 * 添加贡献成员
	 * @param unknown_type $fuid
	 * @param unknown_type $dcid
	 */
	public function add_gx_list($fuid,$dcid, $gxMax = 20){
		//判断是否已达贡献人数上限
		$Act40Model = Master::getAct40($fuid);
		$cid = $Act40Model->info['cid']; //联盟id
		if(empty($cid)){
			Master::error(CLUB_NO_HAVE_JOIN);
		}
		//判断是否已贡献
		if(!empty($this->info[$fuid][$dcid]) && $this->info[$fuid][$dcid] >= $gxMax){
			Master::error(CLUB_TODAY_BUILDED);
		}
		
		$ClubModel = Master::getClub($cid);
		$cfg_club_id = Game::getcfg_info('club',$ClubModel->info['level']);
		$maxMember = empty($cfg_club_id['maxMember'])?0:$cfg_club_id['maxMember'];
		if( count($this->info) > $maxMember ){
			Master::error(CLUB_BUILD_PERSON_TO_MAX);
		}

		if (!isset($this->info[$fuid][$dcid])) {
			$this->info[$fuid][$dcid] = 0;
		}

		$this->info[$fuid][$dcid]++;
		$this->save();
		
		//删除缓存
		//$ClubModel->delete_cache();
	}
	
	
}
