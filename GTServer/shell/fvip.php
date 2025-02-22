<?php 
/**
 * 删档返利
 * 
 */
set_time_limit(0);
ini_set('memory_limit','3000M');
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
Common::loadModel("Master");
Common::loadModel("ClubModel");
$serverList = ServerModel::getServList();

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;


if ( is_array($serverList) ) {

    $all_order = array();

	foreach ($serverList as $k => $v) {
		if ( empty($v) ) {
			continue;
		}
		
		if(!empty($serverID) && $serverID != $v['id']){
			continue;
		}
		
		$Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID
		
		echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;
		
		if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {
			echo PHP_EOL, '>>>跳过', PHP_EOL;
			continue;
		}
		
		if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
			&& $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
			echo PHP_EOL, '>>>从服跳过', PHP_EOL;
			continue;
		}

        $db = Common::getDbBySevId($Sev_Cfg['sevid']);

        //记录成功的  包括后台福利充值
        $sql_order = ' select * from `t_order` where `ptime` > 0';
        $res = $db->fetchArray($sql_order);
        if(empty($res)){
            echo "无充值记录\n";
            continue;
        }

        foreach ($res as $rek => $rev){
            if(empty($all_order[$rev['openid']])){
                $all_order[$rev['openid']] = "";
            }
            $all_order[$rev['openid']] .= $rev['money']."|";
        }
	}

	//对主服(1服或者999服)进行创建表/数据插入操作
    $db = Common::getComDb();
	if(!empty($all_order)){
        //创建SQL表
        $table = 'fvip';

        //删除sql表
        $sql = "drop table if exists `{$table}`";
        $db->query($sql);

        //新创建sql表
        $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
                    `openid` varchar(64) NOT NULL DEFAULT '' ,
                    `money` text DEFAULT '' COMMENT '实际充值金额, 精确到分',
                    `uid` bigint(64) DEFAULT 0,
                    UNIQUE KEY `idx_uid` (`openid`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='删档返利表';";

        $rt = $db->query($sql);
        if (empty($rt)){
            echo $sql;
            echo "创建SQL表失败\n";
        }else{
            echo $rt;
            echo "创建SQL表成功\n";
        }

        //插入数据
        foreach ( $all_order as $openid => $money){
            $sql = "insert into `{$table}` set `openid`='{$openid}', `money`='{$money}'";
            $db->query($sql);
            echo $openid.': '.$money.'   __success'."\n";
        }

    }else{
        echo "返利失败\n";
    }

}


Master::click_destroy();


echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();











