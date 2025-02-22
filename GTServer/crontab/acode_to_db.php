<?php 
/**
 * 兑换码基础配置转移到数据库
 * 调用方式：手动运行 只在一区运行
 * 
 */
set_time_limit(0);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区

$serverList = ServerModel::getServList();

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

Common::loadModel('AcodeTypeModel');
$AcodeTypeModel = new AcodeTypeModel();
$serverid = ServerModel::getDefaultServerId();//默认服务器id
Common::getSevidCfg($serverid);
$db = Common::getDbBySevId($serverid);
//通服基础配置
$allcfg = HoutaiModel::read_all_peizhi('recode');
if(!empty($allcfg)){
    $allcfg = json_decode($allcfg,true);
    if(!empty($allcfg)){
        foreach ($allcfg as $key => $val){
            echo $key,PHP_EOL;
            $sql = "select `type` from `acode` where `act_key` = '{$key}' limit 1";
            $info = $db->fetchRow($sql);
            if(empty($info)) continue;
            if($val['num'] == 1 && $info['type'] != 3){//不是通用兑换码
                continue;
            }
            $data = array(
                'act_key' => $key,
                'name' => $val['name'],
                'type' => $info['type'],
                'sever' => 'all',
                'num' => $val['num'],
                'sTime' => 1483200000,//之前没有 给个今年的1.1
                'eTime' => strtotime($val['etime']),
                'items' => $val['items']
            );
            $AcodeTypeModel->add($data);
        }
    }
}
//通服基础配置end

//单服基础配置
if ( is_array($serverList) ) {
    foreach ($serverList as $k => $v) {
        if (empty($v)) {
            continue;
        }
        $Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID

        echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

        if (!(defined('IS_TEST_SERVER') && IS_TEST_SERVER) && 999 == $Sev_Cfg['sevid']) {
            echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }

        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
            && $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID
        ) {
            echo PHP_EOL, '>>>从服跳过', PHP_EOL;
            continue;
        }

        $allcfg = HoutaiModel::read_base_peizhi('recode');
        $allcfg = json_decode($allcfg,true);
        if(!empty($allcfg)){
            foreach ($allcfg as $key => $val){
                $sql = "select `type` from `acode` where `act_key` = '{$key}' limit 1";
                $info = $db->fetchRow($sql);
                if(empty($info)) continue;
                if($val['num'] == 1 && $info['type'] != 3){//不是通用兑换码
                    continue;
                }
                
                $data = array(
                    'act_key' => $key,
                    'name' => $val['name'],
                    'type' => $info['type'],
                    'sever' => '1000'.str_pad($val['serverid'],3,0,STR_PAD_LEFT),
                    'num' => $val['num'],
                    'sTime' => 1483200000,//之前没有 给个今年的1.1
                    'eTime' => strtotime($val['etime']),
                    'items' => $val['items']
                );
                $AcodeTypeModel->add($data);
            }
        }
        //单服基础配置end
    }
}


//单服活动配置end

echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();