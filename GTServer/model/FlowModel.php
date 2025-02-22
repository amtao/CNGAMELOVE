<?php
//流水类
class FlowModel
{
	public $uid;
    public $model;
    public $ctrl;
    public $params;
    public $_ftime;
    public $_ip;
	public $_records = array();
	
	public $_flowcfg;


	
	/*
	 * 初始化 uid , 协议类型
	 */
	public function __construct($uid, $model, $ctrl, $params, $ip=null)
	{
        //$params json_decode---
		$this->uid = intval($uid);
		$this->model = $model;
		$this->ctrl = $ctrl;
        $this->params = json_encode($params);
		if (empty($ip)){
			$ip = Common::GetIP();
		}
		$this->_ip = $ip;
		$this->_ftime = $_SERVER['REQUEST_TIME'];
		
		//流水配置文件
		$this->_flowcfg =  Game::getcfg('flowConfig');
	}

    /**
     * 添加一条详细流水记录
     * @param $type  流水类型(int)
     * @param $itemid  道具ID / 或其他意思
     * @param $cha    差值
     * @param $next  新的值
     * @return bool
     */
	public function add_record($type, $itemid, $cha, $next){
		if ($this->_flowcfg[$type]['open'] != 1){
			return false;
		}
		$itemid = $itemid?$itemid:0;
		$cha = $cha?$cha:0;
		$next = $next?$next:0;
		$this->_records[] = array(
			'type' => $type,
			'itemid' => $itemid,
			'cha' => $cha,
			'next' => $next,
		);
		return true;
	}
	
	/*
	 * 写入
	 */
	public function destroy()
	{
        if (empty($this->_records)){
            return true;
        }
		//队列类
		Common::loadModel("QMCModel");
		unset($this->_flowcfg);
		QMC::input('flow',$this);
		return true;
	}
	
	/*
	 * 固化入库
	 */
	public static function sync(){
		//队列类
		Common::loadModel("QMCModel");
		$data = QMC::output('flow');
		if (empty($data)){
			return;
		}
		$db = Common::getMyDb('flow');
		if (empty($db)){
			return false;//报警
		}
		//遍历结构体 入库
		foreach ($data as $k => $v){
			//插入事件表
            $eventTable = 'flow_event_'.Common::computeTableId($v->uid);
			$sql = "INSERT INTO ".$eventTable." (`uid`, `model`, `ctrl`, `params`,`ftime`, `ip`) 
			VALUES ({$v->uid}, '{$v->model}', '{$v->ctrl}', '{$v->params}','{$v->_ftime}', '{$v->_ip}')";
			if (!$db->query($sql)){
				return false;//报警
			}
			//事件流水ID
			$flowid = $db->insertId();
			//插入 详细记录表
            $recordTable = 'flow_records_'.Common::computeTableId($v->uid);
			foreach ($v->_records as $key => $v2){
				$sql ="INSERT INTO ".$recordTable." (`flowid`, `type`, `itemid`, `cha`, `next`) 
				VALUES ({$flowid}, {$v2['type']}, '{$v2['itemid']}', {$v2['cha']}, {$v2['next']});";
				if (!$db->query($sql)){
					return false;//报警
				}
			}
		}
		return count($data);
	}

    /*
	 * 写入
	 */
    public function destroy_now($sevid=0){
        if (empty($this->_records)) {
        	return;
        }
		if(empty($sevid)){
			$db = Common::getMyDb('flow');
		}else{
			$db = Common::getDbBySevId($sevid,'flow');
		}

        //插入事件表
        $eventTable = 'flow_event_'.Common::computeTableId($this->uid);
        $sql = "INSERT INTO ".$eventTable." (`uid`, `model`, `ctrl`, `params`,`ftime`, `ip`) 
			VALUES ({$this->uid}, '{$this->model}', '{$this->ctrl}', '{$this->params}','{$this->_ftime}', '{$this->_ip}')";
        if (!$db->query($sql)){
            return false;//报警
        }
        //事件流水ID
        $flowid = $db->insertId();
        //插入 详细记录表
        $recordTable = 'flow_records_'.Common::computeTableId($this->uid);
        $sql = "INSERT INTO ".$recordTable." (`flowid`, `type`, `itemid`, `cha`, `next`) VALUES ";
        $values = array();
        foreach ($this->_records as $key => $v2){
        	$values[] = "({$flowid}, {$v2['type']}, '{$v2['itemid']}', {$v2['cha']}, {$v2['next']})";
        }
        $sql .= implode(",", $values).";";
        if (!$db->query($sql)){
        	return false;//报警
        }
    }

    /**
	 * 聊天流水
     * @param $type
     * @param $uid
     * @param $name
     * @param $vip
     * @param $level
     * @param $content
     * @param $time
     * @param int $other
     * @return bool
     */
    public static function chat_flow($type, $uid, $name, $vip, $level, $content, $time, $other = 1){
        $table = 'flow_chat';
        $uid = intval($uid);
        $type = intval($type);
        $content = trim($content);
        $time = intval($time);
        $sql = "INSERT INTO ".$table." (`uid`, `type`, `name`, `vip`, `level`, `content`, `other`, `time`) VALUES ({$uid}, '{$type}', '{$name}', '{$vip}', '{$level}','{$content}', '{$other}', '{$time}')";
        if ($type == 2){
            $db = Common::getComDb('flow');
        }else{
            $db = Common::getMyDb('flow');
        }
        if (!$db->query($sql)){
            return false;//报警
        }
    }

	/**
	 * 消费统计
	 * @param $uid
	 * @param $num
	 * @param $from
	 * @param int $type
	 * @param int $other
	 * @return bool
	 */
	public static function consume_flow($uid, $num, $from, $type = 1, $other = 1){
		$table = 'flow_consume';
		$ip = $_SERVER["REMOTE_ADDR"];
		$time = time();
		$num = abs($num);
		$sql = "INSERT INTO ".$table." (`uid`, `type`, `num`, `from`, `ip`, `other`, `time`) VALUES ({$uid}, '{$type}', '{$num}', '{$from}', '{$ip}','{$other}', '{$time}')";
		$db = Common::getMyDb('flow');
		if (!$db->query($sql)){
			return false;//报警
		}
	}
}

