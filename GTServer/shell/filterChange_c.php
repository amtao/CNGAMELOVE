<?php

require_once dirname(__FILE__) . '/common.inc.php';
$SevCfg = Common::getSevidCfg(999);

$filter = Game::getcfg('filter');

if(empty($filter)){
    echo '未找到文件';exit();
}
foreach ($filter as $v){
    $p = implode('.{0,2}', mb_str_split($v));
    $p = str_replace(array('(',')','+','*','['.']','/','$','^','?'),array('（','）','\+','\*','\[','\]','\/','\$','\^','\?'),$p);
    $pa = "'/{$p}/ui',";
    echo $pa,PHP_EOL;
}


//分词
function mb_str_split($str){
    return preg_split('/(?<!^)(?!$)/u', $str );
}