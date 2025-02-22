<?php
/*
 * 联盟-申请列表
 */
require_once "SevBaseModel.php";
class Sev11Model extends SevBaseModel
{
	public $comment = "联盟-申请列表";
	public $act = 11;//活动标签
	
	public $_init = array(//初始化数据
		/*
		     fuid => aray(
		       cid =>　time　　//三天没了申请操作  就清除申请记录
		     );
		 */
	);
	
	public function __construct($act){
		parent::__construct($act);
	}
	
	
	/**
	 * 添加申请
	 * @param unknown_type $fuid  玩家id
	 * @param unknown_type $cid  公会
	 */
	public function add_apply($fuid,$cid){
		
		$ClubModel = Master::getClub($cid);
		$cfg_club_id = Game::getcfg_info('club',$ClubModel->info['level']);
		$maxMember = empty($cfg_club_id['maxMember'])?0:$cfg_club_id['maxMember'];
		if( count($ClubModel->info['members']) >= $maxMember ){
			Master::error(CLUB_PERSON_TO_MAX);
		}
		if(!empty($this->info[$fuid][$cid])){
			Master::error(CLUB_APPLYED);
		}
		$this->info[$fuid][$cid] = $_SERVER['REQUEST_TIME'];
		
		$this->save();
	}
	
	/**
	 * 删除申请  -->  指定玩家的
	 * @param $fuid 玩家id
	 * @param $cid  公会id   指定公会  0:标识删除该玩家的全部申请信息 ;  >0 标识删除指定玩家指定公会信息
	 */
	public function del_apply_user($fuid,$cid){
		//删除全部信息
		if( empty($cid) ){
			unset($this->info[$fuid]);
		}else{
			$this->info[$fuid][$cid] = 0;
		}
		$this->save();
	}
	
	/**
	 * 删除该公会的所有申请     
	 * 删除申请  -->  指定公会的
	 * @param unknown_type $cid
	 */
	public function del_apply_club($cid){
		foreach($this->info as $k => $v ){
			if(empty($v)){
				unset($this->info[$k]);
			}
			if(!empty($v[$cid])){
				unset($this->info[$k][$cid]);
			}
		}
		$this->save();
	}
	
	
	
	/**
	 * 获取某个公会的申请列表,申请列表
	 * @param unknown_type $cid
	 */
	public function apply_list($cid){
		$list = array();
		$flag = 0;  //是否进行保存操作   1:保存   0:不保存
		
		if(!empty($this->info)){
			//清除过期数据
			foreach($this->info as $kfuid => $vinfo ){
				foreach($vinfo as $k => $v ){
					//三天没了申请操作  就清除申请记录
					if($_SERVER['REQUEST_TIME'] - $v > 86400*3 ){
						unset($this->info[$kfuid][$k]);
						$flag = 1;
						continue;
					}
					if($cid != $k){ //
						continue;
					}
					$fuidData = Master::fuidData($kfuid);
					$list[] = array(
						'id' => $kfuid,
						'name' => $fuidData['name'],
						'sex' => $fuidData['sex'],
						'job' => $fuidData['job'],
						'level' => $fuidData['level'],
						'shili' => $fuidData['shili'],
						'chenghao' => $fuidData['chenghao'],
						'headavatar' => $fuidData['headavatar'],
					);
				}
			}
		}
		
		//保存
		if($flag == 1){
			$this->save();
		}
		return $list;
	}	
}


