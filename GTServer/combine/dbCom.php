<?php
/*
 * 合并数据库
 * */
class dbCom{
    protected $table = array(
        'son_marry',
        'club'
    );
    /*
     * 合并数据库
     * */
    public function modifyDb($SevCfg){

        if($SevCfg['sevid'] == $SevCfg['he']){//插入联盟信息
            $he_db = Common::getDftDb();
            $sql = "select cid,name from `club`";
            $he_data = $he_db->fetchArray($sql);
            $club_name_value = '';
            if (!empty($he_data)) {
                foreach ($he_data as $val) {
                    $club_name_value .= "('{$val['name']}','{$val['cid']}'),";
                    unset($val);
                }
                $club_name_value = rtrim($club_name_value, ',');
                $sql = "insert into `club_name` values {$club_name_value}";
                echo $sql, PHP_EOL;
                $rt = $he_db->query($sql);
                if (empty($rt)) {
                    echo '插入新的sql失败', PHP_EOL;
                } else {
                    echo '插入新的sql成功', PHP_EOL;
                }
            }
        }else {
            $my_db = Common::getMyDb();
            $he_db = Common::getDftDb();
            foreach ($this->table as $t) {
                switch ($t) {
                    case 'son_marry':
                        $sql = "select * from `son_marry`";
                        $data = $my_db->fetchArray($sql);
                        if (!empty($data)) {
                            $valus = "";
                            $count = count($data);
                            foreach ($data as $k => $val) {
                                if (($k + 1) == $count) {
                                    $valus .= "('" . implode("','", $val) . "')";
                                } else {
                                    $valus .= "('" . implode("','", $val) . "'),";
                                }
                            }
                            $i_sql = "insert into `son_marry` values {$valus}";
                            if ($he_db->query($i_sql)) {
                                echo "提亲数据库合并成功", PHP_EOL;
                            } else {
                                echo "提亲数据库合并失败", PHP_EOL;
                            }
                        }
                        break;
                    case 'club'://相同改名
                        $sql = "select cid,name from `club`";
                        $he_sql = "select name,cid from `club_name`";
                        $my_data = $my_db->fetchArray($sql);
                        $he_data = $he_db->fetchArray($he_sql);
                        //数据组合
                        $my_list = array();
                        $he_list = array();
                        if (!empty($my_data)) {
                            foreach ($my_data as $val) {
                                $my_list[$val['cid']] = $val['name'];
                                unset($val);
                            }
                        }
                        if (!empty($he_data)) {
                            foreach ($he_data as $val) {
                                $he_list[$val['cid']] = $val['name'];
                                unset($val);
                            }
                        }
                        unset($my_data, $he_data);

                        $cid_list = array_keys(array_intersect($my_list, $he_list));
                        $where = "('" . implode("','", $cid_list) . "')";
                        $after = 's' . $SevCfg['sevid'];
                        $i_sql = "update `club` set name = concat(name,'{$after}') WHERE cid in {$where}";
                        if ($my_db->query($i_sql) === false) {
                            echo "联盟改名失败", PHP_EOL;
                        } else {
                            echo "联盟改名成功", PHP_EOL;
                            //向需要改名的联盟发放联盟改名卡
                            Common::loadModel('MailModel');
                            foreach ($cid_list as $cid) {
                                $ClubModel = Master::getClub($cid);
                                foreach ($ClubModel->info['members'] as $uid => $member){
                                    if(in_array($member['post'],array(1,2))){
                                        $mailModel = new MailModel($uid);
                                        $mailModel->sendMail($uid,'合服-宫殿重名修改','因您的宫殿名字和所合并服务器中的其他玩家的宫殿名字出现重名，特奉上更名令一张，请在道具栏中使用进行更名。',1,array(array('id'=>140,'count'=>1,'kind'=>1)));
                                        $mailModel->destroy();
                                    }
                                    unset($uid,$member);
                                }
                                $ClubModel->delete_cache();
                                unset($ClubModel,$cid);
                            }
                            unset($cid_list);
                            $my_data = $my_db->fetchArray($sql);
                            $club_name_value = '';
                            if (!empty($my_data)) {
                                foreach ($my_data as $val) {
                                    $club_name_value .= "('{$val['name']}','{$val['cid']}'),";
                                }
                                unset($my_data);
                            }
                            $club_name_value = rtrim($club_name_value, ',');
                            $sql = "insert into `club_name` values {$club_name_value}";
                            echo $sql, PHP_EOL;
                            $rt = $he_db->query($sql);
                            if (empty($rt)) {
                                echo '插入新的sql失败', PHP_EOL;
                            } else {
                                echo '插入新的sql成功', PHP_EOL;
                            }
                        }
                        break;
                    default:
                        echo "输入的表暂时不需要修改", PHP_EOL;
                        break;
                }
            }
        }
    }
}