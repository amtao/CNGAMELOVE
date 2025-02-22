<?php
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
if (!empty($_REQUEST['frontString'])){
    $db = Common::getDbBySevId(999);
    $table = "`front_log`";
    $string = $_REQUEST['frontString'];
    $sql = 'INSERT INTO '.$table.' (`string`,`time`) VALUES ('.$string.','.time().')';
    $db->query($sql);
    echo 1;
}
