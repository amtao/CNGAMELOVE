<?php

/*
 *
 * SevModel合并
 * */
class severCom{

    protected $seract = array(
        //需合并的数据
        2,//世界BOSS葛二蛋 血量想加？
        5,//历代王爷
        8,//翰林院总列表信息
        9,//翰林院房间座位信息
        10,//联盟-每日贡献列表信息
        11,//联盟-申请列表
        12,//联盟-boss血量
        13,//联盟-战报信息(boss未被击杀)
        14,//战报列表(boss被击杀)
        15,//联盟日志
        20,//酒楼-家宴-联盟可见
        21,//酒楼-官宴-全服可见
        23,//聊天禁言
        24,//聊天-工会频道
        26,//聊天封号
        29,//酒楼-家宴-全服可见
        35,//聊天玩家GM系统
        //需删除的数据
        4,//葛二蛋击杀记录
        22,//聊天-公共频道
        38,//聊天-公共频道 - 隐性
        31,//全服邮件列表
        //不操作
        1,//子嗣全服提亲列表
        3,//蒙古战斗道具奖励日志
        6,//衙门战击败20榜
        25,//聊天-跨服频道
        27,//聊天封设备
        28,//聊天 - 敏感过滤
        29,//跨服聊天禁言
        30,//新官上任鞭打道具奖励日志
        32,//新官上任-boss血量
        36,//惩戒来福-boss血量
        37,//惩戒来福-奖励日志
        40,//狩猎
        43,//国庆活动-boss血量
        44,//国庆活动-奖励日志
        45,//重阳节活动-boss血量
        46,//重阳节活动-奖励日志
        47,//聊天-跑马灯
        48,//跑马灯-跑脚本记录
        50,//跨服帮会战
        51,
        52,
        53,
        54,
        55,
        56,
        57,
        60,//跨服衙门战
        61,
        62,
        24,  //宫殿聊天
        6012,//聊天-系统频道
        6013,//聊天-本服聊天滞留
        6190,//御花园-世界树
    );

