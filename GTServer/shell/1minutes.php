<?php
/**
 * 后台配置文件脚本
 * 调用方式：每分钟跑一次
 *
 */
set_time_limit(0);
require_once dirname(__FILE__) . '/../public/common.inc.php';
Common::loadModel('ClubModel');
Common::loadModel('ServerModel');
Common::loadModel('HoutaiModel');
$serverID = intval($_SERVER['argv'][1]);// 默认是全部区
$serverList = ServerModel::getServList();
$btime = microtime(true);
echo PHP_EOL, '当前时间', date('y-m-d h:i:s',$_SERVER['REQUEST_TIME']), PHP_EOL;
if ( is_array($serverList) ) {

    foreach ($serverList as $k => $v) {
        if ( empty($v) ) {
            continue;
        }
        $Sev_Cfg = Common::getSevidCfg($v['id']);//子服ID

        echo PHP_EOL, '服务器ID：', $Sev_Cfg['sevid'], PHP_EOL;

        if ( !( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) && 999 == $Sev_Cfg['sevid'] ) {
            echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }
        if ( 0 < $serverID && $serverID != $Sev_Cfg['sevid'] ) {
            echo PHP_EOL, '>>>跳过', PHP_EOL;
            continue;
        }

        if (defined('PASS_SEV_CRONTAB_MAXID') && PASS_SEV_CRONTAB_MAXID > 0
            && $Sev_Cfg['sevid'] > PASS_SEV_CRONTAB_MAXID) {
            echo PHP_EOL, '>>>从服跳过', PHP_EOL;
            continue;
        }

        $open_day = ServerModel::isOpen($Sev_Cfg['sevid']);
        //过滤未开服的
        if($open_day <= 0){
            continue;
        }
        echo '生效时间'.$open_day."\n";
        son_name();
    }
}

exit();


function son_name(){
    $db = Common::getMyDb();
    for($i = 0;$i<100;$i++){
        if($i < 10){
            $table = 'son_0'.$i;
        }else{
            $table = 'son_'.$i;
        }
        $sql = "select `uid`,`sonuid`,`name` from {$table} where `name` like '%\\\';";
        $data = $db->fetchArray($sql);
        echo mysql_error();
        foreach ($data as $v){
            echo $v['uid'],">>>>",$v['sonuid'],">>>>",$v['name'];
            $name = str_replace("\\",'',$v['name']);
            $sql = "update {$table} set `name` = {$name} where `uid` = {$v['uid']} and `sonuid` = {$v['sonuid']};";
            $db->query($sql);
        }
    }

}
/**
 * 跑马灯脚本
 */
function paomadeng()
{
    echo PHP_EOL, '>>>>>>跑马灯脚本开始>>>>>>>', PHP_EOL;
    $Sev47Model = Master::getSev47();
    $Sev48Model = Master::getSev48();
    $Sev47Model->delect_msg();
    $base_pz = HoutaiModel::get_base_pz('zoumadeng');
    if(empty($base_pz)){
        $base_pz = HoutaiModel::get_all_pz('zoumadeng');
    }
    if(!empty($base_pz)){
        foreach ($base_pz as $k => $v) {
            //判断是否已发送
            if ($Sev47Model->info[$v['id']]['msg'] == $v['content']) {
                echo PHP_EOL, '已经发送跑马灯信息，ID:'.$v['id'].'  发送时间：'.$v['sendTime'], PHP_EOL;
                continue;
            }
            $nowtime = date('Y-m-d H:i:s', Game::get_now());//当前时间2001-10-11 10:10:10
            $DayTime = date('h:i:s', Game::get_now());//当前时间 10:10:10
            $startTime = $v['startTime'];//生效日期开始
            $endTime = $v['endTime'];//生效日期结束
            $sendTime = $v['sendTime'];//当日发送时间
            $expire = strtotime($v['expire']);//过期时间
            $content = $v['content'];
            if ($startTime < $nowtime and $nowtime < $endTime) {//判断是否生效中
                if ($sendTime < $DayTime) {//判断是否在当日发送时间段
                    $Sev47Model->add_msg($content,$expire,$v['id']);//发送信息
                    //$Sev48Model->add_id($v['id']);//记录ID
                    echo PHP_EOL, '发送跑马灯信息，ID:'.$v['id'].'  发送时间：'.$sendTime,PHP_EOL;
                }
            }

        }
    }

    echo PHP_EOL, '>>>>>>跑马灯脚本结束>>>>>>>', PHP_EOL;
}

/*
 * 更改微信字段类型
 */
function chance_weixin($sevid){
    $db = Common::getMyDb();
    $sql = "ALTER TABLE `club` MODIFY COLUMN `weixin` VARCHAR (32) DEFAULT '' ";
    if($db->query($sql)){
        echo PHP_EOL, '更改服务器ID'.$sevid.'  更改CLUB表微信字段类型成功',PHP_EOL;
    }else{
        echo PHP_EOL, '更改服务器ID'.$sevid.'  更改CLUB表微信字段类型失败',PHP_EOL;
    }
}

/*
 * 通过平台ID 查找 UserID
 * 构造结构$k = pid,$v=level
 */
function pidToLevel($users)
{

    $db = Common::getMyDb();
    //完善输出列表
    foreach ($users as $k => $v) {
        $sql = "select `uid` from `gm_sharding` where `ustr` = {$k}";
        $user = $db->fetchRow($sql);
        if ($user) {
            //根据UID列表查询UID对应的最大等级
            $userModel = Master::getUser($user['uid']);
            if ($users[$k] < $userModel->info['level']) {
                $users[$k] = $userModel->info['level'];
            }
        } else {
            continue;
        }
    }
    return $users;
}

