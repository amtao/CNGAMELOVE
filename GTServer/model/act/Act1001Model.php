<?php
require_once "ActBaseModel.php";
/*
 * 角色重置
 */
class Act1001Model extends ActBaseModel
{
	public $atype = 1001;//活动编号
	public $comment = "角色重置";
	public $b_mol = "";//返回信息 所在模块
	public $b_ctrl = "";//返回信息 所在控制器
    private $pre = "role_reset_";
    public $key = [
        'shili_redis'=>'shili_redis_msg',
        'guanka_redis'=>'guanka_redis_msg',
        'love_redis'=>'love_redis_msg',
        'yamen_redis'=>'yamen_redis_msg',
        'jiulou_redis'=>'jiulou_redis_msg',
        'taofa_redis'=>'taofa_redis_msg',
//        'huodong_301_kua_redis'=>'huodong_301_kua_redis_msg',
//        'huodong_302_kua_redis'=>'huodong_302_kua_redis_msg'
        ];                      //redis和$memcache缓存的下表是redis的，值是$memcache

    public $userkey = [
        '_user',
        '_hero',
        '_wife',
        '_item'
    ];                      //删除个人势力缓存、个人门客信息缓存、个人佳人信息缓存、个人道具信息缓存


	/*
	 * 初始化结构体
	 */
	public $_init = array(

	);
	
	/*
	 * 重置账号
	 */
    public function reset()
    {
//        if($this->info['is_reset'] == 1){
//            return false;
//        }
        $this->info['is_reset'] = 1;
        $this->info['time'] = Game::get_now();

        //重置用户信息
        if(self::update_userinfo() === false){
            Master::error('reset userinfo error');
        }
        //重置门客信息
        if(self::update_menke() === false){
            Master::error('reset menke error');
        }
        //重置红颜信息
        if(self::update_wife() === false){
            Master::error('reset wife error');
        }

        //重置道具
        if(self::update_item() === false){
            Master::error('reset item error');
        }

        //排行榜
        if(self::update_list() === false){
            Master::error('reset list error');
        }

        //帮派
        if(self::update_club() === false){
            Master::error('reset club error');
        }

        //个人信息等缓存清掉
        if(self::reset_memcache() === false){
            Master::error('reset reset_memcache error');
        }

        //封号
        if(self::closure() === false){
            Master::error('closure error');
        }


        $this->save();

        $Redis1001Model = Master::getRedis1001();
        $Redis1001Model->zAdd($this->uid,1);



        return true;
    }

    /*
     * 是否已经重置账号
     */
    public function isReset()
    {
        //判断是否已经删除
        if($this->info['is_reset'] == 1){
            return true;
        }
        return false;
    }

    /**
     * 用户信息更新
    */
    public function update_userinfo(){
        $table = 'user_'.Common::computeTableId($this->uid);
        $sql = "UPDATE {$table} SET level = 0,coin = 2000,food = 2000,army = 280,loginday = 1,step = 1,bmap = 1,smap = 0,mkill = 0,exp = 0, vip = 0 WHERE uid ={$this->uid}";
        $db = Common::getDbeByUid($this->uid);
        if($db->query($sql) === false){
            return false;
        }

        return true;
    }
    /**
     * 用户门客更新
     */
    public function update_menke(){
        $table = 'hero_'.Common::computeTableId($this->uid);
        $sql = "update {$table} set `level` = 1,exp = 0,zzexp = 0,pkexp = 0,senior = 1,e1 = 0,e2 = 0, e3 = 0,e4 = 0 WHERE uid ={$this->uid}";
        $db = Common::getDbeByUid($this->uid);
        if($db->query($sql) === false){
            return false;
        }

        return true;
    }

    /**
     * 用户红颜更新
     */
    public function update_wife(){
        $table = 'wife_'.Common::computeTableId($this->uid);
        $sql = "SELECT wifeid,skill FROM {$table} where uid = {$this->uid} order by wifeid";
        $db = Common::getDbeByUid($this->uid);
        $data = $db->fetchArray($sql);
        //如果没有红颜直接跳过
        if(empty($data)){
            return true;
        }
        //如果存在红颜
        foreach ($data as $key => $value){
            $skill = json_decode($value['skill'],true);

            foreach ($skill as $k => $s){
                $skill[$k] = 0;
            }

            $skill = json_encode($skill);
            $sql = "UPDATE {$table} set flower = 0,love = 0,exp = 0,skill = '{$skill}' where uid = {$this->uid} AND wifeid = {$value['wifeid']}";
            if($db->query($sql) === false){
                return false;
            }
        }
        return true;
    }

    /**
     * 用户道具更新
     */
    public function update_item(){
        $table = 'item_'.Common::computeTableId($this->uid);
        $sql = "DELETE FROM {$table} WHERE uid = {$this->uid}";
        $db = Common::getDbeByUid($this->uid);
        if($db->query($sql) === false){
            return false;
        }

        return true;
    }

    /**
     * 用户榜单删除redis
     */
    public function update_list(){
        //删除redis缓存单个数据
        //删除memcache榜单
            $serid = $_REQUEST['server'];
            $redis = Common::getDftRedis($serid);
            foreach ($this->key as $red => $memcache){
                if(is_float($redis->ZSCORE($red, $this->uid))){
                    $redis->zDelete($red, $this->uid);
                    $cache = Common::getMyMem();
                    $cache->delete($memcache);
                }
            }
            //删除跨服势力榜
            $Redis301Model = Master::getRedis301();
            $Redis301Model->zDelete($this->uid);
            $Redis301Model->del_msg_key();

            //跨服帮会排行 删除
            $Redis302Model = Master::getRedis302();
            $Redis302Model->del_msg_key();

            return true;
    }

