<?php
/**
 * 修复 狄仁杰
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';

$serverID = 6;
$SevidCfg1 = Common::getSevidCfg($serverID);//子服ID
$db = Common::getMyDb();
$memcacheNow = Common::getCacheBySevId($serverID);
$memcache = Common::getHistoryCacheBySevId($serverID);

//------------额外的更新  结束 ------
//$result = array(6002529, 6003234, 6010687, 6000781, 6004370, 6000899, 6008351);
$result = array(6003311,6001521,6009322,6011228,6001149,6003852,6006753,6003156,6000857,6006959,6002667,6011767,6007673,6006879,6009879,6010283,6012789,6004994,6004895);
foreach ($result as $dv) {
    //如果没有  进缓存查
    $memKey = $dv.'_hero';
    $json = $memcache->get($memKey);
    if (empty($json[44])){//如果缓存也没有  放弃这个人
        continue;
    }
    $jsonNow = $memcacheNow->get($memKey);
    file_put_contents('/tmp/u_h_h_180306', var_export($jsonNow, true).PHP_EOL, FILE_APPEND);

    if (!empty($jsonNow[44])){//如果现在缓存有  放弃这个人
        continue;
    }
    $h_info = $json[44];

    $epskill = json_encode($h_info['epskill']);
    $pkskill = json_encode($h_info['pkskill']);
    $ghskill = json_encode($h_info['ghskill']);

    $acttable = 'hero_' . Common::computeTableId($dv);
    $sql = "INSERT INTO `{$acttable}` set `uid`='{$h_info['uid']}', `heroid`='44', `level`='{$h_info['level']}', `exp`='{$h_info['exp']}', `zzexp`='{$h_info['zzexp']}',`pkexp`='{$h_info['pkexp']}',`senior`='{$h_info['senior']}',`epskill`='{$epskill}', `pkskill`='{$pkskill}',`ghskill`='{$ghskill}',`e1`='{$h_info['e1']}',`e2`='{$h_info['e2']}',`e3`='{$h_info['e3']}',`e4`='{$h_info['e4']}';";
    $re = $db->query($sql);
    echo $sql.PHP_EOL;
    var_dump($re);
    var_dump(mysql_errno());

    //清理现有缓存
    $memcacheNow->delete($memKey);
}