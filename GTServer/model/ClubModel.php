<?php
//公会
class ClubModel
{
	public $_cid;
	public $info;
	public $getKey;
	private $table = 'club';
	public $_key = "_club";
	protected  $updateSetKey = array(
		'cid','name','level','exp','fund','qq','weixin',
		'outmsg','notice','members','ftime','password','ctime','isJoin','lsjLv','spLv','dissolutionTime','jytLv'
	);
    protected $_serverID = null;
	public function __construct($cid)
	{
		$this->_cid = $cid;
        $this->_serverID = Game::get_sevid_club($cid);
		$cache = $this->_getCache();
		$this->getKey = $this->_cid.$this->_key;
		$this->info = $cache->get($this->getKey);
		if($this->info == false || empty($this->info["jytLv"])){
			$sql = "select * from `{$this->table}` where `cid`='{$this->_cid}'";
			$db = $this->_getDb();
			if (empty($db))
			{
				Master::error('club_dberr_cid_'.$this->_cid);
				return false;
			}
			$this->info = $db->fetchRow($sql);
			if($this->info == false) {
				//Master::error(CLUB_IS_NULL);
				return;
			}
			$this->info['name'] = Game::str_feifa($this->info['name'],1);
			if(!empty($this->info['members'])){
				$this->info['members'] = json_decode($this->info['members'],true);
			}
			$cache->set($this->getKey,$this->info);
		}
	}
	
	/**
	 * 获取联盟基础信息
	 */
	public function getBase()
	{
		$cache = $this->_getCache();
		$key = $this->_cid.'_club_base_data';
		$data = $cache->get($key);
		if(empty($data) || !isset($data['mzUID'])){
			//公会成员信息
			$members = array();
            $mzUID = 0;
			foreach($this->info['members'] as $k => $v){
                $member = $this->get_member($v);
                if ($member['post'] == 1) {
                    $mzUID = $member['id'];
                }
				$members[] = $member;

                unset($member);
			}
			
			//盟主7天未上线  更换盟主
			$xmembers = self::ref_post($members);
			if($xmembers){
				$members = $xmembers;
			}
			
			$data = array(
				'id' => $this->info['cid'],  //联盟id
				'name' => $this->info['name'], //联盟名字
				'level' => $this->info['level'], //等级
				'exp' => $this->info['exp'],  //联盟总经验
				'fund' => $this->info['fund'],  //财富值
				'qq' => $this->info['qq'],  //QQ
				'lsjLv' => empty($this->info['lsjLv']) ? 1:$this->info['lsjLv'],//理事间等级
				'spLv' => empty($this->info['spLv']) ? 1:$this->info['spLv'],//商铺等级
				'jytLv' => empty($this->info['jytLv']) ? 1:$this->info['jytLv'],//谏言堂等级
				'laoma' => empty($this->info['weixin'])?'':$this->info['weixin'],  //微信  热更后去掉
				'outmsg' => empty($this->info['outmsg'])?'':$this->info['outmsg'],  //对外宣言
				'notice' => empty($this->info['notice'])?'':$this->info['notice'],  //公告
				'members' => $members, //成员列表   1:盟主  2:副盟主 3:精英 4:成员 5:其他
				'isJoin' => $this->info['isJoin'], //是否允许其他玩家随机加入 1:是 0:否
				'mzUID' => $mzUID,//盟主UID
				'dissolutionTime' => empty($this->info['dissolutionTime']) ? 0:$this->info['dissolutionTime'],
			);
			$cache->set($key,$data,600);

            unset($members, $xmembers);
		}

		unset($cache);
		return $data;
	}

