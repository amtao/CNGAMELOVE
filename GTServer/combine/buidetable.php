<?php

require_once dirname( __FILE__ ) . '/../public/common.inc.php';
$AUTO_INCREMENT_START = 10086;

//服务器ID
$sevid = intval($_SERVER['argv'][1]);

if (empty($sevid)){
    exit('错误啦!!!!!!!!!');
}
$SevidCfg = Common::getSevidCfg($sevid);
if($SevidCfg['sevid'] == $SevidCfg['he']){
    $db = Common::getMyDb();
    //联盟姓名表
    $sqls[] = "CREATE TABLE IF NOT EXISTS `club_name` (
        `name` varchar(64) COMMENT '名字',
        `cid` bigint(64)  NOT NULL COMMENT 'cid',
        PRIMARY KEY  `index_name` (`name`),
        UNIQUE INDEX `cid` (`cid`)
    )
    ENGINE=InnoDB DEFAULT CHARSET=utf8";
    foreach ($sqls as $sql){
        $rt = $db->query($sql);
        if (empty($rt)){
            echo $sql;
        }
        echo $rt;
    }
}
