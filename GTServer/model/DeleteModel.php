<?php
class DeleteModel 
{
    /**
     * 删除7天前的邮件
     */
	public static function mailDelete($serverid)
	{
		$sqls = array();
		$table_div = Common::get_table_div($serverid);
		$user = array();
		$db = Common::getDbBySevId($serverid);
		for ($i = 0 ; $i < $table_div ; $i++){
			$table = "mail_".Common::computeTableId($i); 
			$weektime = strtotime(date('Ymd',time())) - DELETE_MAIL_LIMIT_DAY*86400;
			
			$user_sql = "SELECT uid FROM `{$table}` WHERE (`rts`>0 and `rts`<{$weektime}) and (`isdel`=1 or `isdel`=2) GROUP BY uid";
			$userData = $db->fetchArray($user_sql);
			if(!empty($userData)){
			    $user = array_merge($user,$userData);
			}
			
			$sqls[] = "delete from `{$table}` where (`rts`>0 and `rts`<{$weektime}) and (`isdel`=1 or `isdel`=2)";
			$sqls[] = "alter table `{$table}` engine=innodb";// 修改数据库引擎，释放空间
		}
		foreach ($sqls as $k => $sql){
			$count = $db->query($sql);
			if($k == 0){
			    echo $serverid.'删除邮件已运行';
			}
		}
		$memdb = Common::getCacheBySevId($serverid);
		if(!empty($user)){
		    foreach ($user as $u){
		        $memdb->delete($u['uid'].'_mail');
		    }
		}
		
	}
	/*
	 * 删除过期或者已删除的兑换码
	 * */
	public static function acodeDetail($serverid){
	    
	    Common::loadModel('AcodeTypeModel');
	    $AcodeTypeModel = new AcodeTypeModel();
	    $data = $AcodeTypeModel->getAllvalue();
	    if(!empty($data)){
	        $del_arr = array();
	        foreach ($data as $key => $val){
	            if($val['isdel'] == 1){
	                $del_arr[] = $key;
	                unset($data[$key]);
	            }else if($val['eTime'] < Game::get_now()){
	                $del_arr[] = $key;
	                unset($data[$key]);
	            }
	        }
	        if(!empty($del_arr)){
	            $AcodeTypeModel->updateAllData($data);//先删除key信息
	            //删除cdkey
	            $str = "('".implode("','",$del_arr)."')";
	            $sqls[] = "delete from `acode` where `act_key` in {$str}";
	            $sqls[] = "alter table `acode` engine=innodb";// 修改数据库引擎，释放空间
	            $db = Common::getDbBySevId($serverid);
	            foreach ($sqls as $k => $sql){
	                $bool = $db->query($sql);
	                if($bool === true){
	                    echo '删除兑换码已运行';
	                }
	            }
	        }
	    }
	}
	
	/*
	 * 删除封设备记录
	 * */
	public static function fengsbDetail() {
	    $limit_day = 15;

		$Redis9Model = Master::getRedis9();
		$del_info = $Redis9Model->delRandData(0,Game::get_now()-$limit_day*24*3600);
		if($del_info){
			echo '封设备删除成功',PHP_EOL;
		}else{
			echo '没有可以删除的数据',PHP_EOL;
		}
	}

	/**
	 * @param $serverid
	 */
	public static function flowDelete($serverid)
	{
		$sqls = array();
		$table_div = Common::get_table_div($serverid);
		$user = array();
		$db = Common::getDbBySevId($serverid, 'flow');
		for ($i = 0 ; $i < $table_div ; $i++){
			$table = "flow_event_".Common::computeTableId($i);
			$recordsTable = "flow_record_".Common::computeTableId($i);
			$time = strtotime(date('Ymd',time())) - 90*86400;

			$sql = "SELECT `id` FROM `{$table}` WHERE `ftime`<{$time} ORDER BY `id` DESC LIMIT 1;";
			$data = $db->fetchRow($sql);
			if (!empty($data)){
				$sqls[] = "delete from `{$table}` where `ftime`<{$time} ";
				$sqls[] = "delete from `{$recordsTable}` where `flowid`<={$data['id']} ";
				$sqls[] = "alter table `{$table}` engine=innodb";// 修改数据库引擎，释放空间
			}
		}
		foreach ($sqls as $k => $sql){
			$count = $db->query($sql);
			if($k == 0){
				echo $serverid.'删除完成';
			}
		}
	}
}
