<?php
/**
 * 此脚本统计在时间区间内的和在服务器区间内的vip玩家的登录信息
 * 注意数据量大需要分段跑
 * 接口地址示例：
 * http://gjypaf.zhisnet.cn/api/email_look.php?beginser=1&endser=60&beginlogin=1513353600&endlogin=1513612799&excel=0
 * http://gjypaf.zhisnet.cn/api/email_look.php?beginser=61&endser=122&beginlogin=1513353600&endlogin=1513612799&excel=0
 */
ini_set("display_errors","On");
error_reporting(E_ALL);
require_once dirname( dirname( __FILE__ ) ) . '/common.inc.php';
$params = $_REQUEST;
//导出csv
if($params['excel']) {
    header("content-type:text/csv; charset=UTF-8");
    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=rank.csv");
    header("Pragma: no-cache");
}

$beginser = isset($params['beginser']) && is_numeric($params['beginser']) ? intval($params['beginser']) : 0;
$endser = isset($params['endser']) && is_numeric($params['endser']) ? intval($params['endser']) : 0;
$beginlogin = isset($params['beginlogin']) && is_numeric($params['beginlogin']) ? $params['beginlogin'] : 0;
$endlogin = isset($params['endlogin']) && is_numeric($params['endlogin']) ? $params['endlogin'] : 0;
if($beginser == 0 || $endser == 0){
    exit('请同时携带 beginser 和 endser 整数型参数');
}
if($beginlogin == 0 || $endlogin == 0){
    exit('请同时携带 beginlogin 和 endlogin 整数型参数');
}
set_time_limit(0);
Common::loadModel('ServerModel');
$serverList = ServerModel::getServList();
$data = array();
if (is_array($serverList)) {
    foreach ($serverList as $k => $v) {
        if (empty($v)) {
            continue;
        }
        if (!($v['id'] >= $beginser && $v['id'] <= $endser)) {
            continue;
        }

        $SevidCfg1 = Common::getSevidCfg($v['id']);//子服ID
        if (999 == $SevidCfg1['sevid']) {
            continue;
        }
        if (0 < $serverID && $serverID != $SevidCfg1['sevid']) {
            continue;
        }
        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0 && $SevidCfg1['sevid'] > PASS_SEV_CRONTAB_MAXID) {
            continue;
        }
        $db = Common::getMyDb();
        $sql = "SELECT DISTINCT `roleid` FROM `t_order` WHERE `ptime`>={$beginlogin} AND `ptime`<={$endlogin}";
        $result = $db->fetchArray($sql);
        if (!empty($result)){
            foreach ($result as $rk => $rv){
                $uid[] = $rv['roleid'];
            }
        }
        unset($result);
        if (!empty($uid)){
            $uids = implode(',', $uid);
        }
        $table_div = Common::get_table_div();
        for ($i = 0; $i < $table_div; $i++) {
            $table = 'user_' . Common::computeTableId($i);
            $sql = "SELECT `uid`,`name`,`vip`,`lastlogin` FROM {$table} WHERE `uid` IN (".$uids.")";
            $result = $db->fetchArray($sql);
            $data = array_merge($data, $result);
            unset($result);
        }
        unset($uids, $uid);
    }
}
?>
<?php
if(!empty($data)){
    if($params['excel']) {
        echo "玩家uid,玩家昵称,vip等级,最后登录时间\n";
        foreach ($data as $k=>$val){
            $lastlogin = date("Y-m-d H:i:s", $val['lastlogin']);
            $str = <<<SQL
    {$val['uid']},{$val['name']},{$val['vip']},{$lastlogin}\n
SQL;
            echo $str;
        }
    }else{
    ?>
    <table>
        <tr>
            <th>玩家uid</th>
            <th>玩家昵称</th>
            <th>vip等级</th>
            <th>最后登录时间</th>
        </tr>
        <?php foreach($data as $k => $val){?>
                <tr style="background-color:#f6f9f3">
                    <td style="text-align:center;"><?php echo $val['uid'];?></td>
                    <td style="text-align:center;"><?php echo $val['name']; ?></td>
                    <td style="text-align:center;"><?php echo $val['vip']; ?></td>
                    <td style="text-align:center;"><?php echo date("Y-m-d H:i:s", $val['lastlogin']); ?></td>
                </tr>
        <?php } ?>
    </table>
<?php  }}?>