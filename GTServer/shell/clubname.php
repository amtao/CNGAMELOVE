<?php
/**
 * 年卡订正脚本
 *
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ClubModel');
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$serverList = ServerModel::getServList();
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
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
        if($open_day <= 0){
            continue;
        }
         echo '生效时间'.$open_day."\n";
        name();
    }
}

exit();


function name(){
    $sql = "select `name`,`cid` from `club`;";
    $db = Common::getMyDb();
    $data = $db->fetchArray($sql);
    foreach ($data as $value){
        //特殊字符验证
        $heimingdan = array("\t","\\n","\n","\r","\f","/","'","\\","\"");
        foreach ($heimingdan as $v){
            $res = strstr($value['name'],$v);
            if($res){
                echo 'name:'.$value['name'],"          CID:",$value['cid'],PHP_EOL;
                break;
            }
        }

        //验证
        $tmpStr = json_encode($value['name']); //暴露出unicode

        if(preg_match('/\\\u[ed]{1}[0-9a-f]{3}/', $tmpStr)){//含有emoji
            echo 'name:'.$value['name'],"           CID:",$value['cid'],PHP_EOL;
        }
    }
}