<?php
// 操作日志
class OperateLogModel {
	
	public static function flowAdmin($data) {
		$db = Common::getMyDb();
    	$data = array_map('mysql_real_escape_string', $data);
		$ctime = time();
		$curUser = trim($_SESSION['CURRENT_USER']);
		$ip = Common::GetIP();
		$type = strtolower(trim($data['type']));
    	
		$insert = "insert into `flow_admin` 
			(`uid`, `pre`, `cha`, `next`, `type`, `optype`, `user`, `time`, `ip`)
			values ('{$data['uid']}', '{$data['pre']}', '{$data['cha']}', '{$data['next']}',
			'{$type}', '{$data['optype']}', '{$curUser}', '{$ctime}', '{$ip}')";
		$db->query($insert);
		return ;	
	}
}