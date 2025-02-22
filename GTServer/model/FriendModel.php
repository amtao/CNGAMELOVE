<?php
//门客
require_once "AModel.php";
class FriendModel extends AModel
{
    protected $_syn_w = true;
    public $_key = "_friend";
    protected  $updateSetKey = array(
        'uid','fuid','love','level','status'
    );

    public function __construct($uid)
    {
        parent::__construct($uid);
        $cache = $this->_getCache();
        $this->info = $cache->get($this->getKey());

        if($this->info == false || ($_SERVER['REQUEST_TIME'] - $this->info["time"] > 3600)){

            $sql = "select * from `friend_love` where `uid`={$this->uid} AND `status` = 0";

            $newUid = min($this->uid, $uid);
            $servid = Game::get_sevid($newUid);
            $db = Common::getDbBySevId($servid);
            if (empty($db))
            {
                Master::error('dberruid_'.$this->uid);
                return false;
            }
            $data = $db->fetchArray($sql);
            if($data == false) $data = array();

            $info = array('time' => $_SERVER["REQUEST_TIME"]);
            foreach ($data as $v){
                $info[$v['fuid']] = $v;
            }

            $sql = "select * from `friend_love` where `fuid`={$this->uid} AND `status` = 0";
            $data = $db->fetchArray($sql);
            if($data == false) $data = array();

            foreach ($data as $v){
                $info[$v['uid']] = $v;
            }

            $this->info = $info;
            $cache->set($this->getKey(),$this->info);
        }
    }

    /*
     * 获取亲密度和等级
     */
    public function getLoveInfo($fuid)
    {
        $info = $this->info[$fuid];
        return $info;
    }

    /*
     * 添加好友亲密度
     */
    public function add_friend($fuid){

        $newUid = min($this->uid, $fuid);
        $newFuid = max($this->uid, $fuid);

        $sql = "select * from `friend_love` where `uid`={$newUid} AND `fuid` = {$newFuid}";
        $db = $this->_getDb();
        if (empty($db))
        {
            Master::error('dberruid_'.$this->uid);
            return false;
        }
        $data = $db->fetchRow($sql);

        if ($data == false) {

            $data = array(
                'uid' => $newUid,
                'fuid' => $newFuid,
                'love' => 0,
                'level' => 1,
                'status' => 0
            );
        }else{
            $data["status"] = 0;
            $data['_update'] = true;
            $this->info[$fuid] = $data;
        }

        $this->update($data, $fuid);
        $this->info[$fuid] = $data;
    }

    /*
     * 删除好友亲密度
     */
    public function del_friend($fuid){

        $newUid = min($this->uid, $fuid);
        $newFuid = max($this->uid, $fuid);

        $sql = "select * from `friend_love` where `uid`={$newUid} AND `fuid` = {$newFuid}";
        $db = $this->_getDb();
        if (empty($db))
        {
            Master::error('dberruid_'.$this->uid);
            return false;
        }
        $data = $db->fetchRow($sql);

        if ($data == false) {

            return false;
        }else{

            $data["status"] = 1;
            $data['_update'] = true;
        }

        $this->update($data, $fuid);
    }

    /*
     * 更新
     */
    public function update($data, $fuid, $isUpdate = true)
    {

        if ( isset($this->info[$fuid]) ){//存在 则更新

            $info = $this->info[$fuid];
            //更新
            foreach ($data as $k => $v){
                $info[$k] = $v;
            }
            $info['_update'] = $isUpdate;

            $this->info[$fuid] = $info;
        }else{
            $sql = "insert into `friend_love` set 
                `uid`='{$data['uid']}',
                `fuid`='{$data['fuid']}',
                `love`={$data['love']},
                `level`={$data['level']},
                `status`={$data['status']}";
            $db = $this->_getDb();
            $db->query($sql);
            $is_new = 1;

            $this->info[$fuid] = $data;
        }

        $this->_update = true;
    }

    /*
     * 增加好友亲密度
     * $love
     * */
    public function add_love($fuid, $love, $isChange = true){
        if(empty($this->info)){
            return 0;
        }

        $this->info[$fuid]["love"] += $love;
        $this->_update = true;
        $data = array(
            "love" => $this->info[$fuid]["love"]
        );

        $this->update($data, $fuid, $isChange);
    }

    /*
     */
    public function sync()
    {
        if (!is_array($this->info)) return;
        $db = $this->_getDb();
        foreach ($this->info as $k=>$v){
            if ($v['_update']){
                $this->info[$k]['_update'] = false;

                $sql=<<<SQL
update `friend_love` set `love` = {$v['love']}, `level` = {$v['level']}, `status` = {$v['status']} where `uid` = {$v['uid']} and `fuid` = {$v['fuid']} limit 1;
SQL;

                $flag = $db->query($sql);
                if(!$flag){
                    Master::error('db error FriendModel_'.$sql);
                }
            }
        }
        return true;
    }
}
