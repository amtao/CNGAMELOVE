<?php
class GameConfig
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
	public function configList()
    {
        Common::loadModel('GameConfigModel');
        $GameConfigModel = new GameConfigModel();
        if (isset($_REQUEST['del'])) {
            $GameConfigModel->delete(Game::intval($_REQUEST, 'id'));
            $GameConfigModel->destroy();
        }
        $list = $GameConfigModel->getAllInfo();
        include $this->_getTpl(__FUNCTION__);
    }
    public function viewConfig()
    {
        $id = intval($_REQUEST['id']);
        Common::loadModel('GameConfigModel');
        $GameConfigModel = new GameConfigModel();
        $info = $GameConfigModel->getInfo($id);
        include $this->_getTpl(__FUNCTION__);
    }
    public function editConfig()
    {
        $id = intval($_REQUEST['id']);
        Common::loadModel('GameConfigModel');
        $GameConfigModel = new GameConfigModel();
        $info = $GameConfigModel->getInfo($id);
        if (isset($_REQUEST['flag'])) {
            $msg = "修改成功";
            $contents = trim($_REQUEST['contents']);
            $contentsCheckRes = $this->_checkContents($contents);
            if ($contentsCheckRes['ok']) {
                $info['server'] = trim($_REQUEST['server']);
                $info['contents'] = $contents;
                $GameConfigModel->update($info);
                $GameConfigModel->destroy();
                $info = $GameConfigModel->getInfo($id);
            }
            else {
                $msg = "修改失败：{$contentsCheckRes['result']}";
            }
        }
        include $this->_getTpl(__FUNCTION__);
    }
    public function addConfig()
    {
        if (isset($_REQUEST['flag'])) {
            $contents = trim($_REQUEST['contents']);
            $contentsCheckRes = $this->_checkContents($contents);
            if ($contentsCheckRes['ok']) {
                Common::loadModel('GameConfigModel');
                $GameConfigModel = new GameConfigModel();
                $GameConfigModel->add(array(
                    'server'=>trim($_REQUEST['server']),
                    'config_key'=>trim($_REQUEST['config_key']),
                    'contents'=>$contents,
                ));
                $GameConfigModel->destroy();
                $location = "location.href='?sevid={$_GET['sevid']}&mod=gameConfig&act=configList';";
                echo "<script>alert('提交成功！');{$location}</script>";
                exit;
            }
        }
        include $this->_getTpl(__FUNCTION__);
    }
    public function checkTempAjax()
    {
        echo json_encode($this->_checkContents( trim($_REQUEST['contents']) ));
    }
    protected function _checkContents($contents)
    {
        $dataReturn = array('ok'=>1);
        return $dataReturn;
    }
}