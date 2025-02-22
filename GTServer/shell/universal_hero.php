<?php
/**
 * 修复 狄仁杰
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ServerModel');
$serverList = ServerModel::getServList();
$data = array();
if (is_array($serverList)) {
    foreach ($serverList as $k => $v) {
        if (empty($v)) {
            continue;
        }
        if ($v['id'] != 6) {
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
        $memcache = Common::getMyMem();

        //------------额外的更新  开始 ------
        //从旧缓存 获取制定数据 更新数据库
        $uids = array(6024701,6022902,6026503,6011506,6013106,6000010,6002712,6015816,6017516,6020518,6009120,6022022,6014623,6013427,6027527,6019729,6014530,6026533,6025234,6000836,6003736,6010738,6021041,6025341,6024443,6000444,6014045,6026345,6017547,6000348,6023749,6009252,6021653,6003555,6024555,6018357,6016159,6024659,6006061,6005262,6015062,6022662,6018263,6018264,6001367,6024968,6009369,6012174,6013274,6016674,6025174,6023975,6017478,6020880,6024680,6013381,6020681,6019082,6009985,6003586,6008786,6003892,6027594,6011095,6013296,6001299,6019299);

        foreach($uids as $uid){
            $memKey = $uid.'_hero';
            $json = $memcache->get($memKey);

            foreach ($json as $heroID => $json_v) {
                if (empty($json_v)) {
                    echo 'err_mem_empty' . $uid . PHP_EOL;
                }
                //缓存数据更新数据库
                $acttable = 'hero_' . Common::computeTableId($uid);
                $actsql = "update `{$acttable}` set `level`={$json_v['level']},`exp`={$json_v['exp']},`zzexp`={$json_v['zzexp']} 
where `uid` = {$uid} and `heroid`={$heroID};";
                $re = $db->query($actsql);
                echo $actsql . PHP_EOL;
                echo $re . PHP_EOL;
            }
        }
        //缓存获取数据


        exit();
        //------------额外的更新  结束 ------

/*
        $table_div = Common::get_table_div();
        for ($i = 0; $i < $table_div; $i++) {
            $table = 'user_' . Common::computeTableId($i);
            $sql = "SELECT `uid` FROM `{$table}`;";
            $result = $db->fetchArray($sql);//遍历所有玩家
            foreach ($result as $dk => $dv){
                //查询 hero 数据库有没有狄仁杰
                $acttable = 'hero_' . Common::computeTableId($dv['uid']);
                $actsql = "SELECT `uid` FROM `{$acttable}` where  `uid`={$dv['uid']} and `heroid`=44 ;";
                //echo $actsql;
                $actresult = $db->fetchArray($actsql);//查询act数据库有没有
                if (empty($actresult)){
                    //如果没有  进缓存查
                    $memKey = $dv['uid'].'_hero';
                    $json = $memcache->get($memKey);
                    if (empty($json[44])){//如果缓存也没有  放弃这个人
                        //echo ' memNull'.PHP_EOL;
                        continue;
                    }
                    //echo ' memYes'.$dv['uid'];
                    //$tjson = json_encode($json['tjson']);
                    //echo json_encode($json['tjson']).PHP_EOL;
                    $h_info = $json[44];

                    $epskill = json_encode($h_info['epskill']);
                    $pkskill = json_encode($h_info['pkskill']);
                    $ghskill = json_encode($h_info['ghskill']);

                    $sql = "INSERT INTO `".$acttable."` set `uid`='{$h_info['uid']}', `heroid`='44', `level`='{$h_info['level']}', `exp`='{$h_info['exp']}', `zzexp`='{$h_info['zzexp']}',`pkexp`='{$h_info['pkexp']}', `senior`='{$h_info['senior']}',`epskill`='{$epskill}', `pkskill`='{$pkskill}',`ghskill`='{$ghskill}',`e1`='{$h_info['e1']}',`e2`='{$h_info['e2']}',`e3`='{$h_info['e3']}',`e4`='{$h_info['e4']}';";
                    $re = $db->query($sql);
                    echo $sql.PHP_EOL;
                    echo $re.PHP_EOL;

                }else{
                    //如果 act数据库有 正常玩家  跳过不管
                    //echo ' ok'.PHP_EOL;
                }
                unset($actresult);
                //$memcache = $dv['uid'].'_act_36';
            }

        }
*/
    }

    /*
     * 额外的等级更新
     *
     * `level`='{$h_info['level']}',
`exp`='{$h_info['exp']}',
`zzexp`='{$h_info['zzexp']}',

     *
     */

}