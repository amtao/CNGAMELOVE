<?php
/**
 * 后台配置文件脚本
 * 调用方式：每分钟跑一次
 *
 */
set_time_limit(0);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区

$serverList = ServerModel::getServList();
$actTypeList = array(6140, 14, 8);

$startDate = date('Y-m-d H:00:00', time());
$endTime = strtotime($startDate);
$startTime = $endTime - 3600;
$minute = date('i');
$btime = microtime(true);

echo PHP_EOL, '当前时间:'.$startDate."	endTime:".$endTime."     minute:".$minute, PHP_EOL;

if ( is_array($serverList) ) {

	foreach ($serverList as $k => $v) {

		if ( empty($v) ) {
			continue;
		}
		$Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID

		echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

		if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {
			echo PHP_EOL, '>>>跳过', PHP_EOL;
			continue;
		}
		if ( 0 < $serverID && $serverID != $Sev_Cfg['sevid'] ) {
			echo PHP_EOL, '>>>跳过', PHP_EOL;
			continue;
		}

		if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
			&& $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
			echo PHP_EOL, '>>>从服跳过', PHP_EOL;
			continue;
		}

		$open_day = ServerModel::isOpen($Sev_Cfg['sevid']);
		//过滤未开服的
		if($open_day <= 0 || ($i % 60) != $minute ){
			continue;
		}
		echo $i."_开始跑批! \n";

		foreach ($actTypeList as $key => $type) {
			get_act_item_log($Sev_Cfg['sevid'], $type);
		}
	}
}

echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();

/**
 * 获取活动生效详细信息
 */
function get_act_item_log($sevid, $type){
	global $endTime;
	global $startTime;

	$SevidCfg = Common::getSevidCfg($sevid);
    $db = Common::getDftDb("flow");

    $table_div = Common::get_table_div();
    $sqls = array();
    $isTrue = true;
    for ($i = 0 ; $i < $table_div ; $i++)
    {
        //门客表
        $table = 'flow_event_'.Common::computeTableId($i);
        $table1 = 'flow_records_'.Common::computeTableId($i);
        $sqls[] = "SELECT A.`itemid` FROM ".$table1." as A left join ".$table." as B on A.flowid = B.id WHERE A.`type` = ".$type." AND B.`ftime` >= ".$startTime." AND B.`ftime` < ".$endTime;
    }

    $info = array();
    foreach ($sqls as $sql){
        $rt = $db->query($sql);
        while($row = mysql_fetch_assoc($rt)){

            $itemid = $row["itemid"];
            if (isset($info[$itemid])) {
                $info[$itemid] += 1;
            }else{
                $info[$itemid] = 1;
            }
        }
    }

    if (!empty($info)) {
    	$insertSql = "INSERT INTO `act_item_log` (`actid`, `itemid`, `num`, `ftime`) VALUES ";
	    foreach ($info as $k => $v) {
	    	$insertSql .= "('{$type}','{$k}','{$v}','{$startTime}'),";
	    }
	    $insertSql = rtrim($insertSql, ',');
	    $rt = $db->query($insertSql);
	    if (empty($rt)) {
	        echo '插入新的sql失败:'.$insertSql, PHP_EOL;
	    } else {
	        echo '插入新的sql成功', PHP_EOL;
	    }
    }
}