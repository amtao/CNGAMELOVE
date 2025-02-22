<?php
require_once "ActBaseModel.php";
/*
 * 角色删除
 */
class Act1000Model extends ActBaseModel
{
	public $atype = 1000;//活动编号
	
	public $comment = "角色删除";
	public $b_mol = "";//返回信息 所在模块
	public $b_ctrl = "";//返回信息 所在控制器
    private $pre = "role_del_";
	
	//public 
	
	/*
	 * 初始化结构体
	 */
	public $_init = array(

	);
	
	/*
	 * 删号
	 */
    public function del()
    {
        if($this->info['is_del'] == 1){
            return false;
        }
        $this->info['is_del'] = 1;
        $this->info['time'] = Game::get_now();

        $open_id = Common::getOpenid($this->uid);

        $mcache = Common::getCacheByUid($this->uid);
        $mcache->delete($open_id.'_ustr');

        $open_id = $this->pre.$open_id;
        if(self::update_ustr($open_id) === false){
            return false;
        }
        $this->save();

        $Redis1000Model = Master::getRedis1000();
        $Redis1000Model->zAdd($this->uid,1);



        return true;
    }

    /*
     * 是否已经删号
     */
    public function isDel()
    {
        if($this->info['is_del'] == 1){
            return true;
        }
        return false;
    }

    public function update_ustr($open_id){
        $sql = "UPDATE `gm_sharding` SET `ustr` = '{$open_id}' WHERE`uid` ='{$this->uid}'";
        $db = Common::getDbeByUid($this->uid);
        if($db->query($sql) === false){
            return false;
        }
        $mcache = Common::getCacheByUid($this->uid);
        $mcache->delete($this->uid.'_openid');

        return true;
    }


    public function recover(){
        if(empty($this->info['is_del'])){
            return false;
        }
        $this->info['is_del'] = 0;
        $this->info['time'] = Game::get_now();

        $open_id = Common::getOpenid($this->uid);
        $mcache = Common::getCacheByUid($this->uid);
        $mcache->delete($this->uid.'_openid');

        $open_id =  str_replace($this->pre,'',$open_id);
        if(self::update_ustr($open_id) === false){
            return false;
        }
        
        $this->save();
        $mcache->delete($open_id.'_ustr');
        $Redis1000Model = Master::getRedis1000();
        $Redis1000Model->zDelete($this->uid);
    }
}