    /**
     * 简化版本，用于列表使用
     * 获取联盟基础信息
     */
    public function getSimple()
    {
        $cache = $this->_getCache();
        $key = $this->_cid.'_club_simple_data';
        $data = $cache->get($key);
        if(empty($data) || !isset($data['mzUID'])){
            //公会成员信息
            $members = array();
            $mzUID = 0;
            foreach($this->info['members'] as $k => $v){
                $member = $this->get_member_simple($v);
                if ($member['post'] == 1) {
                    $mzUID = $member['id'];
                }
                $members[] = $member;

                unset($member);
            }
            $data = array(
                'id' => $this->info['cid'],  //联盟id
                'name' => $this->info['name'], //联盟名字
                'level' => $this->info['level'], //等级
                'exp' => $this->info['exp'],  //联盟总经验
                'fund' => $this->info['fund'],  //财富值
				'qq' => $this->info['qq'],  //QQ
				'lsjLv' => $this->info['lsjLv'],//理事间等级
				'spLv' => $this->info['spLv'],//商铺等级
				'jytLv' => $this->info['jytLv'],//谏言堂等级
                'laoma' => empty($this->info['weixin'])?'':$this->info['weixin'],  //微信  热更后去掉
                'outmsg' => empty($this->info['outmsg'])?'':$this->info['outmsg'],  //对外宣言
                'notice' => empty($this->info['notice'])?'':$this->info['notice'],  //公告
                'members' => $members, //成员列表   1:盟主  2:副盟主 3:精英 4:成员 5:其他
                'isJoin' => $this->info['isJoin'], //是否允许其他玩家随机加入 1:是 0:否
                'mzUID' => $mzUID,//盟主UID
            );
            $cache->set($key,$data,600);

            unset($members, $xmembers);
        }

        unset($cache);
        return $data;
    }
	/**
	 * 获取帮会势力
	 * @param unknown_type $info  成员信息
	 */
	public function get_clubshili(){
		$data = self::getBase();
		$allshili = 0;
		foreach($data['members'] as $v){
			$allshili += $v['shili'];
		}
		return $allshili;
	}
	
	
	/**
	 * 职位限制
	 * @param unknown_type $info  成员信息
	 */
	public function postlimit($type){
		$allpsot = array();
		$cfg_club = Game::getcfg_info('club',$this->info['level']);
		foreach($this->info['members'] as $k => $v){
			if(empty($allpsot[$v['post']])){
				$allpsot[$v['post']] = 0;
			}
			$allpsot[$v['post']] += 1;
		}
		$post_2 = empty($cfg_club['leader'])?0:$cfg_club['leader'];  //副盟主个数
		$post_3 = empty($cfg_club['elite'])?0:$cfg_club['elite'];   //精英个数
		
		switch($type){
			case 2 :
				if( $allpsot[$type] >= $post_2){
					Master::error(CLUB_POST_MAX);
				}
				break;
			case 3 :
				if( $allpsot[$type] >= $post_3){
					Master::error(CLUB_POST_MAX);
				}
				break;
		}
		return true;
	}
	
	

    /**
     * 获取帮会密码
     * @param password  密码
     */
    public function get_password(){
        return $this->info['password'];
    }
	
	/**
	 * 获取成员信息
	 * @param unknown_type $info  成员信息
	 */
	public function get_member($info){
        //玩家基础信息
        $fUserdate = Master::fuidData($info['uid']);
        //玩家个人公会信息
        $Act40Model = Master::getAct40($info['uid']);
        //贡献列表信息
        $Sev10Model = Master::getSev10($this->_cid);

        $fUserModel = Master::getUser($info['uid']);

        //获取头像框
        $Act6151Model = Master::getAct6151($info['uid']);
		$headavatar = $Act6151Model->info;
		
		$Act6141Model = Master::getAct6141($info['uid']);

        $data = array(
            'id' => $info['uid'], //玩家UID
            'name' => $fUserdate['name'], //名字
            'post' => $info['post'], //职位
            'sex'=> $fUserdate['sex'],  //性别1男2女
            'job'=> $fUserdate['job'],  //头像编号
            'shili' => $fUserdate['shili'], //势力
            'level' => $fUserdate['level'], //官阶
            'gx' => $Act40Model->info['leftgx'], //贡献
            'allGx' => $Act40Model->info['allgx'], //总贡献
            'chenghao'=> $fUserdate['chenghao'],  //称号
            //0:未建设 1:初建   2:中建  3:高建  4:道具建设   5:高级道具建设
            'donate' => empty($Sev10Model->info[$info['uid']])?0:$Sev10Model->info[$info['uid']],
            'loginTime' => $fUserModel->info['lastlogin'], //登陆时间戳
			'headavatar'=>$headavatar,
			'clothe' => $Act6141Model->info,
        );

        unset($fUserdate, $Act40Model, $Sev10Model, $fUserModel, $Act6151Model,$Act6141Model);
        return $data;
	}


