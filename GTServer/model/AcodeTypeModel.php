<?php
//兑换码表
class AcodeTypeModel{
    protected static $_save_key="cdcode";
    /*
     * 添加数据
     * */
    public function add($data){
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_save_key);
        $res = $ComVoComModel->getValue();
        if(empty($res)) $res = array();
        $res[$data['act_key']] = array(
            'name' => $data['name'],
            'type' => $data['type'],
            'sever' => $data['sever'],
            'sTime' => $data['sTime'],
            'eTime' => $data['eTime'],
            'items' => $data['items']
        );
        $ComVoComModel->updateValue($res);
    }
    /*
     * 获取当前活动key的数据
     * */
    public function getvalue($key) {
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_save_key);
        $res = $ComVoComModel->getValue();
        return empty($res[$key]) ? array() : $res[$key];
    }
    /*
     * 获取全服数据
     * */
    public function getAllvalue() {
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_save_key);
        $res = $ComVoComModel->getValue();
        return empty($res) ? array() : $res;
    }
    /*
     * 删除指定key
     * */
    public function del($key) {
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_save_key);
        $res = $ComVoComModel->getValue();
        if(!empty($res[$key])){
            $res[$key]['isdel'] = 1;
            $ComVoComModel->updateValue($res);
        }
    }
    
    /*
     * 修改
     * param $key 活动$key  $data 修改后的数据
     * */
    public function modifyValue($key,$data){
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_save_key);
        $res = $ComVoComModel->getValue();
        if(!empty($res[$key])){
            $res[$key] = array(
                'name' => $data['name'],
                'type' => $data['type'],
                'sever' => $data['sever'],
                'sTime' => $data['sTime'],
                'eTime' => $data['eTime'],
                'items' => $data['items']
            );
            $ComVoComModel->updateValue($data);
            return true;
        }else{
            return false;
        }
    }
    /*
     * 更新所有数据
     * */
    public function updateAllData($data){
        Common::loadVoComModel('ComVoComModel');
        $ComVoComModel = new ComVoComModel(self::$_save_key);
        $ComVoComModel->updateValue($data);
    }
}