<?php
/**
 * 合并取名表
 * User: Administrator
 * Date: 2017/11/7
 * Time: 11:06
 */

set_time_limit(0);
ini_set('memory_limit','1500M');

require_once 'common.php';
Common::loadModel('MailModel');
//获取当前区服
$serverID = intval($_SERVER['argv'][1]);
if(empty($serverID)){
    echo '未输入需要遍历的区服',PHP_EOL;exit();
}
$SevCfg = Common::getSevidCfg($serverID);

if($SevCfg['sevid'] != $SevCfg['he']){
    $mUse = memory_get_usage();
    $my_db = Common::getDbBySevId($SevCfg['sevid']);
    $he_db = Common::getDbBySevId($SevCfg['he']);
    $my_sql = "select name,uid from `index_name`";
    $sql = "select name from `index_name`";
    //本服名字集合
    $my_data = $my_db->fetchArray($my_sql);
    //合服名字集合
    $he_data = $he_db->fetchArray($sql);
    $he_list = array();
    foreach ($he_data as $val){
        $he_list[strtolower(trim($val['name']))] = 1;
        unset($val);
    }
    unset($he_data);
    if(!empty($my_data)){
        $value = '';
        $unique = array();
        foreach ($my_data as $val) {
            $val['name'] = trim($val['name']);
            $c = 0;
            $state = true;
            while ($state && $c < 100) {
                if (isset($he_list[strtolower($val['name'])])) {
                    if ($c == 0) {
                        $val['name'] .= 's' . $SevCfg['sevid'];
                    } else {
                        $val['name'] .= 'r' . rand(1, 100);
                    }
                    $unique[$val['uid']] = $val['name'];
                } else {
                    $he_list[$val['name']] = 1;
                    $state = false;
                }
            }
            $value .= "('{$val['name']}',{$val['uid']}),";
            unset($val);
        }

        $value = rtrim($value, ',');
        $sql = "insert into `index_name` values {$value}";
        unset($my_data,$he_list);
        if($he_db->query($sql) === false){
            echo $sql,PHP_EOL;
            echo '插入失败',PHP_EOL;
        }else{
            echo '插入成功',PHP_EOL;

            if(!empty($unique)) {
                //发放改名卡
                $item = array(array(
                    'id' => 115,
                    'count' => 1,
                    'kind' => 1
                ));
                $m_t = "合服-重名修改";//邮件标题
                $m_c = "因您的名字和所合并服务器中的其他玩家名字出现重名，特奉上更名令一张，请在道具栏中使用进行更名。";//邮件内容
                foreach ($unique as $uid => $name) {
                    $mailModel = new MailModel($uid);
                    $mailModel->sendMail($uid, $m_t, $m_c, 1, $item);
                    $mailModel->destroy();
                    $UserModel = Master::getUser($uid);
                    $s_update = array(
                        'name' => $name,
                    );
                    $UserModel->update($s_update);
                    $UserModel->destroy();
                    unset($uid, $mailModel,$s_update,$UserModel);
                }
                unset($unique);
            }
        }
    }

    echo "当前内存使用:".(memory_get_usage() - $mUse),PHP_EOL;
}else{
    echo "当前服务器和合服id一致",PHP_EOL;
}

