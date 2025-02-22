<?php 
/**
 * 后台配置文件脚本
 * 调用方式：每分钟跑一次
 * 
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区

$serverList = ServerModel::getServList();

$btime = microtime(true);

echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

Common::loadModel('GameActModel');
$GameActModel = new GameActModel();

$newAddLog = array('shop_gift');
//通服基础配置
$dir = CONFIG_DIR . '/houtaicfg/pzall/';    //文件路径
$file=scandir($dir);   //获取所有文件名称
if (!empty($file)) {
    
    foreach ($file as $name) {
        //过滤不是 php文件
        if (!strpos($name, '.php')) {
            continue;
        }
        
        $dir_file = CONFIG_DIR . '/houtaicfg/pzall/' . $name;//需要包含的文件
        //过滤不能读取
        if (!file_exists($dir_file)) {
            continue;
        }
        //获取key
        $key = str_replace('.php', '', $name);
        if (!in_array($key, $newAddLog)) {
            continue;
        }
        //获取value
        $value = file_get_contents($dir_file);  //读取新配置
        $GameActModel->add(array(
            'act_key'=>trim('huodong_82'),
            'server'=>'all',
            'audit'=>1,
            'auser'=>'文件导入',
            'atime'=>time(),
            'contents'=>$value,
        ));
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

        //单服活动配置
        $dir = CONFIG_DIR . '/houtaicfg/pzbase/'.$Sev_Cfg['sevid'].'/';    //文件路径
        if(is_dir($dir)){
            $file=scandir($dir);   //获取所有文件名称
        }
        if (!empty($file)) {
            foreach ($file as $name) {
                //过滤不是 php文件
                if (!strpos($name, '.php')) {
                    continue;
                }
                $dir_file = CONFIG_DIR . '/houtaicfg/pzbase/' . $Sev_Cfg['sevid'] . '/' . $name;//需要包含的文件
                //过滤不能读取
                if (!file_exists($dir_file)) {
                    continue;
                }
                //获取key
                $key = str_replace('.php', '', $name);
                if (!in_array($key, $newAddLog)) {
                    //新服加过的，同服不重复加
                    continue;
                }
                //获取value
                $value = file_get_contents($dir_file);  //读取新配置
                $GameActModel->add(array(
                    'act_key' => trim('huodong_82'),
                    'server' => $Sev_Cfg['sevid'],
                    'audit' => 1,
                    'auser' => '文件导入',
                    'atime' => time(),
                    'contents' => $value,
                ));
            }
        }
        //单服基础配置end
    }
}
//单服活动配置end

$GameActModel->destroy();

echo PHP_EOL, '耗时(s)=', (microtime(true)-$btime), PHP_EOL;
echo PHP_EOL, '-------------------------------------------------------------------',  PHP_EOL;
exit();