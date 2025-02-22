<?php
require_once "ActBaseModel.php";
/*
 * 新手引导步骤保存
 */
class Act32Model extends ActBaseModel
{
	public $atype = 32;//活动编号
	
	public $comment = "新手引导";
	public $b_mol = "user";//返回信息 所在模块
	public $b_ctrl = "guide";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		'gnew' => 0, //新手剧情引导步骤
		'smap' => 0, //小地图引导步骤
		'mmap' => 0, //中地图引导步骤
		'bmap' => 0, //大地图引导步骤
	);
	
	/*
	 * 步骤保存
	 */
	public function set_guide($params)
	{
		if (empty($params)){
			return;
		}
		foreach ($params as $k => $v){
			if (isset($this->_init[$k])){
				$this->info[$k] = max(intval($v),$this->info[$k]);
                //咸鱼日志
                Common::loadModel('XianYuLogModel');
                XianYuLogModel::tutorial($this->uid, $this->info[$k]);
			}
		}
		$this->save();
	}
	
	/*
	 * 一年后，你考取功名荣归故里任职道府大使，官职从九品！
	 * 平民  升 从九品
	 */
	public function up_guan()
	{
		$UserModel = Master::getUser($this->uid);
		$UserModel->add_sth('level',1);
		if($UserModel->info['level'] > 1){
			Master::error(USER_POSITION_UP);
		}
        //咸鱼日志
        Common::loadModel('XianYuLogModel');
		XianYuLogModel::rolelevel($UserModel->info['platform'], $this->uid, $UserModel->info['level']);
		
		Common::loadModel('XianYuNewLogModel');
		XianYuNewLogModel::InsertGameCharacterlevelDot($this->uid,1);
	}
}
















