<?php
require_once MOD_DIR . '/BModel.php';
/*
 * 1:	子嗣全服提亲列表
 * 2:	世界BOSS 葛二蛋
 * 3:	蒙古战斗道具奖励日志
 * 4:	葛二蛋击杀记录
 * 5:	历代王爷
 * 6:	衙门战击败20上榜表
 * 7	
 * 8	翰林院 总列表信息
 * 9	翰林院 房间座位信息
 * 10	联盟-每日贡献列表信息
 * 11	联盟-申请列表
 * 12	联盟-boss血量
 * 13	战报信息(boss未被击杀) 
 * 14	战报列表(boss被击杀)  =>公会boss伤害排行
 * 15	联盟日志
 * 16	
 * 17	
 * 18	
 * 19	
 * 20	酒楼-家宴-联盟可见
 * 21	酒楼-官宴-全服可见
 * 22   聊天-公共频道
 * 23	聊天禁言
 * 24	聊天-工会频道
 * 25	聊天-跨服频道
 * 26	聊天封号
 * 27	聊天封设备
 * 28	聊天 - 敏感过滤
 * 29	酒楼-官宴-全服可见
 * 30	新官上任鞭打道具奖励日志
 * 31   全服邮件列表
 * 32   新官上任 - boss血量
 * 33   礼包配置
 * 34   邮件审核
 * 35   聊天玩家GM系统 
 * 36   惩戒来福-boss血量
 * 37   惩戒来福-奖励日志
 * 38   聊天-公共频道 - 隐性
 * 39   跨服聊天禁言
 * 40   狩猎
 * 41   狩猎 奖励日志
 * 42   服务器列表
 * 43   国庆活动-boss血量
 * 44   国庆活动-奖励日志
 * 45   重阳节活动-boss血量
 * 46   重阳节活动-奖励日志
 *
 * 49   红包列表
 *
 *
 * 6123： 盛装出席通关记录
 * 6136： 皇子应援-投票日志
 * 6137： 皇子应援-胜负
 * 6168： 天天充值活动信息
 * 6183： 雪人信息
 * 6188： 充值翻牌-特奖日志
 * 6189： 点灯笼-特奖日志
 * 6190： 世界树
 *
 * 6220： 女生节-奖励日志
 * 6221： 植树节-胜负
 * 6222： 清明节-奖励日志
 * 6227： 幸运转盘-奖励日志
 * 6228： 读书节-奖励日志
 * 6229： 劳动节-公用数据
 * 6230： 端午节-奖励日志
 * 6231： 抢糕点-奖励日志
 * 6232： 热气球-奖励日志
 * 6234:  放河灯-奖励日志
 *
 */
/*
 * 服务器公共信息类
 */
class SevBaseModel extends BModel
{
	//public $act = null;//活动标签
	public $hid;//活动重置id
	public $cid;//活动分组id

	public $info;//活动数据
    protected $_s_info = array();
	public $_init = array();//初始化数据
	
	public $comment = "活动名称";
    protected $_save_msg_cd = 0;//结果集保存CD时长，单位秒数
    /**
     * 保存的时候，是否删除缓存重建，true是，false否
     * @var bool
     */
    protected $_delete_cache_when_save = true;
    
    
   protected $_use_lock = true;//是否加锁 默认加锁
	
