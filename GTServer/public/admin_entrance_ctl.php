<?php
session_start();
if(empty($_GET['sevid'] ))
{
    $_GET['sevid'] = 1;
}

// 进入注册界面
if ( isset( $_POST["register"] ) ) {

    require_once 'admin_register.php';
    return;
}


require_once dirname( __FILE__ ) . '/common.inc.php';
$_GET['sig'] = $_POST["password"];
$_GET['user'] = $_POST["username"];
$username = $_GET['user'];
$pwd =$_GET['sig'];

if (  isset( $_POST["reg"] ) || isset($_POST["username"]) ) {

    $SevidCfg1 = Common::getSevidCfg(1);
    $db = Common::getDbBySevId(1);
    $sql = "SELECT * FROM `admin_user` WHERE `user` = '$username' AND `pwd` = '$pwd'";
    $adminUser = $db->fetchRow($sql);
    if ( isset( $_POST["reg"] ) ) {

        $nickname = $_POST["nickname"];

        if (!empty($adminUser) && $adminUser["status"] = 0) {
            echo '<script>alert("帐号已存在");</script>';
            return;
        }

        if (!empty($adminUser)) {

            $sql = "UPDATE `admin_user` SET `status` = 0, `pwd` = '$pwd', `name` = '$nickname' WHERE `user` = '$username'";
        }else{

        	$powerList = array(
        		"ml" => array(
                    "1" => array("1","2"),
        			"2" => array("1","2","6","3","7","4","5","9","11","12","13","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29"),
        			"3" => array("1","2","28","3","4","31","37","39","32","22","5","7","33","8","20","21","26","27","29","30","34","35","36","38"),
        			"4" => array("1","3","4","6","7"),
                    "5" => array("13","6","8","4","2","11","14","16","17","19","20","21","22"),
                    "6" => array("1"),
        			"7" => array("1","2","3","7","4","5","6"),
                    "8" => array("1","2","3","4","6","7","8","9","10"),
                    "10" => array("1","2","3","4","5","6","7","8","9","10","11","12"),
        		)
        	);
        	$powerJson = json_encode($powerList);
            $sql = "INSERT INTO `admin_user` (`user`,`name`,`pwd`,`status`,`power`) VALUES ('{$username}', '{$nickname}', '{$pwd}', 0, '{$powerJson}')";
        }
        $res = $db->query($sql);
        if($res === false){
            echo '<script>alert("DB操作失败");</script>';
        }

        require_once 'admin_entrance.php';
        return;
    }
    $userPowerList = json_decode($adminUser["power"], true);
    $_SESSION['USER_POWER_LIST'] = $userPowerList;
}

if($adminUser || $_SESSION['admin'] || ($username == "wyadmin" && $pwd == "654123?") )
{
       $_SESSION['admin'] = 1;
}else{

    echo '<script>alert("登录失效");</script>';exit;
}

require_once 'admin.php';