    /**
     * 获取成员信息
     * @param unknown_type $info  成员信息
     */
    public function get_member_simple($info){
        //玩家基础信息
        $UserModel = Master::getUser($info['uid']);
        $Act99Model = Master::getAct99($info['uid']);

        $data = array(
            'id' => $info['uid'], //玩家UID
            'name' => $UserModel->info['name'], //名字
            'post' => $info['post'], //职位
            'shili' => array_sum($Act99Model->info['ep'])
        );
        return $data;
    }

    /**
	 * 添加财富值
	 * @param unknown_type $num
	 */
	public function add_fund($uid,$num)
	{
		$this->info['fund'] += $num;  //联盟财富
		$data = array(
			'fund' => $this->info['fund'],
		);
		$this->update($data);
	}
	
	/**
	 * 减去财富值
	 * @param unknown_type $num
	 */
	public function sub_fund($uid,$num)
	{
		if($this->info['fund'] < $num){
			Master::error(CLUB_MONEY_SHORT);
		}
		$this->info['fund'] -= $num;  //联盟财富
		$data = array(
			'fund' => $this->info['fund'],
		);
		$this->update($data);
	}
	
	/**
	 * 添加联盟经验
	 * @param unknown_type $num 增加的联盟经验
	 */
	public function add_exp($uid,$num)
	{
		if($num <= 0){
			Master::error(CLUB_EXP_SHORT);
		}
		
		$oldLevel = $this->info['level'];
		
		$this->info['exp'] += $num;  //联盟总经验

		//计算等级
		$currentExp = $this->info['exp'];
		$currentLv = $this->info['level'];
		$cfg_club = Game::getcfg('club');
		$maxLv = count($cfg_club);
		if($currentLv >= $maxLv){
			return;
		}
		for($i = $currentLv;$i < $maxLv;$i++){
			$needExp = $cfg_club[$i]['exp'];
			if($needExp != 0 && $currentExp >= $needExp){
				$currentExp -= $needExp;
				$this->info['level']++;
				if($this->info['level'] >= $maxLv){
					break;
				}
				continue;
			}
		}
		$this->info['exp'] = $currentExp;

        $SevidCfg = Common::getSevidCfg();
        $serverId = $SevidCfg ['he'];
        $uidServerId = Game::get_sevid($uid);
        if ($serverId != $uidServerId){
            $filename = "gonghui_Redis_log";
            $content = 'http://'.$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI'].' | serverid:'.$serverId.' uidServerId:'.$uidServerId.'|'.date("Y-m-d H:i:s")."\r\n";
            $content.= file_get_contents("php://input")."\r\n";
            Common::log($filename, $content);
        }
		//更新联盟redis
		$Redis10Model = Master::getRedis10();
		$Redis10Model->zAdd($this->_cid,$this->info['exp']);
        $selfRid = $Redis10Model->get_rank_id($this->_cid);

        Common::loadModel('SwitchModel');
        if (SwitchModel::isKuaRankOpen()) {
            //更新跨服联盟排行榜
            if ($selfRid >= 1 && $selfRid <= 10) {
                $Redis302Model = Master::getRedis302();
                $Redis302Model->zAdd($this->_cid, $this->info['exp']);
            }
        }
		
		$data = array(
			'exp' => $this->info['exp'],
			'level' => $this->info['level'],
		);
		$this->update($data);
		
		//记录公会日志
		if($oldLevel != $this->info['level']){
			$Sev15Model = Master::getSev15($this->info['cid']);
			$Sev15Model->add_log(8,0,0,$this->info['level']);
			
			$Sev51Model = Master::getSev51($this->info['cid']);
			$Sev51Model->reset_clevel($this->info['level']);
			
			//主线任务 - 刷新
			$Act39Model = Master::getAct39($this->uid);
			$Act39Model->task_refresh(37);
		}
		
		//联盟冲榜
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->chongbang_huodong('huodong250',$this->info['cid'],$num);
		//联盟经验流水
        Game::cmd_other_flow($this->info['cid'] , __CLASS__, __FUNCTION__, array($uid => $num), 27, 1, $num, $this->info['exp']);
	}

