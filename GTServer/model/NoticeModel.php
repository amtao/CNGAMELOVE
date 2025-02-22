<?php
// 公告类
class NoticeModel {

    protected static $_word_key = 'notice';
    protected static $_config_key = 'config';
    protected static $_ver_key = 'ver';
    protected static $_read_write_file_game = array();

    public function __construct()
    {

    }

    /*
     * 添加公告内容
     */
    public function addNotice($data){
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_word_key);
        $ComVoComModel->updateValue($data);
    }

    /*
     * 删除公告内容
     */
    public function delNotice($key){
        $data = self::noticeData();
        unset($data[$key]);
        self::addNotice($data);
    }

    /*
     * 返回公告内容
     */
    public function noticeData(){
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_word_key);
        return $ComVoComModel->getValue();
    }

    /*
     * 公告配置
     */
    public function noticeConfig(){
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_config_key);
        return $ComVoComModel->getValue();
    }

    /*
     * 添加/更新公告配置
     */
    public function addNoticeConfig($data){
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_config_key);
        $ComVoComModel->updateValue($data);
    }

    /*
     * 更新版本
     */
    public function noticeVer(){
        $data = array('v'=>time());
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_ver_key);
        $ComVoComModel->updateValue($data);
    }
}
