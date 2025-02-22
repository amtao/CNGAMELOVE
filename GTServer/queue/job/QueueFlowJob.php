<?php
class QueueFlowJob
{
    /**
     * 流水入库
     */
    public function perform()
    {
        fwrite(STDOUT, 'flow insert started'.PHP_EOL);
        //$this->args;
        $serID = $this->args['serID'];
        $uid = $this->args['uid'];
        $e = $this->args['e'];
        $r = $this->args['r'];

        Common::getSevidCfg($serID);
        $db = Common::getDbBySevId($serID,'flow');
        //插入事件表
        $eventTable = 'flow_event_'.Common::computeTableId($uid);
        $sql = "INSERT INTO {$eventTable} (`uid`, `model`, `ctrl`, `params`,`ftime`, `ip`) 
			VALUES ({$uid}, '{$e['m']}', '{$e['c']}', '{$e['p']}','{$e['ft']}', '{$e['ip']}');";
        if (!$db->query($sql)){
            fwrite(STDOUT, 'flow insert event error!' . PHP_EOL);
            return false;//报警
        }
        //事件流水ID
        $flowid = $db->insertId();
        //插入 详细记录表
        $recordTable = 'flow_records_'.Common::computeTableId($uid);
        $sql = "INSERT INTO ".$recordTable." (`flowid`, `type`, `itemid`, `cha`, `next`) VALUES ";
        $values = array();
        foreach ($r as $key => $v){
            $values[] = "({$flowid}, {$v['type']}, '{$v['itemid']}', {$v['cha']}, {$v['next']})";
        }
        $sql .= implode(",", $values).";";
        if (!$db->query($sql)) {
            fwrite(STDOUT, 'flow insert records error!' . PHP_EOL);
            return false;//报警
        }

        fwrite(STDOUT, 'flow insert ended!' . PHP_EOL);
        return true;
    }
}