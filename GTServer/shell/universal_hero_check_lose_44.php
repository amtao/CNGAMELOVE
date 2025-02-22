<?php
/**
 * 修复 狄仁杰
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
$serverList = ServerModel::getServList();
$marksMax = array(
    //安峰、快游
    'gjypaf'=>150, 'lyjxky'=>141,
    //官方、手游汇
    'jwdgr'=>258, 'jpwysyh'=>79,
    //掌娱、拇指游玩、草花
    'gjypzy'=>167, 'gjypmzyw'=>100, 'gjjpch'=>46,
    //繁体
    'lzqdgft'=>24, 'dqhd'=>44, 'sglyqw'=>27,
    //剑圣、卢丽华、快玩
    'dqgrwhyjs'=>5, 'llhzf'=>38, 'ypdckw'=>88,
    //官方ios
    'lyjxios'=>1,
);
if (is_array($serverList)) {
    foreach ($serverList as $k => $v) {
        if (empty($v)) {
            continue;
        }
        if (defined("GAME_MARK") && isset($marksMax[GAME_MARK]) && $sevid > $marksMax[GAME_MARK]) {
            continue;
        }
        $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
        if (999 == $SevidCfg1['sevid']) {
            continue;
        }
        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
            continue;
        }
        $db = Common::getMyDb();
        $memcache = Common::getHistoryCacheBySevId($v['id']);
        $table_div = Common::get_table_div();
        for ($i = 0; $i < $table_div; $i++) {
            $table = 'user_' . Common::computeTableId($i);
            $sql = "SELECT `uid` FROM `{$table}`;";
            $result = $db->fetchArray($sql);//遍历所有玩家
            foreach ($result as $dk => $dv){
                //查询 hero 数据库有没有狄仁杰
                $acttable = 'hero_' . Common::computeTableId($dv['uid']);
                $actsql = "SELECT `uid` FROM `{$acttable}` where  `uid`={$dv['uid']} and `heroid`=44;";
                $actresult = $db->fetchArray($actsql);//查询act数据库有没有
                if (empty($actresult)){
                    //如果没有  进缓存查
                    $memKey = $dv['uid'].'_hero';
                    $json = $memcache->get($memKey);
                    if (empty($json[44])){//如果缓存也没有  放弃这个人
                        continue;
                    }
                    echo $dv['uid'], ",";
                    //$h_info = $json[44];

                    //$epskill = json_encode($h_info['epskill']);
                    //$pkskill = json_encode($h_info['pkskill']);
                    //$ghskill = json_encode($h_info['ghskill']);

                    //$sql = "INSERT INTO `".$acttable."` set `uid`='{$h_info['uid']}', `heroid`='44', `level`='{$h_info['level']}', `exp`='{$h_info['exp']}', `zzexp`='{$h_info['zzexp']}',`pkexp`='{$h_info['pkexp']}', `senior`='{$h_info['senior']}',`epskill`='{$epskill}', `pkskill`='{$pkskill}',`ghskill`='{$ghskill}',`e1`='{$h_info['e1']}',`e2`='{$h_info['e2']}',`e3`='{$h_info['e3']}',`e4`='{$h_info['e4']}';";
                    //$re = $db->query($sql);
                    //echo $sql.PHP_EOL;
                    //echo $re.PHP_EOL;
                }
                else{
                    //如果 act数据库有 正常玩家  跳过不管
                }
                unset($actresult);
            }

        }
    }
}