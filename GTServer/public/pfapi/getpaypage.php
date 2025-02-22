<?php
/**
 * Created by PhpStorm.
 * User: 'Mr.Chen'
 * Date: 2019/6/21
 * Time: 15:38
 */


/**
 * 网页充值接口
 * @author wenyj
 * @version
 *   - 20190621, init
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

//角色信息获取

$openid = trim($_REQUEST['userId']);//获取openid
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
    exit('userId错误');
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
        //获取充值金额
        $db = Common::getMyDb();
        $sql = "select SUM(`money`) as 'TOTAL' from `t_order` where `roleid`='{$uid}' and `platform` != 'fuli'";
        $totalMoney = $db->fetchRow($sql);
        if($totalMoney['TOTAL']==Null){
            $totalMoney['TOTAL'] = 0;
        }
        $totalarray[] = $totalMoney['TOTAL'];
        //获取服务器名称
        $serverList = ServerModel::getServList();

        $datarts[] = array(
            'role_id'=>$uid,                              //角色id
            'role_name'=>$info['name'],                      //角色昵称
            'server_id'=>$sevid,                             //服务器id
            'server_name'=>$serverList[$sevid]['name'],      //服务器名
        );
    }
    $total = 0;
    for($i=0,$j=count($totalarray);$i<$j;$i++){
        $total += $totalarray[$i];
    }
    $out_data = array("state"=>1,"data"=>array("roles"=>json_encode($datarts),"total_fee"=>$total));
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