		/**
	 * 减少联盟经验
	 * @param unknown_type $num 增加的联盟经验
	 */
	public function sub_exp($uid,$num)
	{
		if($num <= 0 || $this->info['exp'] < $num){
			Master::error(CLUB_EXP_SHORT);
		}

		$oldLevel = $this->info['level'];
		$this->info['exp'] -= $num;  //联盟总经验

		$data = array(
			'exp' => $this->info['exp']
		);
		$this->update($data);

		//联盟经验流水
        Game::cmd_other_flow($this->info['cid'] , __CLASS__, __FUNCTION__, array($uid => -$num), 27, 1, -$num, $this->info['exp']);
	}
	
	/**
	 * 验证人员是否已满
	 * @param unknown_type $data
	 */
	public function check_member(){
		//人数上限
		$cfg_club_id = Game::getcfg_info('club',$this->info['level']);
		$maxMember = empty($cfg_club_id['maxMember'])?0:$cfg_club_id['maxMember'];
		if($maxMember <= count($this->info['members'])){
			Master::error(CLUB_PERSON_FULL);
		}
	}
	
	
	
	/**
	 * 加入联盟
	 * @param unknown_type $data
	 */
	public function join_club($uid,$post){
		
		$this->info['members'][$uid]['post'] = $post;
		$this->info['members'][$uid]['uid'] = $uid;
		$data = array(
			'members' => $this->info['members'],
		);
		$this->update($data);

		//如果新官上任活动开启
		$Act280Model = Master::getAct280($uid);
		$Act280Model->addClubScore($this->_cid);

		//如果惩戒来福活动开启
		$Act282Model = Master::getAct282($uid);
		$Act282Model->addClubScore($this->_cid);

		//如果感恩节活动开启
		$Act284Model = Master::getAct284($uid);
		$Act284Model->addClubScore($this->_cid);

		//如果腊八节活动开启
		$Act286Model = Master::getAct286($uid);
		$Act286Model->addClubScore($this->_cid);

        //元旦-招财活动
        $Act294Model = Master::getAct294($uid);
        $Act294Model->in_club_rank($this->_cid,$Act294Model->info['cons']);

        //联盟势力涨幅冲榜排行
        $Act310Model = Master::getAct310($uid);
        $Act310Model->in_club_rank($this->_cid,$Act310Model->info['cons']);

        //如果宫殿宫斗冲榜开启
        $Act315Model = Master::getAct315($uid);
        $Act315Model->join_club($this->_cid);
	}
	
	/**
	 * 退出联盟
	 * @param unknown_type $data
	 */
	public function goout_club($uid){
		unset($this->info['members'][$uid]);
		$data = array(
			'members' => $this->info['members'],
		);
		$this->update($data);
		
		//如果有家宴,删除
		$Sev20Model = Master::getSev20($this->_cid);
		$Sev20Model->sub_yh($uid);

		//如果新官上任活动开启
		$Act280Model = Master::getAct280($uid);
		$Act280Model->delClubScore($this->_cid);

		//如果惩戒来福活动开启
		$Act282Model = Master::getAct282($uid);
		$Act282Model->delClubScore($this->_cid);

		//如果感恩节活动开启
		$Act284Model = Master::getAct284($uid);
		$Act284Model->delClubScore($this->_cid);

		//如果腊八节活动开启
		$Act286Model = Master::getAct286($uid);
		$Act286Model->delClubScore($this->_cid);

		//元旦-招财活动
        $Act294Model = Master::getAct294($uid);
        $Act294Model->out_club_rank($this->_cid,$Act294Model->info['cons']);

        //联盟势力涨幅冲榜排行
        $Act310Model = Master::getAct310($uid);
        $Act310Model->out_club_rank($this->_cid,$Act310Model->info['cons']);

		//发红包
		$Act295Model = Master::getAct295($uid);
		$Act295Model->removeHb($this->info['cid']);

        //如果宫殿宫斗冲榜开启
        $Act315Model = Master::getAct315($uid);
        $Act315Model->out_club($this->_cid);
	}
	
	
	
