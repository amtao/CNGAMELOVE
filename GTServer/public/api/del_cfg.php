<?php
require_once dirname(dirname(__FILE__)) . '/common.inc.php';

$dir = CONFIG_DIR . '/game/cfg_val_format/';
$file = scandir($dir);
foreach($file as $v){
    if(!is_dir($v)){
        unlink($dir . $v);
    }
}
echo '删除成功';


