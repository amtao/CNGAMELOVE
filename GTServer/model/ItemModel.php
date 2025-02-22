<?php
//用户
require_once "AModel.php";
class ItemModel extends AModel
{
	public $_key = "_item";
	public function __construct($uid)
	{
		parent::__construct($uid);
		$cache = $this->_getCache();
		$this->info = $cache->get($this->getKey());
		$table = 'item_'.Common::computeTableId($this->uid);
		if($this->info == false){
			$sql = "select * from `{$table}` where `uid`='{$this->uid}'";
    		$db = $this->_getDb();
			if (empty($db)){
				return false;
			}
			$data = $db->fetchArray($sql);
			if($data == false) {
				$this->info = array();
				return;
			}
			$info = array();
			foreach ($data as $v)
			{
				$info[$v['itemid']] = $v;
			}
			$this->info = $info;
			$cache->set($this->getKey(),$this->info);
		}
	}
	
	public function getBase()
	{
		$info_base = array();
		foreach ($this->info as $k=>$v)
		{
			if($v['count'] > 0){
				$info_base[] = $this->getBase_buyid($k);
			}
		}
		Master::back_data($this->uid,'item','itemList',$info_base);
	}
	
	public function getBase_buyid($itemid)
	{
		$info = $this->info[$itemid];
		$data = array(
				'id' => $itemid,
 				'count' => $info['count'],//数量
		);
		return $data;
	}
	public function get_item_count($itemid){
		if (empty($this->info[$itemid])){

			return 0;
		}
		return $this->info[$itemid]['count'];
	}
	/*
	 * 减去道具
	 */
	public function sub_item($itemid,$count,$is_click = false)
	{
		if (empty($this->info[$itemid]) 
		|| $this->info[$itemid]['count'] < $count
		|| $count < 0
		|| empty($count)) {
			if ($is_click){
				return false;
			}
			Master::error(ITEMS_NUMBER_SHORT);
		}

		//百里传音使用判断
		if($this->info[$itemid]['itemid']=='1260'){
            //禁言判断
            $Sev23Model = Master::getSev23();
            $bool = $Sev23Model->isBanTalk($this->uid);
            if(!empty($bool)){
                    Master::error(DISABLE_SEND_MSG,$itemid);
            }
        }

		//如果单纯检查
		if ($is_click){
			return true;
		}
		
		$i_update = array(
			'itemid' => $itemid,
			'count' => -$count
		);
		$this->update($i_update);
		
		//限时活动
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->xianshi_huodong($itemid,$count);
					
        //咸鱼日志
        Common::loadModel('XianYuLogModel');
		XianYuLogModel::item($this->uid, $itemid, $this->info[$itemid]['count'], $count, '扣除普通道具');
		
		return true;
	}
	
	/*
	 * 加上道具
	 */
	public function add_item($itemid,$count)
	{
		$_itemid = intval($itemid);
		if (empty($_itemid)){
			Master::error("add_item_id_err_".$itemid);
		}
		
		$i_update = array(
			'itemid' => $_itemid,
			'count' => $count
		);
		$this->update($i_update);

	}
	
	/*
	 * 更新
	 */
	public function update($data)
	{
		if (!isset($data['itemid'])){
			exit ('update_ItemModel_itemid_null');
		}
		if (isset($this->info[$data['itemid']])){//存在 则更新
			$info = $this->info[$data['itemid']];
			//更新
			if (isset($data['count'])) { $info['count'] += $data['count']; }
			$info['_update'] = true;
		}else{
			//新建
			$info = array();
			$info['count'] = isset($data['count'])?$data['count']:1;
			$info['itemid'] = $data['itemid'];
			$info['uid'] = $this->uid;
			//插入数据库
			$table = 'item_'.Common::computeTableId($this->uid);
			$sql = "insert into `{$table}` set 
			`uid`='{$this->uid}',
			`itemid`='{$data['itemid']}',
			`count`='{$info['count']}'";
			$db = $this->_getDb();
			$db->query($sql);
		}
		$this->info[$data['itemid']] = $info;
		$this->_update = true;

		//返回更新信息
		$h_info = $this->getBase_buyid($data['itemid']);
		Master::back_data($this->uid,'item','itemList',array($h_info),true);

        //记录流水 ($type,$itemid,$cha,$next)
        Game::cmd_flow(6,$data['itemid'],$data['count'],$this->info[$data['itemid']]['count']);
	}
	
	/*
	 */
	public function sync()
	{
		if (!is_array($this->info)) return;
		$table = 'item_'.Common::computeTableId($this->uid);
		$db = $this->_getDb();
		foreach ($this->info as $k=>$v){
			if (!empty($v['_update'])){
				$this->info[$k]['_update'] = false;
				$sql=<<<SQL
update  
       `{$table}` 
   set
	`count`	='{$v['count']}'
where
	`uid` ='{$this->uid}'
	and
	`itemid` = '{$k}'
limit   1;
SQL;
				$flag = $db->query($sql);
				if(!$flag){
					Master::error('db error item_'.$sql);
				}
			}
		}
		return true;
	}
}