	/**
	 * 创建公会
	 * @param $data
	 */
	public static function create_club($data)
	{
		self::check($data);
		$info = array();
		$info['name'] = empty($data['name'])?'':$data['name'];
		$info['level'] = 1;
		$info['exp'] = 0;
		$info['fund'] = 0;
		$info['qq'] = empty($data['qq'])?0:$data['qq'];
		$info['weixin'] = empty($data['weixin'])?'':$data['weixin'];
		$info['password'] = empty($data['password'])?'':$data['password'];
		$info['outmsg'] = empty($data['outmsg'])?CLUB_WELCOME:$data['outmsg'];
		$info['notice'] = CLUB_WELCOME;
		$info['isJoin'] = empty($data['isJoin'])?0:$data['isJoin'];
		$info['ctime'] = $_SERVER['REQUEST_TIME'];
		$info['members'] = '';
		$info['ftime'] = $_SERVER['REQUEST_TIME'];
		$info['lsjLv'] = 1;
		$info['spLv'] = 1;
		$info['jytLv'] = 1;
		$db = Common::getMyDb();
		//用户表数据
		$sql = 
<<<SQL
INSERT INTO `club` set 
	`name` = '{$info['name']}',
	`level` = '{$info['level']}',
	`exp` = '{$info['exp']}',
	`fund` = '{$info['fund']}',
	`qq` = '{$info['qq']}',
	`weixin` = '{$info['weixin']}',
	`password` = '{$info['password']}',
	`outmsg` = '{$info['outmsg']}',
	`notice` = '{$info['notice']}',
	`isJoin` = '{$info['isJoin']}',
	`ctime` = '{$info['ctime']}',
	`members` = '{$info['members']}',
	`ftime` = '{$info['ftime']}',
	`lsjLv` = '{$info['lsjLv']}',
	`spLv` = '{$info['spLv']}',
	`dissolutionTime` = '{$info['dissolutionTime']}',
	`jytLv` = '{$info['jytLv']}';
SQL;

		if (!$db->query($sql)){
			Master::error(NOTE_SYSTEM_ERROR.'CLUBMODEL');
		}
		
		$cid = $db->insertId();

		Game::addClubName($cid,$info['name']);
		//加入联盟redis
		$Redis10Model = Master::getRedis10();
		$Redis10Model->zAdd($cid,0);
		
		return $cid;
		
		
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 */
	public function del_club($cid,$uid=0){
		$db = $this->_getDb();
		$sql = "delete from `{$this->table}` where `cid` = '{$cid}' ";
		$db->query($sql);
		$Redis10Model = Master::getRedis10();
		$Redis10Model->del_member($cid);
		//感恩节 解散帮会
		if(!empty($uid)){
			$Act280Model = Master::getAct280($uid);
			$Act280Model->delClub($cid);

			$Act282Model = Master::getAct282($uid);
			$Act282Model->delClub($cid);

			$Act284Model = Master::getAct284($uid);
			$Act284Model->delClub($cid);

			//如果腊八节活动开启
			$Act286Model = Master::getAct286($uid);
			$Act286Model->delClub($cid);

            //元旦-招财活动
            $Act294Model = Master::getAct294($uid);
            $Act294Model->del_club_rank($cid);

            //联盟势力涨幅冲榜排行
            $Act310Model = Master::getAct310($uid);
            $Act310Model->del_club_rank($cid);

		}
		//跨服帮会排行
		$Redis302Model = Master::getRedis302();
        $Redis302Model->del_member($cid);

        //帮会衙门冲榜 删除
        $Act315Model = Master::getAct315($uid);
        $Act315Model->del_club($cid);
	}
	
	public function delete_cache(){
		$cache = $this->_getCache();
		$key = $this->_cid.'_club_base_data';
        $simpleKey = $this->_cid.'_club_simple_data';
		$cache->delete($key);
        $cache->delete($simpleKey);
		$cache->delete($this->getKey);
	}
	
	/**
	 * 更新公会
	 * @param unknown_type $data
	 */
	public function update($data)
	{
		self::check($data);
		$fields = "";
		foreach($data as $k => $v){
			if(!in_array($k,$this->updateSetKey)){
				continue;
			}
			$this->info[$k] = $v;
			if($k == 'members'){
				$v = json_encode($v);
			}
			$fields .= "`{$k}`='{$v}',";
		}
		$fields = substr($fields,0,-1);
		if(	$fields	){
			$db = $this->_getDb();
			$sql = "update `{$this->table}` set {$fields} where `cid`={$this->_cid} ";
			$db->query($sql);	
		}
		self::delete_cache();
		//对应members  整块替换
		$h_info = self::getBase();
		Master::back_data(0,'club','clubInfo',$h_info);
	}
	public function sync()
	{
		return true;
	}
	//公会额外信息 获取 /改变/保存
	public function eeeee(){
		//sevxxModel = array(
		/*
		 * 
		 * 'log' => 2
		 * 'log' => 3
			'log' => attay(
			)
			*/
		//)
	}
	
	/**
	 * 检查数据
	 * @param unknown_type $data
	 */
	public static function check($data){
		if(empty($data)){
			return 0;
		}
		
		foreach($data as $k => $v){
			switch($k){
				case 'name':  //更改/保存名字     联盟不能一样
					Game::str_mingan($v);
					Game::filter_char($v,0);
					Game::check_club_name($v);

                    $len = Common::utf8_strlen($v);
					if($len <= 0 || $len > 8){
						Master::error(CLUB_BUMBER_TO_LONG);
					}

					break;
				case 'outmsg':
				case 'notice':
					Game::str_check($v);
					Game::filter_char($v,0);
					break;
				case 'qq':
					if(!empty($v) && !is_numeric($v)) Master::error(CLUB_QQ_NO_ALL_NUMBER);
					break;
				case 'weixin':
					if(!empty($v) && !preg_match("/^[-_a-zA-Z0-9]{1,20}$/",$v)) Master::error(CLUB_WX_NO_FORMATE);
					break;
			}
		}
	}
	
	/**
	 * 盟主7天未上线  主动退位
	 */
	public function ref_post($members){
		
		$muid = 0; //盟主uid
		
		//成员列表   1:盟主  2:副盟主 3:精英 4:成员 5:其他
		$xpost = 5;  //新职位  越小官职越高
		$xallGx = 0;  //新职位  对应的总贡献
		$xuid = 0;  //新职位  uid
		
		$xmembers = array(); //新成员列表
		
		//先检查盟主是否7天未上线
		foreach($members as $uinfo){
			//存放新成员
			$xmembers[$uinfo['id']] = $uinfo;
			
			//如果是盟主而且7天内在线 直接返回
			if($uinfo['post'] == 1  
			&& $_SERVER['REQUEST_TIME'] - $uinfo['loginTime'] < 7 * 24 * 60 * 60){
				return false;
			}
			//记录盟主uid
			if($uinfo['post'] == 1){
				$muid = $uinfo['id'];  
				continue;
			}
			
			//过滤3天未上线的
			if($_SERVER['REQUEST_TIME'] - $uinfo['loginTime'] >= 3 * 24 * 60 * 60){
				continue;
			}
			//优先 副盟主  => 精英 => 成员
			if($xpost > $uinfo['post']){
				$xpost  = $uinfo['post'];    //职位
				$xallGx = $uinfo['allGx'];  //新职位  对应的总贡献
				$xuid   = $uinfo['id'];  //新职位  uid
			}elseif($xpost == $uinfo['post']){  //职位一样,总贡献高的获得
				if($xallGx <  $uinfo['allGx']){
					$xpost  = $uinfo['post'];   //职位
					$xallGx = $uinfo['allGx'];  //新职位  对应的总贡献
					$xuid   = $uinfo['id'];  //新职位  uid
				}
			}
			
		}
		
		//判断更换盟主的成员是否就绪
		if( empty($muid) || empty($xuid) ){
			return false;
		}
		//   ====   [更换盟主]   ===  
		//盟主的职位改为成员
		$this->info['members'][$muid]['post'] = 4;
		$this->info['members'][$muid]['uid'] = $muid;
		$xmembers[$muid]['post'] = 4;
		//新盟主晋升
		$this->info['members'][$xuid]['post'] = 1;
		$this->info['members'][$xuid]['uid'] = $xuid;
		$xmembers[$xuid]['post'] = 1;
		
		$data = array(
			'members' => $this->info['members'],
		);
		$this->update($data);
		
		//改变帮会战职位
		$Sev51Model = Master::getSev51($this->_cid);
		$Sev51Model->reset_post($muid);
		
		$xmembers = array_values($xmembers);
		
		//设置公会密码弹窗
		$fAct40Model = Master::getAct40($xuid);
		$fAct40Model->pwd_tip(1);
		
		return $xmembers;
		
	}

    protected function _getCache()
    {
        return Common::getCacheBySevId($this->_serverID);
    }
    protected function _getDb()
    {
        return Common::getDbBySevId($this->_serverID);
    }

	/**
	 * 是否可以随机加入帮会
	 * @return bool
	 */
	public function isRandomJoin(){
		return empty($this->info['isJoin']) ? false : true;
	}

	/**
	 * 获取成员列表
	 */
	public function getClubMember(){
		$outf = array();
		if(!empty($this->info['members'])){
			foreach ($this->info['members'] as $uid => $val){
				$outf[] = $uid;
			}
		}
		return $outf;
	}

	/**
	 * 获取建筑当前信息
	 */
	public function getBuildInfo($bId){

		$club_building_up = Game::getcfg('club_building_up');
		$bLv = $this->info['lsjLv'];

		switch ($bId) {
			case 1:
				$bLv = $this->info['lsjLv'];
				break;
			case 2:
				$bLv = $this->info['spLv'];
				break;
			case 3:
				$bLv = $this->info['jytLv'];
				break;
		}
		if(empty($bLv)){
			$bLv = 1;
		}
		$buildInfo = array();
		foreach ($club_building_up as $k => $v) {

			if ($bId == $v["building_type"] && $bLv == $v["lv"]) {

				$buildInfo = $v;
				break;
			}
		}

		return $buildInfo;
	}

	/**
	 * 建筑升级
	 * @param unknown_type $bId
	 */
	public function buildLevelUp($bId)
	{
		$newLv = 1;
		$data = array();
		switch ($bId) {
			case 1:
				$this->info['lsjLv']++;
				$newLv = $this->info["lsjLv"];
				$data = array('lsjLv' => $this->info['lsjLv']);
				break;
			case 2:
				$this->info['spLv']++;
				$newLv = $this->info["spLv"];
				$data = array('spLv' => $this->info['spLv']);
				break;
			case 3:
				$this->info['jytLv']++;
				$newLv = $this->info["jytLv"];
				$data = array('jytLv' => $this->info['jytLv']);
				break;
		}

		$this->update($data);
		return $newLv;
	}

	/**
	 * 删除/解散联盟
	 * @param unknown_type $params
	 * $params['password'] : 联盟密码
	 */
	public function delClub()
	{
	
		$cid = $this->info['cid'];
		$now = Game::get_now();
		if(!empty($this->info['dissolutionTime']) && $now < $this->info['dissolutionTime']){
			return false;
		}

		foreach($this->info['members'] as $uid => $v){
			$Act40Model = Master::getAct40($uid);
			$cid = $Act40Model->info['cid'];
			if(empty($cid)){
				continue;
			}
			$Act40Model->outClub($cid);
			$this->goout_club($uid);
		}
		//删除公会  and  删除redis
		$this->del_club($cid,$uid);
		$this->delete_cache();
	}

	/**
	 * 发放公会排行奖励
	 */
	public function sendRankAward(){
		$cid = $this->info['cid'];
		$Sev12Model = Master::getSev12($cid);
		if(!$Sev12Model->bossIsEnd()){
			return;
		}
		$Sev13Model = Master::getSev13($cid);
		$logsArr = $Sev13Model->getSortLog();

		$rid = 0;
		foreach($logsArr as $k => $v){
			if(empty($this->info['members'][$v['uid']])){
				continue;
			}
			$rid++;
			$newRid = $rid;
			$mailModel = new MailModel($v['uid']);
			$title = MAIL_CLUB_BOSS_RANK_TITLE;
			$tips = MAIL_CLUB_BOSS_RANK_CONTENT_1.'|'.$newRid.'|'.MAIL_CLUB_BOSS_RANK_CONTENT_2;
			$bossRankCfg = Game::getcfg('boss_rank');
			foreach($bossRankCfg as $bsRank){
				if($newRid >= $bsRank['max'] && $newRid <= $bsRank['min']){
					//获取配置
					$mailModel->sendMail($uid,$title,$tips,1,$bsRank['rwd']);
					$mailModel->destroy();
					break;
				}
			}
		}
		$Sev13Model->delLogs();
	}

	/**
	 * 发放宴会奖励
	 */
	public function sendPartyAward(){
		$cid = $this->info['cid'];
		$Sev17Model = Master::getSev17($cid);
		if($Sev17Model->info['partyLv'] <= 0){
			return;
		}
		if(!$Sev17Model->isEnd()){
			return;
		}
		$partyCfg = Game::getcfg_info('party',$Sev17Model->info['partyLv']);
		
		$totalTime = Game::getcfg_param('club_partyOnhookTime');
		$intvel = Game::getcfg_param('club_partyOneTime');
		$endTime = strtotime(date('Y-m-d 23:30:00', time()));
		
		foreach($this->info['members'] as $uid => $mInfo){
			$mailModel = new MailModel($uid);
			//发放宴会挂机奖励 没有手动领取的发送邮件
			$Act770Model = Master::getAct770($uid);
			if($Act770Model->info['hookStart'] > 0 && $Act770Model->info['isHookPick'] == 0){
				$title1 = MAIL_CLUB_PARTY_HOOK_TITLE;
				$tips1 = MAIL_CLUB_PARTY_HOOK_CONTENT;

				$Act768Model = Master::getAct768($uid);
				$buffRate = 1;
				if($Act768Model->info['buff'] > 0){
					$partyBuffCfg = Game::getcfg_info('party_buff',$Act768Model->info['buff']);
					$buffRate = 1+$partyBuffCfg['buff']/100;
				}
				$itemsArr = array();
				
				if($endTime - $Act770Model->info['hookStart'] < $totalTime){
					$totalTime = $endTime - $Act770Model->info['hookStart'];
				}
				$pickCount = ceil($totalTime/$intvel);
				foreach($partyCfg['food_rwd'] as $k => $items){
					$total = ceil($items['count']*$pickCount*$buffRate);
					$item = array('id' => $items['id'],'count' => $total,'kind' => $items['kind']);
					$itemsArr[] = $item;
				}
				$mailModel->sendMail($uid,$title1,$tips1,1,$itemsArr);
			}

			//宴会结束后 所有成员的奖励发放邮件获取
			$title = MAIL_CLUB_PARTY_TITLE;
			$tips = MAIL_CLUB_PARTY_CONTENT;
			
			$mailModel->sendMail($uid,$title,$tips,1,$partyCfg['personal_rwd']);

			$mailModel->destroy();
		}
		$Sev17Model->info['partyLv'] = 0;
		$Sev17Model->save();
	}


}
