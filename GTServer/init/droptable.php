<?php

require_once dirname( __FILE__ ) . '/../public/common.inc.php';
$AUTO_INCREMENT_START = 10086;

//服务器ID
$sevid = intval($_SERVER['argv'][1]);

if (empty($sevid)){
    exit('错误啦!!!!!!!!!');
}
$SevidCfg = Common::getSevidCfg($sevid);
echo PHP_EOL . 'init,server id = ' . $SevidCfg['sevid'] . PHP_EOL;
if( $SevidCfg['sevid'] <> 999 ){
	$AUTO_INCREMENT_START = $SevidCfg['sevid']  * 1000000;
}

//公会自增id
$CLUB_AUTO_START = 100;
if( $SevidCfg['sevid'] <> 999 ){
	$CLUB_AUTO_START = $SevidCfg['sevid']  * 10000;
}


if ( 0 > $SevidCfg['sevid'] ) {
	exit('SERVER_ID invalid');
}
$db = Common::getMyDb();
$flowDb = Common::getDftDb('flow');
$table_div = Common::get_table_div();
$sqls = array();
$flowSqls = array();
for ($i = 0 ; $i < $table_div ; $i++)
{
	$table = 'user_'.Common::computeTableId($i); //用户表
	$sqls[] = "drop table if exists `{$table}`";
	
	
	//门客表
	$table = 'hero_'.Common::computeTableId($i);
	$sqls[] = "drop table if exists `{$table}`";
	
	
	//后宫表
	$table = 'wife_'.Common::computeTableId($i);
	$sqls[] = "drop table if exists `{$table}`";
	
	
	//子嗣表
	$table = 'son_'.Common::computeTableId($i);
	$sqls[] = "drop table if exists `{$table}`";
	
	
	//道具表
	$table = 'item_'.Common::computeTableId($i);
	$sqls[] = "drop table if exists `{$table}`";
	
	
	//活动数据表
	$table = 'act_'.Common::computeTableId($i);
	$sqls[] = "drop table if exists `{$table}`";
	
	
	//邮件表     邮件类型  mtype 0:无道具列表  1:有道具列表 2:其他
	$table = 'mail_'.Common::computeTableId($i);
	$sqls[] = "drop table if exists `{$table}`";
	

    //流水事件表
    $table = 'flow_event_'.Common::computeTableId($i);
    $flowSqls[] = "drop table if exists `{$table}`";
    
    //流水详情表
    $table = 'flow_record_'.Common::computeTableId($i);
    $flowSqls[] = "drop table if exists `{$table}`";
   
	
}

//活动公共数据表
$sqls[] = "drop table if exists `sev_act`";


//公会
$table = 'club'; //公会表
$sqls[] = "drop table if exists `{$table}`";



//子嗣全服提亲表
$sqls[] = "drop table if exists `son_marry`";


//姓名表
$sqls[] = "drop table if exists  `index_name`";


// 订单表
$sqls[] = "drop table if exists `t_order`";


// 路由表
$sqls[] = "drop table if exists `gm_sharding`";

$sqls[] = "drop table if exists `run`";

if($SevidCfg['sevid'] == 999 || $SevidCfg['sevid'] == 1) {
    //激活码/兑换码表
    $sqls[] = "drop table if exists `acode`";
   

    //注册记录表
    $sqls[] = "drop table if exists `register`";
    

    //登录记录表
    $sqls[] = "drop table if exists `login_log`";
        
}


foreach ($sqls as $sql){
	$rt = $db->query($sql);
	if (empty($rt)){
		echo $sql;
	}
	echo $rt;
}

foreach ($flowSqls as $flowSql){
    $result = $flowDb->query($flowSql);
    if (empty($result)){
        echo $flowSql;
    }
    echo $result;
}

echo PHP_EOL;

