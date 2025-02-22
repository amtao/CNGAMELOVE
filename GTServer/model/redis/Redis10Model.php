<?php
require_once "RedisBaseModel.php";
/*
 * 公会排行
 */
class Redis10Model extends RedisBaseModel
{
	public $comment = "公会排行";
	public $act = 'club';//活动标签
	
	public $b_mol = "club";//返回信息 所在模块
	public $b_ctrl = "clubList";//返回信息 所在控制器

    public $out_time = 600;//输出缓存过期时间
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(
	/*
		联盟id => 联盟总经验
	*/
	);
	
	
	/**
	 * 获取单个联盟的信息
	 * @param $member  联盟id
	 * @param $rid   排名id
	 */
	public function getMember($member,$rid){
		
		$ClubModel = Master::getClub($member);
		//获取公共基础信息
		$base = $ClubModel->getSimple();
		
		$cinfo = array();
		$cinfo['id'] = $base['id']; //联盟id
		$cinfo['name'] = $base['name']; //联盟名字
		$cinfo['level'] = $base['level']; //等级
		$cinfo['exp'] = $base['exp']; //联盟总经验
		$cinfo['fund'] = $base['fund']; //财富值
		$cinfo['qq'] = $base['qq']; //QQ
		$cinfo['laoma'] = $base['laoma']; //微信  去掉
		$cinfo['outmsg'] = $base['outmsg']; //微信
		$cinfo['rid'] = $rid; //联盟排名
		$cinfo['allShiLi'] = 0; //总势力
        $cinfo['isJoin'] = $base['isJoin']; //是否加入
        $cinfo['num'] = count($base['members']); //宫殿人数
		foreach($base['members'] as $info){
			$cinfo['allShiLi'] += $info['shili'];
			$cinfo['members'][] = array(
				'name' => $info['name'],  //名字
				'post' => $info['post'],  //职位
			);

            unset($info);
		}

        unset($ClubModel, $base);
		return $cinfo;
	}
	
	
	/*
	 * 返回排行信息
	 */
	public function back_data_my($member){
		$cName = '无';
		$rid = 0;
		if(!empty($member)){
			$rid = parent::get_rank_id($member);
			$ClubModel = Master::getClub($member);
			$cName  = $ClubModel->info['name'];
		}
		Master::back_data(0,$this->b_mol,"myClubRid",array("cName"=>$cName,"cRid"=>$rid));
	}
	
	
}

/*
 * 通用工会 势力 排行榜
 */