    /*
     * 合并
     * */
    public function merge($SevCfg){
        if($SevCfg['he'] != $SevCfg['sevid']) {
            foreach ($this->seract as $sever) {
                $db = Common::getDbBySevId($SevCfg['sevid']);
                $sql = "select * from `sev_act` where `key`={$sever}";
                switch ($sever) {
                    //需合并的数据
                    case 2://世界BOSS葛二蛋 血量想加？
                        $data = $db->fetchRow($sql);
                        if(!empty($data)){
                            $info = json_decode($data['value'],true);
                            if(empty($info)) break;
                            $Sev2Model = Master::getSev2();
                            if($Sev2Model->info['day'] == $info['day']){//时间相同时想加 不相同不管了
                                $Sev2Model->info['allhp'] += $info['allhp'];
                                $Sev2Model->save();
                            }
                            unset($data,$info,$Sev2Model);
                        }
                        break;
                    case 5://历代王爷 需要特殊处理  合区未加
                        $data = $db->fetchRow($sql);
                        if(empty($data['value'])) break;
                        $info = json_decode($data['value'],true);
                        if(empty($info)) break;
                        $Sev5Model = Master::getSev5();
                        foreach ($info as $wid => $wang){
                            if(empty($Sev5Model->info[$wid])){
                                $Sev5Model->info[$wid] = array();
                            }
                            $Sev5Model->info[$wid] = array_merge($Sev5Model->info[$wid],$wang);
                        }
                        $Sev5Model->save();
                        unset($data,$info,$Sev5Model);
                        break;
                    case 8://翰林院总列表信息
                        $data = $db->fetchRow($sql);
                        if(empty($data['value'])) break;
                        $info = json_decode($data['value'],true);
                        if(empty($info)) break;
                        $Sev8Model = Master::getSev8();
                        foreach ($info as $uid => $val){
                            $Sev8Model->info[$uid] = $val;
                            unset($uid,$val);
                        }
                        $Sev8Model->save();
                        unset($info,$data,$Sev8Model);
                        break;
                    case 9://翰林院房间座位信息
                        $data = $db->fetchArray($sql);
                        if(empty($data)) break;
                        foreach ($data as $val){
                            if(empty($val['value'])) continue;
                            $info = json_decode($val['value'],true);
                            if(empty($info) || Game::is_over($info['master']['num'])){//已过期不管
                                continue;
                            }
                            $Sev9Model = Master::getSev9($val['hcid']);
                            $Sev9Model->info = $info;
                            $Sev9Model->save();
                            unset($val,$info,$Sev9Model);
                        }
                        unset($data);
                        break;
                    case 10://联盟-每日贡献列表信息
                    case 12://联盟-boss血量
                    case 13://联盟-战报信息(boss未被击杀)
                    case 14://战报列表(boss被击杀)
                    case 15://联盟日志
                        $data = $db->fetchArray($sql);
                        if(empty($data)) break;
                        $Model = 'getSev'.$sever;
                        foreach ($data as $val){
                            if(empty($val['value'])) continue;
                            $info = json_decode($val['value'],true);
//                            if(empty($info) || Game::get_today_id() != $val['did']){//不是今日的不要
//                                continue;
//                            }
                            $SevModel = Master::$Model($val['hcid']);
                            $SevModel->info = $info;
                            $SevModel->save();
                            unset($val,$info,$SevModel);
                        }
                        unset($data,$Model);
                        break;
                    case 11://联盟-申请列表
                        $data = $db->fetchRow($sql);
                        if(empty($data['value'])) break;
                        $info = json_decode($data['value'],true);
                        if(empty($info)) break;
                        $Sev11Model = Master::getSev11();
                        foreach ($info as $uid => $val){
                            if(!empty($val)){
                                $Sev11Model->info[$uid] = $val;
                            }
                            unset($uid,$val);
                        }
                        $Sev11Model->save();
                        unset($info,$data,$Sev11Model);
                        break;
                    case 20://酒楼-家宴-联盟可见
                    case 24: //联盟聊天
                        $data = $db->fetchArray($sql);
                        if(empty($data)) break;
                        $model = 'getSev'.$sever;
                        foreach ($data as $val){
                            if(empty($val['value'])) continue;
                            $info = json_decode($val['value'],true);
                            if(empty($info)){//空的不管
                                continue;
                            }
                            $SevModel = Master::$model($val['hcid']);
                            $SevModel->info = $info;
                            $SevModel->save();
                            unset($val,$info,$SevModel);
                        }
                        unset($data,$model);
                        break;
                    case 21://酒楼-官宴-全服可见
                    case 23://聊天禁言
                    case 29://酒楼-家宴-全服可见
                        $data = $db->fetchRow($sql);
                        if(!empty($data['value'])){
                            $info = json_decode($data['value'],true);
                            if(empty($info)) break;
                            $Model = 'getSev'.$sever;
                            $SevModel = Master::$Model();
                            foreach ($info as $uid => $val){
                                $SevModel->info[$uid] = $val;
                                unset($uid,$val);
                            }
                            $SevModel->save();
                            unset($info,$SevModel,$data);
                        }
                        break;
                    case 26://聊天封号
                        $data = $db->fetchRow($sql);
                        if(!empty($data['value'])){
                            $info = json_decode($data['value'],true);
                            if(empty($info)) break;
                            $Sev26Model = Master::getSev26();
                            foreach ($info as $uid => $time){
                                $he_id = Common::getSevCfgObj(Game::get_sevid($uid));
                                if($he_id == $SevCfg['he']){//去除后台添加无用的封号号码
                                    $Sev26Model->info[$uid] = $time;
                                }
                                unset($uid,$time,$he_id);
                            }
                            $Sev26Model->save();
                            unset($data,$info,$Sev26Model);
                        }
                        break;
                    case 35://聊天玩家GM系统
                        $data = $db->fetchRow($sql);
                        if(!empty($data['value'])){
                            $info = json_decode($data['value'],true);
                            if(empty($info)) break;
                            $Sev35Model = Master::getSev35();
                            foreach ($info as $val){
                                $Sev35Model->info[] = $val;
                                unset($val);
                            }
                            $Sev35Model->save();
                            unset($data,$info,$Sev35Model);
                        }
                        break;
                    case 6190://御花园-世界树
                        $data = $db->fetchRow($sql);
                        if(!empty($data['value'])){
                            $info = json_decode($data['value'],true);
                            if(empty($info)) break;
                            $Sev6190Model = Master::getSev6190();
                            foreach ($info as $key =>$val){
                                $Sev6190Model->info[$key] = $val;
                                unset($val);
                            }
                            $Sev6190Model->save();
                            unset($data,$info,$Sev6190Model);
                        }
                        break;
                    default://其他的不操作
                        break;
                }
            }
        }else{//当前区是合区的情况
            foreach ($this->seract as $sever) {
                switch ($sever) {
                    case 5://历代王爷 处理当前是合区的情况
                        $cache = Common::getCacheBySevId($SevCfg['sevid']);
                        $cache->delete('sevact_5_1');
                        $cache->delete('sevact_5_1_1_msg');
                        break;
                    //需删除的数据
                    case 4://葛二蛋击杀记录
                    case 22://聊天-
                    case 24://聊天-公共频道
                        $my_redis = Common::getRedisBySevId($SevCfg['sevid']);
                        $rdata  = $my_redis->zRevRange('club_redis', 0, -1,true);  //获取排行数据
                        $model = 'getSev'.$sever;
                        if (!empty($rdata)){
                            foreach ($rdata as $cid =>$val){
                                $SevModel = Master::$model($cid);
                                $SevModel->info = array();
                                $SevModel->save();
                            }
                        }
                        unset($SevModel,$model);
                        break;
                    case 38://聊天-公共频道 - 隐性
                    case 31://全服邮件列表
                    case 6012://聊天-系统频道
                    case 6013://聊天-本服聊天滞留
                        $model = 'getSev'.$sever;
                        $SevModel = Master::$model();
                        $SevModel->info = array();
                        $SevModel->save();
                        unset($SevModel,$model);
                        break;
                    default:
                        break;
                }
            }
        }
    }

}