/*
 * 根据桌位购买记录更新act数据
 */
function shuyuan($level,$count){
    $stime = time();
    $db = Common::getMyDb('flow');
    $db2 = Common::getMyDb();
    for ($i=0;$i<100;$i++) {
        if($i < 10){
            $table = 'flow_event_0'.$i;
        }else{
            $table = 'flow_event_'.$i;
        }
        $sql = 'select `uid`,count(id) as num from `'.$table.'` 
        where `model` = "school" and `ctrl` = "buydesk" group by `uid` having count(id)>'.$count.';';
        $userArray = $db->fetchArray($sql);
        if(empty($userArray)){continue;}
        foreach ($userArray as $v) {
            //act_table
            $table_id = $v['uid']%100;
            $table = $table_id>=10?'act_'.$table_id:'act_0'.$table_id;
            $sql = 'select `tjson` from '.$table.' where `actid` = 15 and `uid` = '.$v['uid'];
            $act_data = $db2->fetchRow($sql);//获取数据
            if (empty($act_data)) {continue;}
            $json = json_decode($act_data['tjson'], true);
            if($json['data']['desk'] - $v['num'] >= 1){continue;}

            //更新到最新
            $json['data']['desk'] = $v['num'] +1;
            $new_json = json_encode($json, JSON_UNESCAPED_UNICODE);
            $sql = "update {$table} set `tjson`='{$new_json}' where `actid`=15 and `uid`={$v['uid']};";
            if ($db2->query($sql)) {
                $cache = Common::getCacheByUid($v['uid']);
                $key = $v['uid'].'_act_15';
                $cache->delete($key);
                echo "uid:".$v['uid'].",num:".$json['data']['desk'].PHP_EOL;
            }
            unset($act_data, $json, $new_json, $sql);
        }
        unset($sql, $userArray);
    }
    echo '耗时：'.(time()-$stime);
}

function son($level,$count){
    $stime = time();
    $db = Common::getMyDb('flow');
    $db2 = Common::getMyDb();
    for ($i=0;$i<100;$i++) {
        if($i < 10){
            $table = 'flow_event_0'.$i;
        }else{
            $table = 'flow_event_'.$i;
        }
        $sql = 'select `uid`,count(id) as num from `'.$table.'` 
        where `model` = "son" and `ctrl` = "buyseat" group by `uid` having count(id)>'.$count.';';
        $userArray = $db->fetchArray($sql);
        if(empty($userArray)){continue;}
        foreach ($userArray as $v) {
            //act_table
            $table_id = $v['uid']%100;
            $table = $table_id>=10?'act_'.$table_id:'act_0'.$table_id;
            $sql = 'select `tjson` from '.$table.' where `actid` = 12 and `uid` = '.$v['uid'];
            $act_data = $db2->fetchRow($sql);//获取数据

            if (empty($act_data)) {continue;}
            $json = json_decode($act_data['tjson'], true);
            if($json['data']['seat'] - $v['num'] >= 2){continue;}

            //更新到最新
            $json['data']['seat'] = $v['num'] +2;
            $new_json = json_encode($json, JSON_UNESCAPED_UNICODE);
            $sql = "update {$table} set `tjson`='{$new_json}' where `actid`=12 and `uid`={$v['uid']};";
            if ($db2->query($sql)) {
                $cache = Common::getCacheByUid($v['uid']);
                $key = $v['uid'].'_act_12';
                $cache->delete($key);
                echo "uid:".$v['uid'].",num:".$json['data']['seat'].PHP_EOL;
            }
            //echo "uid:".$v['uid'].",num:".$json['data']['seat'].PHP_EOL;
            unset($act_data, $json, $new_json, $sql);
        }
        unset($sql, $userArray);
    }
    echo '耗时：'.(time()-$stime);
}

function son_backslash(){
    $db = Common::getMyDb();
    $sql = "select `uid`,`name` from `son_00` like '%\\\%' ;";
    echo $sql,PHP_EOL;
    $data = $db->fetchArray($sql);
    echo mysql_error();
    foreach ($data as $v){
        echo $v['uid'],':',$v['name'],PHP_EOL;
    }
}

function test(){
    $value = array(
		'header' => array(
			'word' =>'',
			'font' => 25,
			'size' => 'c3b',
			'color' => 255,255,255,
		),
		'header' => array(
			'word' => '温馨公告',
			'font' => 25,
			'size' => 'c3b',
			'color' =>255,255,255 ,
		),
		'body' => array(
			0 => array(
				'word' => '温馨公告',
				'font' => 25,
				'size' => 'c3b',
				'color' =>255,255,255 ,
			),
			1 => array(
				'word' => '温馨公告',
				'font' => 25,
				'size' => 'c3b',
				'color' =>255,255,255 ,
			),
		)
	);
    Common::loadModel('HoutaiModel');
    HoutaiModel::write_googgao_peizhi('test',$value);
}


function delete_club(){

    $db = Common::getMyDb();
    $sql = "select `cid` from `club` where `members` = ' '  or `members` = '[]' or `members` is null ;";
    $data = $db->fetchArray($sql);
    foreach ($data as $k => $v){
        echo '联盟为空：',$v['cid'],PHP_EOL;
//        $clubModel = new ClubModel($v['cid']);
//        $clubModel->del_club($v['cid']);
//        unset($clubModel);
    }
    echo mysql_error();
}