<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 2017/12/26
 * Time: 10:33
 */
class Auth{

    public function __construct(){
        Common::loadVoComModel('ComVoComModel');
        Common::loadModel('AdminModel');
    }

    public function index(){
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    public function userAccount(){
        $key = 'userAccount';
        $ComVoComModel = new ComVoComModel($key, true);
        $userAccount = $ComVoComModel->getValue();
        if (empty($userAccount)){
            $userAccount = include(ROOT_DIR . '/administrator/config/userAccount.php');
            $ComVoComModel->updateValue($userAccount);
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    public function deleteUser(){
        $delkey = $_REQUEST['key'];
        $key = 'userAccount';
        $ComVoComModel = new ComVoComModel($key, true);
        $userAccount = $ComVoComModel->getValue();
        if (!empty($userAccount[$delkey])){
            unset($userAccount[$delkey]);
            $ComVoComModel->updateValue($userAccount);
            //后台操作日志
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($delkey => $userAccount[$delkey]));
            echo $delkey.'已删除!';
        }else{
            echo $delkey.'删除失败!';
        }
    }

    public function addUser(){
        $account = $_REQUEST['account'];
        $username = $_REQUEST['username'];
        if (!empty($account) && !empty($username)){
            $key = 'userAccount';
            $ComVoComModel = new ComVoComModel($key, true);
            $userAccount = $ComVoComModel->getValue();
            if (!empty($userAccount[$account])){
                $tip = "已修改！";
            }else{
                $tip = "已添加！";
            }
            $userAccount[$account]['name'] = $username;
            $ComVoComModel->updateValue($userAccount);
            //后台操作日志
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($account => $tip));
            echo $tip;
        }else{
            echo '失败!';
        }
    }

    /**
     * 删除权限
     */
    public function deleteAccount(){
        $account = $_REQUEST['account'];
        if (!empty($account)){
            $key = 'authConfig';
            $ComVoComModel = new ComVoComModel($key, true);
            $authConfig = $ComVoComModel->getValue();
            if (!empty($authConfig[$account])){
                unset($authConfig[$account]);
                $ComVoComModel->updateValue($authConfig);
                //后台操作日志
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($account => $authConfig['zhuanfu']));
                $tip = "已删除！";
            }else{
                $tip = "参数错误！";
            }
            echo $tip;
        }else{
            echo '参数错误!';
        }
    }
    /**
     *  添加权限
     */
    public function addAuthConfig(){
        $account = $_REQUEST['account'];
        if (!empty($account)){
            $key = 'authConfig';
            $ComVoComModel = new ComVoComModel($key, true);
            $authConfig = $ComVoComModel->getValue();
            if (empty($authConfig[$account])){
                $authConfig[$account] = $authConfig['zhuanfu'];
                $ComVoComModel->updateValue($authConfig);
                //后台操作日志
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($account => $authConfig['zhuanfu']));
                $tip = "已添加！";
            }else{
                $tip = "已存在！";
            }
            echo $tip;
        }else{
            echo '失败!';
        }
    }
    /**
     * 权限
     */
    public function authConfig(){

        $SevidCfg1 = Common::getSevidCfg(1);
        $db = Common::getDbBySevId(1);
        $sql = "SELECT * FROM `admin_user` WHERE `status` = 0";
        $adminUsers = $db->fetchArray($sql);

        $userAccount = array();
        $authConfig = array();
        if ( !empty($adminUsers) ){

            foreach ($adminUsers as $key => $value) {

                if ($value["user"] == 'wyadmin') continue;
                if (empty($value["power"])) {
                    $userAccount[] = $value;
                }else{
                    $value["power"] = json_decode($value["power"], true);
                    $authConfig[] = $value;
                }
            }
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    /**
     * 权限修改
     */
    public function changeAuth(){
        $auth = $_REQUEST['key'];
        $account = $_REQUEST['account'];
        $auths = explode('-', $auth);

        $SevidCfg1 = Common::getSevidCfg(1);
        $db = Common::getDbBySevId(1);
        $sql = "SELECT * FROM `admin_user` WHERE `user` = '$account' AND `status` = 0";
        $adminUser = $db->fetchRow($sql);

        if (empty($adminUser)) {
            echo '<script>alert("帐号不存在");</script>';
            return;
        }
        $powerList = json_decode($adminUser["power"], true);
        if (empty($powerList['ml'][$auths[0]]) || !in_array($auths[1], $powerList['ml'][$auths[0]])){

            $powerList['ml'][$auths[0]][] = $auths[1];
            $powerList = json_encode($powerList);
            $sql = "UPDATE `admin_user` SET `power` = '$powerList' WHERE `user` = '$account'";
            $res = $db->query($sql);
            if($res === false){
                echo '<script>alert("DB操作失败");</script>';
            }
            //后台操作日志
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($account => $auths));
            echo "已修改!";
        }else{
            $remove = array_search($auths[1], $powerList['ml'][$auths[0]]);
            unset($powerList['ml'][$auths[0]][$remove]);
            if (empty($powerList['ml'][$auths[0]])){
                $removes = array_search($auths[0], $powerList['ml']);
                unset($powerList['ml'][$removes]);
            }

            $powerList = json_encode($powerList);
            $sql = "UPDATE `admin_user` SET `power` = '$powerList' WHERE `user` = '$account'";
            $res = $db->query($sql);
            if($res === false){
                echo '<script>alert("DB操作失败");</script>';
            }
            //后台操作日志
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($account => $auths));
            echo "已删除!";
        }
    }

    /**
     * 添加平台限制
     */
    public function addPt(){
        $pt = $_REQUEST['platform'];
        $account = $_REQUEST['account'];
        $key = 'authConfig';
        $ComVoComModel = new ComVoComModel($key, true);
        $authConfig = $ComVoComModel->getValue();
        Common::loadModel('OrderModel');
        $platformInfo = OrderModel::get_one_platform_info($pt);
        if (empty($authConfig[$account]['qd']['pt']) || !in_array($pt, $authConfig[$account]['qd']['pt'])){
            $authConfig[$account]['qd']['pt'][] = $pt;
            if (empty($authConfig[$account]['qd']['sdk']) || !in_array($platformInfo['sdk'], $authConfig[$account]['qd']['sdk'])){
                $authConfig[$account]['qd']['sdk'][] = $platformInfo['sdk'];
            }
            $ComVoComModel->updateValue($authConfig);
            //后台操作日志
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($account => array($pt => $platformInfo)));
            echo "已修改!";
        }else{
            $remove = array_search($pt, $authConfig[$account]['qd']['pt']);
            unset($authConfig[$account]['qd']['pt'][$remove]);
            if (empty($authConfig[$account]['qd']['pt'])){
                unset($authConfig[$account]['qd']);
            }
            $ComVoComModel->updateValue($authConfig);
            //后台操作日志
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($account => $pt));
            echo "已删除!";
        }
    }

    /**
     * 添加平台限制
     */
    public function removeSdk(){
        $sdk = $_REQUEST['sdk'];
        $account = $_REQUEST['account'];
        $key = 'authConfig';
        $ComVoComModel = new ComVoComModel($key, true);
        $authConfig = $ComVoComModel->getValue();
        if (in_array($sdk, $authConfig[$account]['qd']['sdk'])){
            $remove = array_search($sdk, $authConfig[$account]['qd']['sdk']);
            unset($authConfig[$account]['qd']['sdk'][$remove]);
            if (empty($authConfig[$account]['qd']['sdk'])){
                unset($authConfig[$account]['qd']);
            }
            $ComVoComModel->updateValue($authConfig);
            //后台操作日志
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($account => $sdk));
            echo "已删除!";
        }else{

            echo "删除失败!";
        }
    }

    /**
     * 添加平台限制
     */
    public function changeBan(){
        $ban = $_REQUEST['ban'];
        $account = $_REQUEST['account'];
        $key = 'authConfig';
        $ComVoComModel = new ComVoComModel($key, true);
        $authConfig = $ComVoComModel->getValue();
        if (empty($authConfig[$account]['ban']['user'][$ban])){
            $authConfig[$account]['ban']['user'][$ban] = 1;
            $ComVoComModel->updateValue($authConfig);
            echo "已删除!";
        }else{
            $authConfig[$account]['ban']['user'][$ban] = 0;
            $ComVoComModel->updateValue($authConfig);
            echo "已添加!";
        }
        //后台操作日志
        AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($account => $ban));
    }
}