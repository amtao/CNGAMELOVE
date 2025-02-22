<?php
class GameAct
{
	public function __construct()
	{
        ini_set("display_errors", "Off");
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
	}
	protected function _getTpl($fun)
    {
        return TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.$fun.'.php';
    }
	public function templateList()
    {
        Common::loadModel('GameActTemplateModel');
        $GameActTemplateModel = new GameActTemplateModel();
        if (isset($_REQUEST['del'])) {
            $id = Game::strval($_REQUEST, 'id');
            $idArr = explode('-', $id);
            foreach ($idArr as $id) {
                $GameActTemplateModel->delete($id);
            }
            $GameActTemplateModel->destroy();
        }
        if (isset($_REQUEST['import'])) {
            $templateList = include(ROOT_DIR . '/administrator/config/game_act_template.php');
            foreach ($templateList as $v) {
                $GameActTemplateModel->add(array(
                    'title'=>trim($v['title']),
                    'act_key'=>trim($v['act_key']),
                    'auser'=>trim($_SESSION["CURRENT_USER"]),
                    'atime'=>time(),
                    'contents'=>$v['contents'],
                ));
            }
            $GameActTemplateModel->destroy();
            $location = "location.href='?sevid={$_GET['sevid']}&mod=gameAct&act=templateList';";
            echo "<script>alert('提交成功！');{$location}</script>";
        }
        $list = $GameActTemplateModel->getAllInfo();
        include $this->_getTpl(__FUNCTION__);
    }
    public function exportTemplate()
    {
        Common::loadModel('GameActTemplateModel');
        $GameActTemplateModel = new GameActTemplateModel();
        if (isset($_REQUEST['export'])) {
            $id = Game::strval($_REQUEST, 'id');
            $idArr = explode('-', $id);
            $sqlArr = array();
            $list = $GameActTemplateModel->getAllInfo();
            foreach ($list as $list_v) {
                if (!in_array($list_v['id'], $idArr)) {continue;}
                $sqlArr[] = $GameActTemplateModel->getAddSql($list_v);
            }
            $filename = 'template_list.sql';
            header("Content-Type: application/octet-stream");
            if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']) ) {
                header('Content-Disposition:  attachment; filename="' . $filename . '"');
            } elseif (preg_match("/Firefox/", $_SERVER['HTTP_USER_AGENT'])) {
                header('Content-Disposition: attachment; filename*="utf8' .  $filename . '"');
            } else {
                header('Content-Disposition: attachment; filename="' .  $filename . '"');
            }
            echo implode("\n", $sqlArr);exit;
        }
    }
    public function viewTemplate()
    {
        $id = intval($_REQUEST['id']);
        Common::loadModel('GameActTemplateModel');
        $GameActTemplateModel = new GameActTemplateModel();
        $info = $GameActTemplateModel->getInfo($id);
        include $this->_getTpl(__FUNCTION__);
    }
    public function editTemplate()
    {
        $id = intval($_REQUEST['id']);
        Common::loadModel('GameActTemplateModel');
        $GameActTemplateModel = new GameActTemplateModel();
        $info = $GameActTemplateModel->getInfo($id);
        if (isset($_REQUEST['flag'])) {
            $msg = "修改成功";
            $contents = trim($_REQUEST['contents']);
            $contentsCheckRes = $this->_checkContents($contents);
            if ($contentsCheckRes['ok']) {
                $info['title'] = trim($_REQUEST['title']);
                $info['contents'] = $contents;
                $GameActTemplateModel->update($info);
                $GameActTemplateModel->destroy();
                $info = $GameActTemplateModel->getInfo($id);

                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, $info);
            }
            else {
                $msg = "修改失败：{$contentsCheckRes['result']}";
            }
        }
        include $this->_getTpl(__FUNCTION__);
    }
    public function addTemplate()
    {
        if (isset($_REQUEST['flag'])) {
            $contents = trim($_REQUEST['contents']);
            $contentsCheckRes = $this->_checkContents($contents);
            if ($contentsCheckRes['ok']) {
                Common::loadModel('GameActTemplateModel');
                $GameActTemplateModel = new GameActTemplateModel();
                $GameActTemplateModel->add(array(
                    'title'=>trim($_REQUEST['title']),
                    'act_key'=>trim($_REQUEST['act_key']),
                    'auser'=>trim($_SESSION["CURRENT_USER"]),
                    'atime'=>time(),
                    'contents'=>$contents,
                ));
                $GameActTemplateModel->destroy();
                $location = "location.href='?sevid={$_GET['sevid']}&mod=gameAct&act=templateList';";
                echo "<script>alert('提交成功！');{$location}</script>";
                exit;
            }
        }
        include $this->_getTpl(__FUNCTION__);
    }
    public function importTemplate()
    {
        if (isset($_REQUEST['flag'])) {
            $contents = trim($_REQUEST['contents']);
            $sqlArr = explode("\n", $contents);
            Common::loadModel('GameActTemplateModel');
            $GameActTemplateModel = new GameActTemplateModel();
            foreach ($sqlArr as $sql) {
                $GameActTemplateModel->addBySql($sql);
            }
            $location = "location.href='?sevid={$_GET['sevid']}&mod=gameAct&act=templateList';";
            echo "<script>alert('提交成功！');{$location}</script>";
        }
        include $this->_getTpl(__FUNCTION__);
    }
    public function importPassList()
    {
        if (isset($_REQUEST['flag'])) {
            $contents = trim($_REQUEST['contents']);
            $sqlArr = explode("\n", $contents);
            Common::loadModel('GameActModel');
            $GameActModel = new GameActModel();
            foreach ($sqlArr as $sql) {
                $GameActModel->addBySql($sql);
            }
            $location = "location.href='?sevid={$_GET['sevid']}&mod=gameAct&act=passList&auditType=0';";
            echo "<script>alert('提交成功！');{$location}</script>";
        }
        include $this->_getTpl(__FUNCTION__);
    }
    public function passList()
    {
        Common::loadModel('GameActModel');
        $GameActModel = new GameActModel();
        $location = "location.href='?sevid={$_GET['sevid']}&mod=gameAct&act=passList&auditType=1';";
        //审核
        if (isset($_REQUEST['audit'])) {

            $isConflict = false;
            $id = intval($_REQUEST['id']);
            $info = $GameActModel->getInfo($id);

            if ($info["server"] != 999) {

                $infoId = $info["contentsArr"][0]["info"]["id"];
                $pindex = $info["contentsArr"][0]["info"]["pindex"];
                $startDay = $info["contentsArr"][0]["info"]["startDay"];
                $endDay = $info["contentsArr"][0]["info"]["endDay"];
                $startTime = strtotime($info["contentsArr"][0]["info"]['startTime']);
                $endTime = !empty($info["contentsArr"][0]["info"]['shTime'])? strtotime($info["contentsArr"][0]["info"]['shTime']):strtotime($info["contentsArr"][0]["info"]['endTime']);

                $list = $GameActModel->getAllInfo();
                $serverList = ServerModel::getServList();
                foreach ($list as $lk => $lv) {

                    if ($lv["contentsArr"][0]["info"]["pindex"] != $pindex || $lv["contentsArr"][0]["info"]["id"] == $infoId || $lv["server"] == 999) {
                        continue;
                    }

                    $actId = $pindex." _ ".$lv["contentsArr"][0]["info"]['id'];

                    $sTime = strtotime($lv["contentsArr"][0]["info"]['startTime']);
                    $eTime = !empty($lv["contentsArr"][0]["info"]['shTime'])? strtotime($lv["contentsArr"][0]["info"]['shTime']):strtotime($lv["contentsArr"][0]["info"]['endTime']);

                    if ($startDay > 0 || $endDay > 0 || $lv["contentsArr"][0]["info"]['startDay'] > 0 || $lv["contentsArr"][0]["info"]['endDay'] > 0) {

                        foreach ($serverList as $sk => $sv) {

                            if ($startDay > 0 || $endDay > 0) {

                                $startTime = $startDay * 86400 + $sv["showtime"];
                                $endTime = $endDay * 86400 + $sv["showtime"];
                            }

                            if ($lv["contentsArr"][0]["info"]['startDay'] > 0 || $lv["contentsArr"][0]["info"]['endDay'] > 0) {

                                $sTime = $lv["contentsArr"][0]["info"]['startDay'] * 86400 + $sv["showtime"];
                                $eTime = $lv["contentsArr"][0]["info"]['endDay'] * 86400 + $sv["showtime"];
                            }

                            if ($startTime <= $eTime || ($endTime > $sTime && $endTime < $eTime)) {
                                $isConflict = true;
                                break;
                            }
                        }
                    }else{

                        if ($startTime <= $eTime || ($endTime > $sTime && $endTime <= $eTime)) {
                            $isConflict = true;
                            break;
                        }
                    }
                }
            }

            // if ($isConflict && ("korprerelease-gtmz.meogames.com" == $_SERVER ['HTTP_HOST'] || "gs-gtmz-admin.meogames.com" == $_SERVER ['HTTP_HOST'])) {
            //     $location = "location.href='?sevid={$_GET['sevid']}&mod=gameAct&act=passList&auditType=0';";
            //     echo "<script>alert('活动冲突!   {$actId}');{$location}</script>";
            // }else{

                $info['audit'] = intval($_REQUEST['audit']);
                $info['auser'] = trim($_SESSION["CURRENT_USER"]);
                $info['atime'] = time();
                $GameActModel->update($info);
                $GameActModel->destroy();
            // }
        }
        if (isset($_REQUEST['del'])) {
            if ($_GET['sevid']=='999'){
                $id = Game::strval($_REQUEST, 'id');
                $idArr = explode('-', $id);
                foreach ($idArr as $id) {
                    $GameActModel->delete($id);
                }
                $GameActModel->destroy();
            }else{
                $spd = md5('dimarts2020');
                $password = Game::strval($_REQUEST, 'password');
                $cpd =md5($password);
                if (!empty($cpd) && $cpd == $spd){
                    $id = Game::strval($_REQUEST, 'id');
                    $idArr = explode('-', $id);
                    foreach ($idArr as $id) {
                        $GameActModel->delete($id);
                    }
                    $GameActModel->destroy();
                    echo "<script>alert('删除成功!');{$location}</script>";
                }else{

                    echo "<script>alert('密码错误!');{$location}</script>";
                }
            }




        }

        $list = $GameActModel->getAllInfo();
        $lastChangeVer = GameActModel::getChangeVer();
        include $this->_getTpl(__FUNCTION__);
    }

    public function exportPassList()
    {
        Common::loadModel('GameActModel');
        $GameActModel = new GameActModel();
        if (isset($_REQUEST['export'])) {
            $id = Game::strval($_REQUEST, 'id');
            $idArr = explode('-', $id);
            $sqlArr = array();
            $list = $GameActModel->getAllInfo();
            foreach ($list as $list_v) {
                if (!in_array($list_v['id'], $idArr)) {continue;}
                $sqlArr[] = $GameActModel->getAddSql($list_v);
            }
            $filename = 'pass_list.sql';
            header("Content-Type: application/octet-stream");
            if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']) ) {
                header('Content-Disposition:  attachment; filename="' . $filename . '"');
            } elseif (preg_match("/Firefox/", $_SERVER['HTTP_USER_AGENT'])) {
                header('Content-Disposition: attachment; filename*="utf8' .  $filename . '"');
            } else {
                header('Content-Disposition: attachment; filename="' .  $filename . '"');
            }
            echo implode("\n", $sqlArr);exit;
        }
    }
    public function viewGameAct()
    {
        $id = intval($_REQUEST['id']);
        Common::loadModel('GameActModel');
        $GameActModel = new GameActModel();
        $info = $GameActModel->getInfo($id);
        include $this->_getTpl(__FUNCTION__);
    }
    public function editGameAct()
    {
        $id = intval($_REQUEST['id']);
        Common::loadModel('GameActModel');
        $GameActModel = new GameActModel();
        $info = $GameActModel->getInfo($id);
        if (isset($_REQUEST['flag'])) {
            $msg = "修改成功";
            $contents = trim($_REQUEST['contents']);
            $contentsCheckRes = $this->_checkContents($contents);
            if ($contentsCheckRes['ok']) {
                $info['server'] = trim($_REQUEST['server']);
                $info['contents'] = $contents;
                $GameActModel->update($info);
                $GameActModel->destroy();
                $info = $GameActModel->getInfo($id);

                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, $info);
            }
            else {
                $msg = "修改失败：{$contentsCheckRes['result']}";
            }
        }
        include $this->_getTpl(__FUNCTION__);
    }
    public function batEditGameAct()
    {
        $msg = "";
        if (isset($_REQUEST['flag'])) {
            $msg = "修改成功";
            $id = Game::strval($_REQUEST, 'id');
            $idArr = explode('-', $id);
            Common::loadModel('GameActModel');
            $GameActModel = new GameActModel();
            foreach ($idArr as $idArr_v) {
                $info = $GameActModel->getInfo($idArr_v);
                if (strlen(trim($_REQUEST['server'])) > 0) {
                    $info['server'] = trim($_REQUEST['server']);
                }
                if (isset($_REQUEST['gid']) && trim($_REQUEST['gid']) !== '') {
                    $changeIDList = array();
                    foreach ($info['contentsArr'] as $v) {
                        if ($v['info']['id'] == 'day') {continue;}
                        $changeIDList[] = $v['info']['id'];
                    }
                    $changeIDList = array_unique($changeIDList);
                    foreach ($changeIDList as $id_v) {
                        $info['contents'] = $this->_rpContentsID('id', $id_v, trim($_REQUEST['gid']), $info['contents']);
                    }
                }
                $info['contents'] = $this->_rpContentsDay('startDay', trim($_REQUEST['startDay']), $info['contents']);
                $info['contents'] = $this->_rpContentsDay('endDay', trim($_REQUEST['endDay']), $info['contents']);
                $info['contents'] = $this->_rpContentsTime('startTime', trim($_REQUEST['startTime']), $info['contents']);
                $info['contents'] = $this->_rpContentsTime('endTime', trim($_REQUEST['endTime']), $info['contents']);
                $GameActModel->update($info);
            }
            $GameActModel->destroy();
        }
        include $this->_getTpl(__FUNCTION__);
    }
    //导出活动
    public function exportActivity()
    {
        Common::loadModel('GameActModel');
        $GameActModel = new GameActModel();
        if (isset($_REQUEST['export'])) {
            $id = Game::strval($_REQUEST, 'id');
            $idArr = explode('-', $id);
            $sqlArr = array();
            $list = $GameActModel->getAllInfo();
            foreach ($list as $list_v) {
                if (!in_array($list_v['id'], $idArr)) {continue;}
                $sqlArr[] = $GameActModel->getAddSql($list_v);
            }
            $filename = 'activity_list.sql';
            header("Content-Type: application/octet-stream");
            if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']) ) {
                header('Content-Disposition:  attachment; filename="' . $filename . '"');
            } elseif (preg_match("/Firefox/", $_SERVER['HTTP_USER_AGENT'])) {
                header('Content-Disposition: attachment; filename*="utf8' .  $filename . '"');
            } else {
                header('Content-Disposition: attachment; filename="' .  $filename . '"');
            }
            echo implode("\n", $sqlArr);exit;
        }
    }
    private function _rpContentsID($key, $value, $replace, $contents)
    {
        if (empty($replace)) {return $contents;}
        return preg_replace("/['\"]{1}{$key}['\"]{1}[ ]*=[ ]*>[ ]*['\"]?{$value}*['\"]?,/", "'{$key}' => {$replace},", $contents);
    }
    private function _rpContentsDay($key, $replace, $contents)
    {
        if (!is_numeric($replace)) {return $contents;}
        return preg_replace("/['\"]{1}{$key}['\"]{1}[ ]*=[ ]*>[ ]*['\"]?[0-9]*['\"]?,/", "'{$key}' => {$replace},", $contents);
    }
    private function _rpContentsTime($key, $replace, $contents)
    {
        if (empty($replace)) {return $contents;}
        return preg_replace("/['\"]{1}{$key}['\"]{1}[ ]*=[ ]*>[ ]*['\"]{1}.*['\"]{1}/", "'{$key}' => '{$replace}'", $contents);
    }
    public function selectTemplate()
    {
        Common::loadModel('GameActTemplateModel');
        $GameActTemplateModel = new GameActTemplateModel();
        $category = $GameActTemplateModel->getCategory();
        include $this->_getTpl(__FUNCTION__);
    }

    public function selectActivityById(){
        include $this->_getTpl(__FUNCTION__);
    }

    public function getTemplateByCate()
    {
        $resBox = isset($_REQUEST['resType']) && trim($_REQUEST['resType']) == 'box';
        $cate = $_REQUEST['cate'];
        if ($cate == -1) {
            echo '';
        }
        Common::loadModel('GameActTemplateModel');
        $GameActTemplateModel = new GameActTemplateModel();
        $category = $GameActTemplateModel->getCategory();
        $list = $GameActTemplateModel->getInfoByCate($category[$cate]);
        if ($resBox == 'box') {
            $res = '';
            foreach ($list as $val){
                $res .= "<label><input type='checkbox' name='template[]' value='{$val['id']}'>{$val['id']}-{$val['act_key']}-{$val['title']}</label><br />";
            }
        }
        else {
            $res = "<option value=''>请选择</option>";
            foreach ($list as $val){
                $res .= "<option value='{$val['id']}'>{$val['id']}-{$val['act_key']}-{$val['title']}</option>";
            }
        }
        echo $res;
    }
    public function addGameAct()
    {
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel($this->game_server_key);
        $allMark = $ComVoComModel->getValue();
        if (!isset($_REQUEST['template_id'])) {
            $location = "location.href='?sevid={$_GET['sevid']}&mod=gameAct&act=passList';";
            echo "<script>alert('请选择模板！');{$location}</script>";
            include $this->_getTpl(__FUNCTION__);return;
        }
        Common::loadModel('GameActTemplateModel');
        $GameActTemplateModel = new GameActTemplateModel();
        $template = $GameActTemplateModel->getInfo(trim($_REQUEST['template_id']));
        if (empty($template)) {
            echo '模板不存在';
            include $this->_getTpl(__FUNCTION__);return;
        }
        $mark_copy = isset($_REQUEST['mark_copy']) ? $_REQUEST['mark_copy'] : array();
        if (isset($_REQUEST['flag'])) {
            $contents = trim($_REQUEST['contents']);
            $contentsCheckRes = $this->_checkContents($contents);
            if ($contentsCheckRes['ok']) {
                Common::loadModel('GameActModel');
                $GameActModel = new GameActModel();
                $GameActModel->add(array(
                    'act_key'=>trim($template['act_key']),
                    'server'=>trim($_REQUEST['server']),
                    'audit'=>0,
                    'auser'=>'',
                    'atime'=>time(),
                    'contents'=>$contents,
                ));
                $GameActModel->destroy();

                $copyFail = array();
                Common::loadLib('HttpRequest');
                foreach ($mark_copy as $m_c_m) {
                    if ($m_c_m == GAME_MARK) {
                        $copyFail[] = $allMark[$m_c_m]['title'];
                        continue;
                    }
                    $args = array(
                        'act_key'=>trim($template['act_key']),
                        'sevid'=>$_GET['sevid'],
                        'mod'=>'gameAct',
                        'act'=>'copyGameAct',
                    );
                    ksort($args);
                    $query = http_build_query($args);
                    $sign = Game::getAdminApiSign($query);
                    $url = sprintf('http://%s/api/adminApi.php?%s&sign=%s', $allMark[$m_c_m]['url'], $query, $sign);
                    $reqRes = HttpRequest::makeRequest($url, $_POST, array());
                    if (!$reqRes['result'] || !empty($reqRes['msg'])) {
                        $copyFail[] = $allMark[$m_c_m]['title'];
                    }
                }
                $copyRes = empty($copyFail) ? '' : '复制失败：'.implode(',', $copyFail);
                $location = "location.href='?sevid={$_GET['sevid']}&mod=gameAct&act=passList&auditType=0';";
                echo "<script>alert('提交成功！{$copyRes}');{$location}</script>";
                exit;
            }
        }
        include $this->_getTpl(__FUNCTION__);
    }
    public function batAddGameAct()
    {
        Common::loadModel('GameActTemplateModel');
        $GameActTemplateModel = new GameActTemplateModel();
        $category = $GameActTemplateModel->getCategory();

        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel($this->game_server_key);
        $allMark = $ComVoComModel->getValue();
        $mark_copy = isset($_REQUEST['mark_copy']) ? $_REQUEST['mark_copy'] : array();
        if (isset($_REQUEST['flag'])) {
            if (!isset($_REQUEST['template'])) {
                $location = "location.href='?sevid={$_GET['sevid']}&mod=gameAct&act=batAddGameAct';";
                echo "<script>alert('请选择模板！');{$location}</script>";
                include $this->_getTpl(__FUNCTION__);return;
            }

            Common::loadLib('HttpRequest');
            Common::loadModel('GameActModel');
            $GameActModel = new GameActModel();
            $addFail = array();
            $copyFail = array();
            foreach ($_REQUEST['template'] as $tpID) {
                $template = $GameActTemplateModel->getInfo($tpID);
                if (empty($template)) {
                    $addFail[] = "{$tpID}不存在";
                    continue;
                }
                $GameActModel->add(array(
                    'act_key'=>trim($template['act_key']),
                    'server'=>'all',
                    'audit'=>0,
                    'auser'=>'',
                    'atime'=>time(),
                    'contents'=>$template['contents'],
                ));
                $GameActModel->destroy();

                foreach ($mark_copy as $m_c_m) {
                    if ($m_c_m == GAME_MARK) {
                        $copyFail[] = $allMark[$m_c_m]['title'];
                        continue;
                    }
                    $args = array(
                        'act_key'=>trim($template['act_key']),
                        'sevid'=>$_GET['sevid'],
                        'mod'=>'gameAct',
                        'act'=>'copyGameAct',
                    );
                    ksort($args);
                    $query = http_build_query($args);
                    $sign = Game::getAdminApiSign($query);
                    $url = sprintf('http://%s/api/adminApi.php?%s&sign=%s', $allMark[$m_c_m]['url'], $query, $sign);
                    $reqRes = HttpRequest::makeRequest($url, array(
                        'server' => 'all',
                        'contents' => $template['contents'],
                    ), array());
                    if (!$reqRes['result'] || !empty($reqRes['msg'])) {
                        $copyFail[] = $allMark[$m_c_m]['title'];
                    }
                }
            }
            $addRes = empty($addFail) ? '' : '添加失败：'.implode(',', $addFail);
            $copyRes = empty($copyFail) ? '' : '复制失败：'.implode(',', $copyFail);
            $location = "location.href='?sevid={$_GET['sevid']}&mod=gameAct&act=passList&auditType=0';";
            echo "<script>alert('提交成功！{$copyRes}');{$location}</script>";
        }
        include $this->_getTpl(__FUNCTION__);
    }
    public function copyGameAct()
    {
        Common::loadModel('GameActModel');
        $GameActModel = new GameActModel();
        $GameActModel->add(array(
            'act_key'=>trim($_REQUEST['act_key']),
            'server'=>trim($_REQUEST['server']),
            'audit'=>0,
            'auser'=>'',
            'atime'=>time(),
            'contents'=>trim($_REQUEST['contents']),
        ));
        $GameActModel->destroy();
    }
    /*
     * 生效列表
     * */
    public function effectiveList(){
        Common::loadModel('ServerModel');
        $serverList = ServerModel::getServList();
        $listNow = array();
        $listNewTime = array();
        if (!empty($_REQUEST['selectSevID'])) {
            $serid = $_REQUEST['selectSevID'];
            $cache 	= Common::getCacheBySevId($serid);
            Common::loadModel('HoutaiModel');
            $listNow = $cache->get('huodong_list_'.$serid);
            $listNewTime = $cache->get('huodong_list_new_time_'.$serid);
        }
        Common::loadModel('GameActViewModel');
        $lists = array(
            array(
                'title'=>'区活动生效列表',
                'list'=>$listNow,
                'date'=>date("Y-m-d H:i:s", Game::get_now()),
            ),
            array(
                'title'=>'区活动预生效列表',
                'list'=>$listNewTime,
                'date'=>date("Y-m-d H:i:s", GameActViewModel::getNewTime())
            ),
        );
        Common::loadModel('GameActModel');
        $GameActModel = new GameActModel();
        Common::loadModel('ServerModel');
        $allList = array();
        if (!empty($_REQUEST['selectSevID'])) {
            $allList = $this->_forAllList(
                $GameActModel->getAllInfo(true),
                $_REQUEST['selectSevID'],
                ServerModel::getOpenDays($_REQUEST['selectSevID'])
            );
        }
        include $this->_getTpl(__FUNCTION__);
    }
    public function newTime()
    {
        Common::loadModel('GameActViewModel');
        if (!empty($_POST)) {
            $value = GameActViewModel::getValue();
            $value['newTime'] = $_POST['newTime'];
            $value['ip'] = empty($_POST['ip']) ? '' : $_POST['ip'];
            GameActViewModel::setValue($value);
        }
        $value = GameActViewModel::getValue();
        include $this->_getTpl(__FUNCTION__);
    }
    public function checkTempAjax()
    {
        echo json_encode($this->_checkContents( trim($_REQUEST['contents']) ));
    }
    protected function _checkContents($contents)
    {
        $dataReturn = array('ok'=>1);

        $info = trim($contents);
        if (empty($info)) {
            $dataReturn = array('ok'=>0, 'result'=>'详细信息不为空');
        }
        else {
            $info = @eval("return {$info};");
            if ($info === false) {
                $dataReturn = array('ok'=>0, 'result'=>'详细信息语法错误');
            }
            else if (!is_array($info)) {
                $dataReturn = array('ok'=>0, 'result'=>'详细信息要为数组格式');
            }
            $jsonEnInfo = json_encode($info, JSON_UNESCAPED_UNICODE);
            if (is_null(json_decode($jsonEnInfo, true))) {
                $dataReturn = array('ok'=>0, 'result'=>'详细信息无法json解析');
            }
            foreach ($info as $info_v) {
                $idStr = "id为{$info_v['info']['id']}的";
                if (!isset($info_v['info'])) {
                    $dataReturn = array('ok'=>0, 'result'=>$idStr.'详细信息需要info字段');break;
                }
                if (!empty($info_v['info']['startDay']) && !Game::isRightNumber($info_v['info']['startDay'])) {
                    $dataReturn = array('ok'=>0, 'result'=>$idStr.'详细信息 startDay 字段不符合数字格式');break;
                }
                if (!empty($info_v['info']['endDay']) && !Game::isRightNumber($info_v['info']['endDay'])) {
                    $dataReturn = array('ok'=>0, 'result'=>$idStr.'详细信息 endDay 字段不符合数字格式');break;
                }
                if (!empty($info_v['info']['showDay']) && !Game::isRightNumber($info_v['info']['showDay'])) {
                    $dataReturn = array('ok'=>0, 'result'=>$idStr.'详细信息 showDay 字段不符合数字格式');break;
                }
                if (!empty($info_v['info']['startTime']) && !Game::isRightDate($info_v['info']['startTime'])) {
                    $dataReturn = array('ok'=>0, 'result'=>$idStr.'详细信息startTime字段不符合日期格式');break;
                }
                if (!empty($info_v['info']['endTime']) && !Game::isRightDate($info_v['info']['endTime'])) {
                    $dataReturn = array('ok'=>0, 'result'=>$idStr.'详细信息endTime字段不符合日期格式');break;
                }
                if (!empty($info_v['info']['shTime']) && !Game::isRightDate($info_v['info']['shTime'])) {
                    $dataReturn = array('ok'=>0, 'result'=>$idStr.'详细信息shTime字段不符合日期格式');break;
                }
                if (isset($info_v['info']['type']) && !Game::isRightNumber($info_v['info']['type'])) {
                    $dataReturn = array('ok'=>0, 'result'=>$idStr.'详细信息 type 字段不符合数字格式');break;
                }
            }
        }

        return $dataReturn;
    }
    protected function _forAllList($gameActList, $serverID, $openDay)
    {
        ksort($gameActList);
        $actInfoList = array();
        foreach ($gameActList as $v) {
            $server = $v['server'];
            if (empty($server)) {
                continue;
            }
            $serverList = Game::serves_str_arr($server);
            if ($server != 'all' && !in_array($serverID, $serverList)) {
                continue;
            }
            $actKey = $v['act_key'];
            $infoEs = GameActModel::check_huodong($v['contentsArr'], $openDay);
            if (empty($infoEs)) {
                continue;
            }
            foreach ($infoEs as $info) {
                $data = GameActModel::create_cfg($actKey, $info);
                //处理info各种时间
                $dataInfo = $data['info'];
                $dataInfo['hid'] = $dataInfo['id'];
                $dataInfo['id'] = $dataInfo['no'];
                $dataInfo['cd'] = array(//倒计时
                    'next' => $dataInfo['eTime'],
                    'label' => $actKey . '_ltime',
                );
                unset($dataInfo['startDay']);
                unset($dataInfo['endDay']);
                unset($dataInfo['startTime']);
                unset($dataInfo['endTime']);
                unset($dataInfo['no']);

                if (!isset($actInfoList[$actKey])) {
                    $actInfoList[$actKey] = array();
                }
                if (!isset($actInfoList[$actKey][$v['id']])) {
                    $actInfoList[$actKey][$v['id']] = array();
                }
                $status = $this->_getTimeStatus($dataInfo['sTime'], $dataInfo['eTime']);
                $showStatus = $this->_getTimeStatus($dataInfo['sTime'], $dataInfo['showTime']);
                $existsIDs = array();
                if (isset($actInfoList[$actKey])) {
                    foreach ($actInfoList[$actKey] as $existsID => &$val) {
                        if ($server == 'all' && $status == 2) {
                            foreach ($val as $val_val) {
                                if ($val_val['server'] == 'all') {continue;}
                                if ($val_val['info']['status'] != 2) {continue;}
                                $existsIDs[] = $existsID;
                                //标记覆盖生效
                                $status = 4;
                                $showStatus = 4;
                            }
                        }
                        if ($server != 'all' && $existsID != $v['id'] && $status == 2) {
                            foreach ($val as &$val_val_val) {
                                if ($val_val_val['info']['status'] != 2) {continue;}
                                //回找标记覆盖生效
                                $val_val_val['info']['status'] = 4;
                                $val_val_val['info']['showStatus'] = 4;
                                $val_val_val['info']['existsIDs'][] = $v['id'];
                            }
                        }
                    }
                }
                $dataInfo['status'] = $status;
                $dataInfo['showStatus'] = $showStatus;
                $dataInfo['existsIDs'] = $existsIDs;
                $actInfoList[$actKey][$v['id']][] = array(
                    'info'=>$dataInfo,
                    'server'=>$server,
                );
            }
        }
        return $actInfoList;
    }
    protected function _getTimeStatus($sTime, $eTime)
    {
        if ($sTime > $_SERVER['REQUEST_TIME']) {
            return 1;
        } else if ($sTime <= $_SERVER['REQUEST_TIME'] && $eTime >= $_SERVER['REQUEST_TIME']) {
            return 2;
        } else {
            return 3;
        }
    }

    public function serverConfig(){
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel($this->game_server_key);
        $severConfig = $ComVoComModel->getValue();
        if ($_POST){
            $title = trim($_POST['title']);
            $url   = trim($_POST['url']);
            if (!empty($title) && !empty($url)){
                $key = substr($url, 0, strpos($url, '.'));
                $severConfig[$key]['title'] = $title;
                $severConfig[$key]['url'] = $url;
                $ComVoComModel->updateValue($severConfig);
                echo '<script>alert("添加成功")</script>';
            }
        }
        if (!empty($_REQUEST['delete'])){
            $key = trim($_REQUEST['delete']);
            unset($severConfig[$key]);
            $ComVoComModel->updateValue($severConfig);
            echo '<script>alert("删除成功")</script>';
        }
        include $this->_getTpl(__FUNCTION__);
    }

    public function importActivity()
    {
        if (isset($_REQUEST['flag'])) {
            $contents = trim($_REQUEST['contents']);
            $sqlArr = explode("\n", $contents);
            Common::loadModel('GameActModel');
            $GameActModel = new GameActModel();
            foreach ($sqlArr as $sql) {
                $GameActModel->addBySql($sql);
            }
            $location = "location.href='?sevid={$_GET['sevid']}&mod=gameAct&act=passList';";
            echo "<script>alert('提交成功！');{$location}</script>";
        }
        include $this->_getTpl(__FUNCTION__);
    }

    protected $game_server_key = "GameAct_serverConfig";
}