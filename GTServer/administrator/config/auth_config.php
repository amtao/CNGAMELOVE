<?php
/**
 * Author:Luffy
 * Date: 2017/7/4
 */

//开放目录权限
$account = array(

);

if(empty($account[$_SESSION["CURRENT_USER"]])){
	$account[$_SESSION["CURRENT_USER"]] = array();
}
return $account[$_SESSION["CURRENT_USER"]];









