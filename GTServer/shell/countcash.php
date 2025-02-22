<?php
/*
 * 掌娱统计元宝
 */

require_once dirname(__FILE__) . '/../public/common.inc.php';

$min = intval($_SERVER['argv'][1]);// 默认是全部区
$max = intval($_SERVER['argv'][2]);// 默认是全部区
echo PHP_EOL, 'minServerID', $min, PHP_EOL;
echo 'maxServerID', $max, PHP_EOL;


$cash = 0;
for ($i = $min; $i<=$max; $i++) {
    $SevidCfg = Common::getSevidCfg($i);
    $db = Common::getDbBySevId($i);
    for ($j = 0;$j<100;$j++) {
        if ($j<10) {
            $sql = "SELECT SUM(cash_sys+cash_buy-cash_use) as cash from user_0{$j}";
        }else{
            $sql = "SELECT SUM(cash_sys+cash_buy-cash_use) as cash from user_{$j}";
        }
        $result = $db->fetchRow($sql);
        $cash += $result['cash'];
    }
    echo $i.'区'.','.$cash, PHP_EOL;
    $cash = 0;
}








