<?php

require_once dirname( __FILE__ ) . '/../public/common.inc.php';
$AUTO_INCREMENT_START = 10086;

//服务器ID
$sevid = intval($_SERVER['argv'][1]);

if (empty($sevid)){
    exit('错误啦!!!!!!!!!');
}

$start = $sevid <> 999 ? 1 : $sevid;
$end = $sevid;

for ($sevid = $start; $sevid <= $end; $sevid++) {
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

    $sqls = array();

//公共存储表
    $sqls[] = "CREATE TABLE IF NOT EXISTS `vo_common` (
  `key`  varchar(255) NOT NULL COMMENT '键值',
  `value`  longtext NULL COMMENT '值',
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    foreach ($sqls as $sql){
        $rt = $db->query($sql);
        if (empty($rt)){
            echo $sql;
        }
        echo $rt;
    }
}
echo PHP_EOL;

