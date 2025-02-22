<?php
/*
 * 翰林院 房间座位信息
 */
require_once "SevListBaseModel.php";
class Sev9Model extends SevBaseModel
{
	public $comment = "翰林院座位详细信息";
	public $act = 9;//活动标签
	
	public $b_mol = "hanlin";//返回信息 所在模块
    public $b_ctrl = "desk";//返回信息 所在控制器
	
	public $_init = array(//翰林院座位
		'master' => array(),//主人
		'desk' => array(//学生座位
			/*
			 * 1 => fuser
			 */
		),
		'log' => array(),//日志
	);
	
	/*
	 * $hid = 1,$cid = 1
	 * 按照人分组
	 */
	public function __construct($hid,$cid){
		parent::__construct(1,$cid);
		
		//如果时间已经过期 则重新初始化
		if (!empty($this->info['master']['num'])
		&& Game::is_over($this->info['master']['num'])){
			$this->info = $this->_init;
		}
	}
	
	/*
	 * 检查当前房间状态是否为指定状态
	 * 0:无效
	 * 1:有效
	 */
	public function click_state($state,$only_click = false){
		if ($state == 0){
			if (empty($this->info['master'])){
				return true;
			}else{
				if ($only_click){
					return false;
				}else{
					Master::error(SEV_9_ROOMOPEN);
				}
			}
		}else{
			if (!empty($this->info['master'])){
				return true;
			}else{
				if ($only_click){
					return false;
				}else{
					Master::error(SEV_9_ROOMDOWN);
				}
			}
		}
		
	}
	
	/*
	 * 初始化房间
	 */
	public function open($fuser){
		//当前是否为空房
		$this->click_state(0);
		//num 过期时间
		$this->info['master'] = $fuser;
		$this->save();
	}
	
	/*
	 * 进入一个人
	 */
	public function sitdown($fuser){
        //判断是否已经在席位中
        $this->check_uid($fuser['uid']);
		//房主工会ID
		$master_Act40Model = Master::getAct40($this->info['master']['uid']);
		$master_cid = $master_Act40Model->info['cid'];
		//我的工会ID
		$Act40Model = Master::getAct40($fuser['uid']);
		$cid = $Act40Model->info['cid'];
		if ($master_cid > 0 && $cid > 0
		&& $master_cid == $cid){
			//如果我和房主在同一工会 保护时间1个小时
			$b_time = 3600;
		}else{
			//保护时间 30分钟
			$b_time = 1800;
		}
		
		$fuser['num2'] = Game::get_now();//开始时间
		$fuser['num'] = Game::get_over($b_time);//保护时间
        $fuser['type'] = 0;//锁定状态
		$fUserModel = Master::getUser($fuser['uid']);
		$fuser['exp'] = $fUserModel->info['exp'];//政绩信息 记录在桌子上 用来PK
		$this->info['desk'][$fuser['rid']] = $fuser;
		$this->save();
	}
	
	/*
	 * 构造业务输出数据
	 */
	public function mk_outf(){
		$desks = array();
		foreach ($this->info['desk'] as $k => $v){
			//$v['rid'] = $k;
			unset($v['exp']);//去掉经验值
			$desks[] = $v;//去掉座位下标
		}
		
		return array(
			'master' => $this->info['master'],
			'desks' => $desks,
			'log' => $this->info['log'],
		);
	}
	
	
	/*
	 * 记录LOG 1创建 2 进入空座位 3T人成功 4T人失败 5 锁定
	 */
	public function addlog($log){
		
		$log['time'] = Game::get_now();
		$this->info['log'][] = $log;
		$this->save();
	}
	
	
	/*
	 * 返回协议信息
	 * 返回房间弹窗信息
	 */
	public function back_data($tcode = array()){
		$outf = $this->get_outf();
		
		//写入T人冷却时间
		foreach($outf['desks'] as &$v){
			$my_code = 0;//我对这个人的单独冷却时间
			if (isset($tcode[$v['uid']]) 	//如果我有T这个人的记录
			&& !Game::is_over($tcode[$v['uid']])){	//并且冷却时间还没过
				$my_code = $tcode[$v['uid']];
			}
			//对比这个人本来的保护时间
			$btime = max($my_code,$v['num']);
			if(Game::is_over($btime)){
				$btime = 0;
			}
			$v['num'] = $btime;
		}
		
		Master::back_data(0,'hanlin','desk',$outf);
	}

	/*
	 * 锁定玩家
	 */
	public function baohu($rid){
	    //验证房主是否还有保护人的次数
        if(!isset($this->info['master']['suoding'])){//兼容旧数据
            $this->info['master']['suoding'] = 1;
        }
        if($this->info['master']['suoding'] < 1){//判断是否有保护次数
            Master::error(NO_PROTECT_TIME);
        }
        $this->info['desk'][$rid]['num'] = $this->info['master']['num'];//保护时间等于房间结束时间
        $this->info['desk'][$rid]['type'] = 1;//改变锁定状态
        $this->info['master']['suoding'] -= 1;//次数减去1
        $this->addlog(//添加日志
            array(
                'name1' => $this->info['desk'][$rid]['name'],
                'type' => 5,
            )
        );
        $this->save();
        $this->back_data();
    }

    /*
     * 判断玩家是否已坐进座位中
     */
    public function check_uid($uid){
        if(isset($this->info['desk'])){
            foreach ($this->info['desk'] as $k => $v){
                if($v['uid'] == $uid){
                    Master::error(SEV_9_DESK_ERROR);
                }
            }
        }
    }
}
