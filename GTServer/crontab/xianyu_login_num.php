<?php
//正式服统计登录人数脚本  */5 * * * *  /usr/local/services/php/bin/php /data/www/kingh5hf/s1_kingh5hf/crontab/xianyu_login_num.php 1 > /data/logs/kingh5hf_log/xianyu_login_num 2>&1
exit; //不开启
set_time_limit(0);
$start = microtime(true);
require_once dirname( __FILE__ ) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
$current_time = $_SERVER['REQUEST_TIME'];
$before_time = $current_time - 3600;
echo PHP_EOL, '当前时间 ', date('Y-m-d H:i:s', $current_time), PHP_EOL;
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$SevidCfg = Common::getSevidCfg($serverID);//子服ID
echo PHP_EOL, '服务器ID：', $SevidCfg['sevid'], PHP_EOL;

if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $SevidCfg['sevid'] ) {
	echo PHP_EOL, '>>>跳过', PHP_EOL;
	exit;
}

$sns_login_num = array();
$sns_arr = array();
$pay_cfg_dir = ROOT_DIR . '/public/pay_cfg/';
$pay_cfg_filename_arr = scandir($pay_cfg_dir);
if(!empty($pay_cfg_filename_arr)){
    foreach ($pay_cfg_filename_arr as $filename){
        if(!strstr($filename, '.php')){
            continue;
        }
        if(strstr($filename, 'soeasy') || strstr($filename, 'wxmini')){
            $sns_arr[] = str_replace('.php', '', $filename);
        }
    }
}

$db = Common::getDbBySevId($SevidCfg['sevid']);
$table_div = Common::get_table_div($SevidCfg['sevid']);
if(!empty($sns_arr)){
    foreach ($sns_arr as $item){
        $where = " where `lastlogin`>={$before_time} and `lastlogin`<={$current_time} and `platform`='{$item}' ";
        for ($i = 0 ; $i < $table_div ; $i++){
            $table = '`user_' . Common::computeTableId($i) . '`';
            $sql = "select count(*) as total from {$table} {$where}";
            $userData = $db->fetchArray($sql);
            if(!empty($userData) && $userData[0]['total'] > 0){
                if(isset($sns_login_num[$item])){
                    $sns_login_num[$item] += $userData[0]['total'];
                }else{
                    $sns_login_num[$item] = $userData[0]['total'];
                }
            }
        }
    }
}

//咸鱼日志
Common::loadModel('XianYuLogModel');
if(!empty($sns_login_num)){
    foreach ($sns_login_num as $key=>$val){
        XianYuLogModel::online($key, $SevidCfg['sevid'], $val);
    }
}

echo PHP_EOL, '统计完毕。time=', microtime(true)-$start, PHP_EOL;
exit;
