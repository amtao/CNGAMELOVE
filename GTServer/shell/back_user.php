<?php
/**
 * 删档返回普通玩家奖励
 */
set_time_limit(0);
ini_set('memory_limit','3000M');
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
Common::loadModel("Master");
$serverList = ServerModel::getServList();

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;


if ( is_array($serverList) ) {

    $all = array();

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
        $sql_order = 'select * from `gm_sharding`';
        $res = $db->fetchArray($sql_order);
        if(empty($res)){
            echo "暂无参与用户\n";
            continue;
        }
        foreach ($res as $val){
            if(empty($val['ustr']) || isset($all[$val['ustr']])){
                continue;
            }
            $all[$val['ustr']] = 1;
        }
    }
}


$db = Common::getComDb();
//创建SQL表
$table = 'delete_back';
//删除sql表
$sql = "drop table if exists `{$table}`";
$db->query($sql);

//新创建sql表
$sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
                    `openid` varchar(64) NOT NULL DEFAULT '' ,
                    `uid` bigint(64) DEFAULT 0,
                    UNIQUE KEY `idx_uid` (`openid`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='删档回馈表';";
$rt = $db->query($sql);
if (empty($rt)){
    echo $sql;
    echo "创建SQL表失败\n";
}else{
    echo $rt;
    echo "创建SQL表成功\n";
}
//插入数据
if(!empty($all)){
    foreach ( $all as $openid => $val){
        $sql = "insert into `{$table}` set `openid`='{$openid}'";
        $db->query($sql);
        echo $openid.'__success'."\n";
    }
}

echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();