	/**
	 * @param int $hid	//功能分期ID(用于重置)
	 * @param int $cid	//功能分组ID(用于分组数据)
	 */
	public function __construct($hid = 1,$cid = 1,$serverID = null)
	{
		if (empty($this->act)){
			Master::error('SevBaseModel_act_null');
		}
		parent::__construct($serverID);

		//$this->act = $act;//活动类型
		$this->cid = $cid;//活动分组ID
		//加公共数据锁
		if ($this->_use_lock){
			//Master::get_lock($this->_server_type+1,'sevact_'.$this->act,true,"数据忙碌中".'sevact_'.$this->act);
			//Master::get_lock($this->_server_type,'sevact_'.$this->act,true,"数据忙碌中".'sevact_'.$this->act);
		}
		
		//$this->hid = $hid;//活动重置ID
		$cache = $this->_getCache();
		$s_info = $cache->get($this->getKey());//缓存获取活动信息
		if($s_info == false || empty($s_info['did'])){
    		$db = $this->_getDB();
			$sql = "select * from `sev_act` where `key`='{$this->act}' and `hcid`='{$this->cid}'";
			if (empty($db)){
				return false;
			}
			$s_info = $db->fetchRow($sql);
			
			if(!empty($s_info['value'])) {
				$s_info['value'] = json_decode($s_info['value'],1);//活动数据
			}else{
				$this->info = $this->_init;
                $s_info = array(
                    'did'=>$hid,
                    'value'=>$this->info,
                );
			}
			
			$cache->set($this->getKey(),$s_info);
		}
		$this->hid = $hid;
		if (!is_array($s_info) 
		|| $s_info['did'] != $hid){
			$this->info = $this->_init;
			$s_info['did'] = $hid;
		}else{
			$this->info = $s_info['value'];
		}
		$this->_s_info = $s_info;

        return true;
	}
	
	public function getKey(){
		return 'sevact_'.$this->act.'_'.$this->cid;
	}
	
	/*
	 * 获取输出列表缓存Key
	 */
	public function getMsgKey(){
		return $this->getKey().'_'.$this->hid.'_msg';
	}
	
	/*
	 * 获取输出数据
	 */
	public function get_outf(){
        $cache = $this->_getCache();
		$outf = $cache->get($this->getMsgKey());
        $isMsgOut = $this->_save_msg_cd > 0 && (
            !isset($outf['save_msg_out_time']) || Game::is_over($outf['save_msg_out_time']));
		if($outf == false || $isMsgOut){
			$outf = $this->mk_outf();
            $outf['save_msg_out_time'] = $_SERVER['REQUEST_TIME'] + $this->_save_msg_cd;
			$cache->set($this->getMsgKey(),$outf);
		}
		unset($outf['save_msg_out_time']);
		return $outf;
	}
	
	/*
	 * 构造业务输出数据
	 */
	public function mk_outf(){
		return $this->info;
	}
	/*
	 * 保存操作
	 */
	public function save(){
		//保存到数据库 / 删除缓存
		$value_text = json_encode($this->info,JSON_UNESCAPED_UNICODE);
		$key = $this->act;
		$hcid = $this->cid;
		
		$did = $this->hid;
		
		$db = $this->_getDB();
		$sql = "select * from `sev_act` where `key`='{$key}' and `hcid`='{$hcid}'";
		$row = $db->fetchRow($sql);
		if ( empty($row['key']) ) {
			$sql = "insert into `sev_act`(`key`,`hcid`,`did`,`value`) values ('{$key}','{$hcid}','{$did}','{$value_text}')";
		} else {
			$sql = "update `sev_act` set `did`='{$did}',`value`='{$value_text}' where `key`='{$key}' and `hcid`='{$hcid}'";
		}
//		error_log("sql".$sql);
		if ( $db->query($sql) ) {
			//删除缓存
            $cache = $this->_getCache();
            if ($this->_delete_cache_when_save) {
                //删除缓存重建
                $cache->delete($this->getKey());
            } else {
                //更新到最新
                $this->_s_info['value'] = $this->info;
                $cache->set($this->getKey(), $this->_s_info);
            }
			$cache->delete($this->getMsgKey());
			//$this->mk_outf();
			return true;
		}else{
			Master::error("sev_act_saveerr:".$sql);
			return false;
		}
	}
    /**
     * 获取info数据
     * @return mixed
     */
	public function getInfo()
    {
        return $this->info;
    }

    /**
     * 设置info信息
     * @param $data
     */
    public function setInfo($data)
    {
        $this->info = $data;
        $this->save();
    }

	public function oldInfo(){
		return $this->_s_info;
	}
	/**
	 * 删除数据缓存
	 */
	public function delMsgCache(){
		$cache = $this->_getCache();
		$cache->delete($this->getMsgKey());
	}
}
