<?php
/**
 * 帮会战-参战资格列表
 */
require_once "SevKuaCfgBaseModel.php";
class Sev52Model extends SevKuaCfgBaseModel
{
    public $comment = "帮会战-参战资格列表";
    public $act = 52;//活动标签
    public $_server_kua_key = 'clubpk';//指定跨服配置对应的key
    
    
    /*
	 * 初始化结构体
	 */
	public $_init = array(
		//  我方cid => 我方势力
	);
	
	/**
	 * 获取 帮会战-参战资格
	 * @param $cid   帮会id
	 */
	public function add($cid){
		//检测是否已参加参赛资格
		if(!empty($this->info[$cid])){
			return 0;
		}
		//检测是否已匹配
		$Sev50Model = Master::getSev50();
		if($Sev50Model->check_match($cid)){
			return 0;
		}
		//保存参赛资格
		$ClubModel = Master::getClub($cid);
		$allshili = $ClubModel->get_clubshili();
		$this->info[$cid] = $allshili;
		$this->save();
	}
	
}