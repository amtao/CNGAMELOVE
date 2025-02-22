<?php
/**
 * 统用脚本
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
        if ($v['id'] != 2) {
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
        $table_div = Common::get_table_div();
        for ($i = 0; $i < $table_div; $i++) {
            $table = 'user_' . Common::computeTableId($i);
            $sql = "SELECT `uid` FROM `{$table}`;";

            //echo $sql.PHP_EOL;
            $result = $db->fetchArray($sql);//遍历所有玩家
            foreach ($result as $dk => $dv){
                $acttable = 'act_' . Common::computeTableId($dv['uid']);
                $actsql = "SELECT `uid` FROM `{$acttable}` where  `uid`={$dv['uid']} and `actid`=36 ;";
                //echo $actsql;
                $actresult = $db->fetchArray($actsql);//查询act数据库有没有36
                if (empty($actresult)){
                    //如果没有  进缓存查
                    //echo $dv['uid'];
                    $memKey = $dv['uid'].'_act_36';
                    $json = $memcache->get($memKey);
                    if (empty($json)){//如果缓存也没有  放弃这个人
                        //echo ' memNull'.PHP_EOL;
                        continue;
                    }
                    echo ' memYes'.$dv['uid'];
                    $tjson = json_encode($json['tjson']);
                    //echo json_encode($json['tjson']).PHP_EOL;
                    $sql = "INSERT INTO `".$acttable."` VALUES ({$dv['uid']}, 36, '{$tjson}');";
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
    }

}