    /**
     * 联盟更新
     */
    public function update_club(){
        //当前用户的公会信息
        $Act40Model = Master::getAct40($this->uid);
        $cid = $Act40Model->info['cid'];
        $Sev15Model = Master::getSev15($cid);

        if(!empty($cid)){
            $ClubModel = Master::getClub($cid);
            //判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他
            $myPost = $ClubModel->info['members'][$this->uid]['post'];
            //判断是不是盟主   1:盟主  2:副盟主 3:精英 4:成员 5:其他
            //不是帮主
            if($myPost != 1){
                //退出帮会
                $Act40Model->outClub($cid);
                $ClubModel->goout_club($this->uid);
                //记录公会日志
                $Sev15Model->add_log(7,$this->uid,$this->uid,$myPost,$myPost);
                return true;
            }
            //是帮主
            //帮会有多少人
            $num = count($ClubModel->info['members']);
            //判断帮会用户是否就一个人，如果是就直接解散
            if($num<2){
                //退出帮会
                $Act40Model->outClub($cid);
                $ClubModel->goout_club($this->uid);
                //删除公会  and  删除redis
                $ClubModel->del_club($cid,$this->uid);
                $ClubModel->delete_cache();

                return true;
            }
            //帮会用户不止一个人
            $post = $ClubModel->info['members'];
            //判断副帮主，精英，成员各有多少人
            $fubangzhu = $jingying = $chengyuan = array();
            foreach ($post as $p){
                if($p['post'] == 2){
                    $fubangzhu[] = $p;
                }
                if($p['post'] == 3){
                    $jingying[] = $p;
                }
                if($p['post'] == 4){
                    $chengyuan[] = $p;
                }
            }
//            return $jingying[0]['uid'];
            if($this->out_club($Act40Model,$ClubModel,$Sev15Model,$cid,$myPost,$fubangzhu)) {
                return true;
            }
//            return $this->out_club($Act40Model,$ClubModel,$Sev15Model,$cid,$myPost,$jingying);
            if($this->out_club($Act40Model,$ClubModel,$Sev15Model,$cid,$myPost,$jingying)) {
                return true;
            }
            if($this->out_club($Act40Model,$ClubModel,$Sev15Model,$cid,$myPost,$chengyuan)) {
                return true;
            }

        }
        return true;


    }

    /**
     * 本身是帮主，帮会存在其他成员
    */
    public function out_club($Act40Model,$ClubModel,$Sev15Model,$cid,$myPost,$people=array()){
        //如果没有副帮主,程序退出
        if(count($people)==0){
            return false;
        }
        //如果就一个副帮主
        if(count($people)==1) {
            //帮主替换
            $fuid = $people[0]['uid'];
            $ClubModel->info['members'][$fuid]['post'] = 1;
            $data = array(
                'members' => $ClubModel->info['members'],
            );
            $ClubModel->update($data);

            //退出帮会
            $Act40Model->outClub($cid);
            $ClubModel->goout_club($this->uid);

            //记录公会日志
            $Sev15Model->add_log(7,$this->uid,$this->uid,$myPost,$myPost);
            return true;
        }
        if(count($people)>=2) {
            //加载贡献
            foreach ($people as $k => $value){
                $people[$k]['allgx'] = Master::getAct40($value['uid'])->info['allgx'];
            }
            //比较贡献
            $gx = $people[0];
            foreach ($people as $k => $value){
                if($gx['allgx'] < $value['allgx']) {
                    $gx = $people[$k];
                }
            }
            //帮主替换
            $fuid = $gx['uid'];
            $ClubModel->info['members'][$fuid]['post'] = 1;
            $data = array(
                'members' => $ClubModel->info['members'],
            );
            $ClubModel->update($data);

            //退出帮会
            $Act40Model->outClub($cid);
            $ClubModel->goout_club($this->uid);
            //记录公会日志
            $Sev15Model->add_log(7,$this->uid,$this->uid,$myPost,$myPost);
            return true;
        }
    }

    /**
     * 删除个人势力缓存、个人门客信息缓存、个人佳人信息缓存、个人道具信息缓存
     */
    public function reset_memcache(){
        $cache = Common::getDftMem();
        foreach ($this->userkey as $key) {
            $cache->delete($this->uid.$key);
        }
        return true;
    }

    /**
     * 封号
    */
    public function closure(){
        $Redis12Model = Master::getRedis12();
        $sb_data = $Redis12Model->is_exist($this->uid);
        if(empty($sb_data)){
            $Act59Model = Master::getAct59($this->uid);
            $Act59Model->addAccount();

            $sev25Model = Master::getSev25();
            $sev25Model->delete_msg($this->uid);

            $Sev22Model = Master::getSev22();
            $Sev22Model->delete_msg($this->uid);

            $Sev6012Model = Master::getSev6012();
            $Sev6012Model->delete_msg($this->uid);

            $Sev6013Model = Master::getSev6013();
            $Sev6013Model->delete_msg($this->uid);
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('closureUid' => $this->uid));

        }
        return true;
    }
}
