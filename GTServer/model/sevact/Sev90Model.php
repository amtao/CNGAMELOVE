<?php
/*
 * 新版公告
 */
require_once "SevListBaseModel.php";

class Sev90Model extends SevBaseModel
{
    public $comment = "新版公告";
    public $b_mol = "notice";//返回信息 所在模块
    public $b_ctrl = "listNew";//返回信息 所在控制器
    public $act = 90;//活动标签
    public $_init = array(//初始化数据

    );

    /**
     * 构造输出
     * $uid 玩家id
     * $isNotice 1:只开启新版自动生成活动公告 2:只开启新版配置公告 3:生成新版公告
     */
    public function get_notice($uid, $isNotice)
    {

        //当前服务器id
        $SevidCfg = Common::getSevidCfg();
        $sevid = $SevidCfg['he'];

        Common::loadModel('HoutaiModel');
        Common::loadVoComModel('ComVoComModel');

        $notice = array();  //存放所有公告

        $rk = 1; //自增 用于排序

        //---------------------------自动生成活动公告------------------------
        //1:只开启新版自动生成活动公告 2:只开启新版配置公告 3:生成新版公告
        if ($isNotice == 1 || $isNotice == 3) {
            //活动公告内容
            $ComVoComModel = new ComVoComModel('notice');
            $content = $ComVoComModel->getValue();

            //当前服务器 - 生效列表信息
            $cache = Common::getDftMem();
            $hdList = $cache->get(HoutaiModel::get_huodong_list_key($sevid));

            if (is_array($hdList) && !emptyempty($hdList)) {
                //活动公告
                foreach ($hdList as $hdk => $hdv) {
                    if (empty($content[$hdv['id']])) {
                        continue;
                    }
                    $rk++;
                    $top = empty($content[$hdv['id']]['top']) ? 1 : $content[$hdv['id']]['top'];
                    $notice[$top * 10000 + $rk] = array(
                        'header' => $content[$hdv['id']]['header'],
                        'title' => $content[$hdv['id']]['title'],
                        'body' => $content[$hdv['id']]['body'],
                    );
                }
            }

        }

        //---------------------------新版配置公告------------------------
        //1:只开启新版自动生成活动公告 2:只开启新版配置公告 3:生成新版公告
        if ($isNotice == 2 || $isNotice == 3) {
            //新版配置
            $ComVoComModel = new ComVoComModel('config');
            $show = $ComVoComModel->getValue();
            if (!empty($show))
                foreach ($show as $value) {

                    //未开启期间过滤    小于开始 大于结束
                    if (Game::dis_over(strtotime($value['sTime'])) > 0 || Game::is_over(strtotime($value['eTime']))) {
                        continue;
                    }

                    //过滤非本服公告
                    if ($value['serv'] != 'all') {
                        $servs = Game::serves_str_arr($value['serv']);
                        if (!in_array($sevid, $servs)) {
                            continue;
                        }
                    }

                    //我的平台
                    $UserModel = Master::getUser($uid);
                    $mypf = $UserModel->info['platform'];


                    //平台处理 -- 包含
                    if (!empty($value['include'])) {
                        $include = array();
                        $include = explode(',', $value['include']);
                        if (!in_array($mypf, $include)) {
                            continue;
                        }
                    }

                    //平台处理 -- 不包含
                    if (!empty($value['exclusive'])) {
                        $exclusive = array();
                        $exclusive = explode(',', $value['exclusive']);
                        if (in_array($mypf, $exclusive)) {
                            continue;
                        }
                    }

                    //添加公告
                    $rk++;
                    $top = empty($value['top']) ? 1 : $value['top'];
                    $notice[$top * 10000 + $rk] = array(
                        'header' => $value['header'],
                        'title' => $value['title'],
                        'body' => $value['body'],
                    );

                }
        }
        if (!empty($notice)) {
            krsort($notice);
        }
        return $notice;

    }

    public function out_back($uid, $isNotice)
    {

        $SevidCfg = Common::getSevidCfg();

        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel('ver');
        $verInfo = $ComVoComModel->getValue();

        //我的平台
        $UserModel = Master::getUser($uid);
        $mypf = $UserModel->info['platform'];

        //版本,用于更新,来源于后台操作记录时间戳
        $ver = empty($verInfo['v']) ? 0 : $verInfo['v'];
        $key = 'new_gg_' . $this->hid . '_' . $ver . '_' . $isNotice . '_' . $mypf;

        $cache = Common::getCacheBySevId($SevidCfg['he']);
        $list = $cache->get($key);//缓存获取活动信息

        if (empty($list)) {
            $list = self::get_notice($uid, $isNotice);//公告
            $cache->set($key, $list, 60);
        }

        if (!empty($list)) {
            $data = array_values($list);
            Master::back_data($uid, $this->b_mol, $this->b_ctrl, $data);
        }
    }

}
