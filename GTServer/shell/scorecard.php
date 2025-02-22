<?php
/*
 * 帮战区服切换
 * */
require_once dirname(__FILE__) . '/../public/common.inc.php';
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;

Common::loadModel('HoutaiModel');
$data = HoutaiModel::get_all_pz();



/**
 * 手动输入clubpk旧主区服id
 * 例如: 'ksev' => array(
        array(1,4),
        array(5,6),
        array(7,999),
        ),
 *  那主区服就是 1,5,7
 */
$serid_clubpk = array(
    //输入 todo
    1,5,7
);

//获取 修改clubpk后的新配置
$serid_cross_section = $data['clubpk']['ksev'];


//获取所有分数存在$card中
$card = array();


foreach ($serid_clubpk as $id) {
    try
    {
        $redis = Common::getRedisBySevId($id);
    }

    //捕获异常
    catch(Exception $e)
    {
        echo '手动输入配置错误，没有'.$id.'区服,redis链接地址找不到.'.PHP_EOL;
        echo 'Message: ' .$e->getMessage();
        exit();
    }
    //备份数据
    Game::logMsg('/data/logs/clubkuajf'.Game::get_today_id(),$id.'区');
    $data = $redis->zRevRange('clubkuajf_redis', 0, -1,true);
    Game::logMsg('/data/logs/clubkuajf'.Game::get_today_id(),json_encode($data));
    //赋值给$card
    foreach ($data as $k => $value) {
        $card[$k] = $value;
    }
    //清除redis
    $redis->delete('clubkuajf_redis');
}




//排行重新导入
foreach ($serid_cross_section as $id){

    try
    {
        $redis = Common::getRedisBySevId($id[0]);
    }

    catch (Exception $e)
    {
        echo 'clubpk配置错误，没有'.$id[0].'区服,redis链接地址找不到.'.PHP_EOL;
        echo 'Message: ' .$e->getMessage();
        exit();
    }
    foreach ($card as $k => $value) {
        //获取SEV id
        $re = Game::get_sevid_club($k);
        $SevcfgObj = Common::getSevCfgObj($re);
        $re = $SevcfgObj->getHE();
        if($re >= $id[0] && $re<=$id[1]) {
//            $redis->ZAdd('clubkuajf_kua_redis',$value,$k);
            $redis->ZAdd('clubkuajf_redis',$value,$k);
            echo $k.'=>'.$value.'移动到'.$id[0].'区'.PHP_EOL;
            unset($card[$k]);
        }
    }
}

echo "---------------------完成，哇哈哈哈----------------------------".PHP_EOL;