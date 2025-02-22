<?php
//数据统计
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
set_time_limit(0);
$test = array(
    'xj2455701993
','xj2455030071
','xj2453689666
','xj2453358028
','xj2452825027
','xj2452804941
','mark1979
','xj2463239037
','xj2462606420
','xj2462404011
','xj2462228969
','xj2461297273
','xj2460443010
','xj2456696828
','lsl198151200
','xj2472132571
','xj2471847718
','xj2470450458
','xj2468065256
','xj2462606420
','185902892.00 
','xj2481787111
','xj2481679023
','xj2480727433
','xj2480601123
','xj2479494417
','xj2478760657
','xj2476871752
','xj2474236651
','userscj
','xj2488605624
','xj2488511598
','wq221165
','438860521.00 
','xj2498763782
','wi622665
','sd212410
','CAOXIAOFANYA
','15840573293.00 
','xj2506858971
','xj2504951246
','xj2504066813
','xj2504040669
','xj2501022621
','xj2499953066
','xj2499918979
','xj2514804643
','xj2508976052
','xj2523103769
','xj2522834245
','xj2521788998
','xj2521300311
','xj2520279191
','ghggghhv
','xj5413364959
','xj2533893568
','xj2533412048
','xj2533368552
','xj2532465522
','xj2532173283
','xj2531437429
','xj2531360149
','xj2531337323
','xj2529977825
','xj2529772567
','xj2529003882
','xj2528770525
','xj2528009698
','13836741057.00 
','xj2542639446
','xj2541928994
','xj2538723483
','xj2550481215
','xj2559929365
','xj2557736583
','xj2554154040
','ddliubo
','xj2567054885
','xj2561600010
','xj2574969390
','xj2573075384
','xj2571711652
','xj2570945072
','13889862526.00 
','xj2585095410
','xj2584747991
','xj2584591347
','xj2583323030
','xj2580299728
','3349454568.00 
','xj2594245690
','xj2594238589
','xj2593463232
','xj2591044510
','gcs123456
','18340214040.00 
','xj2598997575
','xj2598262281
','xj2594693159
','xj2611547248');
foreach ($test as $value){
    $data[] = trim($value);
}
$SevidCfg1 = Common::getSevidCfg(1);
$db = Common::getMyDb();
$openid =  implode("','", $data);
$sql = "SELECT * FROM `register` WHERE `openid` IN ('".$openid."')";
$result = $db->fetchArray($sql);
unset($db, $sql);
$info  = array();
foreach ($result as $rk => $rv){
    $SevidCfg1 = Common::getSevidCfg($rv['servid']);
    $db = Common::getMyDb();
    $table = '`user_'.Common::computeTableId($rv['uid']).'`';
    $where = '`uid`='.$rv['uid'];
    $sql = "SELECT `level`,`uid` FROM {$table} WHERE {$where} ";
    $results = $db->fetchRow($sql);
    $info[$rv['openid']] = $results;
}
foreach ($info as $ik => $iv){
    echo $ik.'  '.$iv['uid'].'    '.$iv['level'].'<br/>';
    $level[$iv['level']] +=1;
    $total +=1;
}

foreach ($level as $lk => $lv){
    echo $lk.  '占比：'. $lv*100/$total.'%<br/>' ;
}