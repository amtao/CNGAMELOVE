<?php
//活动数据表
require_once "AModel.php";
class ActModel extends AModel
{
	public $_key = "_act";
    protected $_actType = null;
    protected $_syn_w = true;
	
	//各种活动初始化信息
	static public $_init = array(
		
		12 => array(//红颜精力恢复
			'num' => 0,//精力值
			'time' => 0,//上次恢复时间
		),
	);
	public function __construct($uid, $actType)
	{
        $preKey = $uid.$this->_key;
	    $this->_actType = $actType;
	    $this->_key = $this->_key .'_'. $actType;
		parent::__construct($uid);
		$cache = $this->_getCache();

        //把之前缓存结构的数据迁移到新缓存结构数据里
        $preCacheInfo = $cache->get($preKey);
        if (!empty($preCacheInfo)) {
            foreach ($preCacheInfo as $preActType => $preValue) {
                $cache->set($preKey.'_'.$preActType, $preValue);
            }
            $cache->delete($preKey);
        }
        //把之前缓存结构的数据迁移到新缓存结构数据里end

		$this->info = $cache->get($this->getKey());
		$table = 'act_'.Common::computeTableId($this->uid);
		if($this->info === false){
			$sql = "select * from `{$table}` where `uid`='{$this->uid}' and `actid`={$actType};";
    		$db = $this->_getDb();
			if (empty($db)){
				return false;
			}
			$data = $db->fetchRow($sql);
			if($data == false) {
				$this->info = array();
                $cache->set($this->getKey(),$this->info);
				return false;
			}
            if (!empty($data['tjson'])){
                $data['tjson'] = json_decode($data['tjson'],true);
            }
			$this->info = $data;
			$cache->set($this->getKey(),$this->info);
		}
		return true;
	}
	
	/*
	 * 获取某个活动的活动信息
	 * 1:经营统计信息
	 * 2:成就统计信息
	 */
	public function getAct($type)
	{
		return $this->info['tjson'];
	}
	
	/*
	 * 设置一个活动信息
	 */
	public function setAct($type,$data)
	{
		$a_update = array(
			'actid' => $type,
			'tjson' => $data,
		);
		$this->update($a_update);
		return true;
	}
	
	/*
	 * 更新
	 * 'actid' => $type,
	 * 'tjson' => $data,
	 */
	public function update($data)
	{
		if (!isset($data['actid'])){
			exit ('update_act_actid_null');
		}
        $info = array(
            'uid'   => $this->uid,
            'actid' => $data['actid'],
            'tjson' => $data['tjson'],
        );
		if (!empty($this->info)){//存在 则更新
			$info['_update'] = true;
		}else{
                if(in_array($this->_actType,array(34,44))){
				$tjson = json_encode($info['tjson'],JSON_UNESCAPED_UNICODE);
			}else{
				$tjson = json_encode($info['tjson']);
			}
			//插入数据库
			$table = 'act_'.Common::computeTableId($this->uid);
			$sql = "insert into `{$table}` set 
			`uid`='{$this->uid}',
			`actid`='{$data['actid']}',
			`tjson`='{$tjson}'";
			$db = $this->_getDb();
			$db->query($sql);
		}
		$this->info = $info;
		$this->_update = true;
	}
	
	
	//------------各种活动逻辑----------//


	/*
	 */
	public function sync()
	{
        if (!empty($this->info['_update'])){
            $this->info['_update'] = false;
            $table = 'act_'.Common::computeTableId($this->uid);
			if(in_array($this->_actType,array(34))){
				$tjson = json_encode($this->info['tjson'],JSON_UNESCAPED_UNICODE);
			}else{
				$tjson = json_encode($this->info['tjson']);
			}
            $sql=<<<SQL
update `{$table}` 
set `tjson`	='{$tjson}'
where `uid` ='{$this->uid}' and `actid` = '{$this->_actType}' limit 1;
SQL;
            $db = $this->_getDb();
            $db->query($sql);
        }
		return true;
	}
	public static function getAllInfo($uid)
    {
        Common::loadActModel('ActBaseModel');
        $info = array();
        foreach (ActBaseModel::$rightActTypes as $actType => $actComment) {
            $ActModel = new ActModel($uid, $actType);
            if (empty($ActModel->info)) {continue;}
            $info[$actType] = $ActModel->info;
        }
        return $info;
    }
}
