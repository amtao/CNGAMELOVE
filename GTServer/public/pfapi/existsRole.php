<?php
/**
 * Created by PhpStorm.
 * User: 'Mr.Chen'
 * Date: 2019/5/20
 * Time: 12:19
 */


/**
 * 存在角色服务器查询接口
 * @author wenyj
 * @version
 *   - 20170920, init
 * @return JSON
 */
error_reporting(E_ALL);
ini_set('display_errors','on');

if($_GET['_pf'] == "epmycxianyuovergat_zjfh"){
    $_GET['_pf'] = "epandxianyuovergat_zjfh";
}

if (file_exists(dirname(dirname(__FILE__)) . '/pay_cfg/' . $_GET['_pf'] . '.php')) {
    require_once dirname(dirname(__FILE__)) . '/pay_cfg/' . $_GET['_pf'] . '.php';
} else {
    exit('param invalid(_pf)');
}

require_once dirname(dirname(__FILE__)) . '/common.inc.php';

define('LOG_API_ACTIVE_REWARD', LOG_PATH . '/apilog/' . SNS . '_' . SERVER_ID . '_' . strtr(basename(__FILE__), array('.' => '_')) . '.log');

// 记录request参数
$params = (empty($_REQUEST)) ? file_get_contents('php://input') : $_REQUEST;
Game::logMsg(LOG_API_ACTIVE_REWARD, 'request = ' . var_export($params, 1));
if (empty($params)) {
    Game::logMsg(LOG_API_ACTIVE_REWARD, '参数为空');
    exit('参数为空');
}

//平台验证
$platform = defined('SNS') ? SNS : '';
if (empty($platform)) {
    Game::logMsg(LOG_API_ACTIVE_REWARD, '参数错误（未知平台）');
    exit('参数错误（未知平台）');
}
//此段代码可忽略
Common::loadModel('OrderModel');
$Api = OrderModel::sdk_func($platform);
if (method_exists($Api, 'getRoleInfo')) {
    $rt = $Api->getRoleInfo($params);
    Game::logMsg(LOG_API_ACTIVE_REWARD,  'api rt=' . var_export($rt, 1));
    echo (is_array($rt)) ? json_encode($rt) : $rt;
    exit();
} else {
    Game::logMsg(LOG_API_ACTIVE_REWARD, '接口未授权');
    //exit('接口未授权');
}

//存在角色服务器获取

$openid = trim($_REQUEST['openid']);//获取openid
$data = array ();

//获取默认服务器
Common::loadModel('ServerModel');
$serverID = ServerModel::getDefaultServerId();
$SevidCfg = Common::getSevidCfg($serverID);
//连接对应数据库获取uid
$db = Common::getDbBySevId($serverID);
$sql = "select openid,servid,uid,`data` from `register` where `openid`='{$openid}'";
$row = $db->fetchArray($sql);
if(empty($row)){
    exit('openid错误');
}else{
    //初始化数组
    $uids = array();
    $datarts = array();
    //主号uid
    $uids[] = $row[0]['uid'];
    //小号uid
    if(!empty($row[0]['data'])){
        $datas = json_decode($row[0]['data'],1);
        foreach ($datas as $k=>$v){
            $uids[] = $v['uid'];
        }
    }
    //构造数据
    foreach ($uids as $index => $uid){
        //获取角色区服
        $sevid = get_sevid($uid);
        //初始化
        $SevidCfg = Common::getSevidCfg($sevid);
        Common::loadModel("Master");
        Common::loadModel('ServerModel');
        //获取角色信息
        $UserModel = Master::getUser($uid);
        $info = $UserModel->info;
        //实例化redis
        $Redis1Model = Master::getRedis1();
        //获取势力值
        $value = $Redis1Model->zScore($uid);
        //获取当前等级的策划数据
        $guanCfg = Game::getcfg_info('guan',$info['level']);
        //获取服务器名称
        $serverList = ServerModel::getServList();

        $datarts[] = array(
            'value'=>$value,                                //势力值
            'openid'=>$openid,                              //渠道id
            'serverid'=>$sevid,                             //服务器id
            'level'=>$info['level'],                        //角色等级
            'userName'=>$info['name'],                      //角色昵称
            'levelname'=>$guanCfg['name'],                  //角色身份
            'lastlogin'=>$info['lastlogin'],                //最后登陆时间
            'servername'=>$serverList[$sevid]['name'],      //服务器名
        );
    }
    $out_data ['a']['role']['rolelist']= $datarts;
    echo json_encode($out_data);
}

//根据uid获取对应服务器
function get_sevid($uid){
    if($uid == 0){
        if(defined('IS_TEST_SERVER') && IS_TEST_SERVER){
            return 999;
        }
        Master::error(CLUB_NAME_NOT_NULL);
    }
    if ($uid < 1000000){
        if(defined('IS_TEST_SERVER') && IS_TEST_SERVER){
            return 999;
        }
        Master::error(CLUB_NAME_NOT_NULL);
    }else{
        return intval($uid/1000000);
    }
}




