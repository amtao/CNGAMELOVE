<?php
require_once "ActBaseModel.php";
/*
 * 新版酒楼-个人宴会信息
 */
class Act170Model extends ActBaseModel
{
	public $atype = 170;//活动编号
	
	public $comment = "个人宴会信息";
	public $b_mol = "boite";//返回信息 所在模块
	public $b_ctrl = "yhInfo";//返回信息 所在控制器
	public $cfg;
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'type' => 0, //1:家宴  2:官宴
		'ep' => 0, //当前宴会总属性
		'ctime' => 0, //开宴时间
        'addPer' => 0, //宴会加成
        'count' => 0,//今天开家宴会次数
        'guancount' => 0,//今天开官宴次数
		'list' => array(
			/*
			 席位id => array(
				 uid ,  来贺礼的玩家id
				 hid ,  来贺礼的门客id
			     ep, 门客的势力
				 show ,  是否已显示
			 )
			 */
		), //参加宴会列表
	);

	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out(){
		//获取剩余时间
		$next = 0;  //倒计时绝对时间
		$base = 0;
		if(in_array($this->info['type'],array(1,2))){
			$cfg_yanhui = Game::getcfg_info('boite_yanhui',$this->info['type']);
			//获取剩余时间
			$next = $cfg_yanhui['dtime']*3600+$this->info['ctime'];
			$base = $cfg_yanhui['base'];
		}
		//构造输出
		$fUserModel = Master::getUser($this->uid);

		$this->outf = array();
		$this->outf['id'] = $this->info['type'];   //宴会类型
		$this->outf['uid'] = $this->uid;   //玩家uid
		$this->outf['job'] = $fUserModel->info['job'];   //头像编号
		$this->outf['sex'] = $fUserModel->info['sex'];   //性别1男2女
		$this->outf['level'] = $fUserModel->info['level'];   //官阶
		$this->outf['name'] = $fUserModel->info['name'];   //玩家名字
        $this->outf['ep'] = 0;//参与门客总属
        $this->outf['addPer'] = $this->info['addPer'];
        $this->outf['count'] = $this->info['count'];
        $this->outf['guancount'] = $this->info['guancount'];
		$this->outf['ltime'] = array(   //倒计时
			'next' => $next,//下次绝对时间
			'label' => 'yhInfoltime',
		);

		$num = 0; //参加宴会的人数
		$this->outf['list'] = array();
		if(!empty($this->info['list'])){
			//宴会列表
			foreach($this->info['list'] as $k => $v){
				$this->outf['list'][$k]['uid'] = $v['uid'];
				$this->outf['list'][$k]['hid'] = $v['hid'];
				$this->outf['list'][$k]['ep'] = $v['ep'];
				$this->outf['ep'] += $v['ep'];
				$this->outf['list'][$k]['id'] = $k;
				if(!empty($v['uid'])){
					$num ++;  //参加宴会的人数
					$fuidInfo = Master::fuidInfo($v['uid']);
					$this->outf['list'][$k]['name'] = $fuidInfo['name'];
					$this->outf['list'][$k]['job'] = $fuidInfo['job'];
					$this->outf['list'][$k]['sex'] = $fuidInfo['sex'];
				}
			}
			$this->outf['list'] = array_values($this->outf['list']);
		}
		$this->outf['num'] = $num;    //当前宴会参加人数
		$this->outf['maxnum'] = count($this->info['list']);    //当前宴会参加人数
        $this->outf['score'] = $base + $num * 50 + floor($this->outf['ep']/ 20000);    //当前宴会获得积分
	}
	
	
	/**
	 * 占席位
	 * @param int $wxid   席位id
	 * @param int $fuid     对应的玩家id
	 * @param int $hid   门客id
	 * @param int $ep 势力
	 */
	public function add_xiwei($wxid,$fuid,$hid,$ep){
		if(empty($this->info['type'])){
			Master::error(BOITE_FEAST_END);
		}
		$yanhui_cfg = Game::getcfg_info('boite_yanhui',$this->info['type']);
		$count = 0; //记录已参加宴会的人数
		foreach($this->info['list'] as $k => $v){
			if($v['uid'] == $fuid && $v['hid'] == $hid){
				Master::error(BOITE_FEAST_HAVE_ATTEND);
			}
			if(empty($v['uid'])){
				continue;
			}
			$count ++;
		}
		if($count >= $yanhui_cfg['xiwei']){
			Master::error(BOITE_FEAST_END);
		}
		if(!empty($this->info['list'][$wxid]['uid'])){
			Master::error(BOITE_SEATE_USERED);
		}

		if(empty($this->info['list'][$wxid])){
			Master::error(BOITE_FEAST_PARAM_ERROR);
		}

        //处理折损和加成
        $lost = 0;
        if ($fuid == $this->uid){
            $cfg_yanhui = Game::getcfg_info('boite_yanhui',$this->info['type']);
            $lost = $cfg_yanhui['reduce'];
        }
        $ep = ceil($ep * (1 - $lost / 10000) * (1 + $this->info['addPer'] / 10000));

		$this->info['list'][$wxid] = array(
			'uid'  => $fuid, //来贺礼的玩家id
			'hid' => $hid, //来贺礼的门客id
			'ep' => $ep,
			'show' => 0,  //是否已展示 0:未展示   1:已展示
		);
		
		$this->save();
	}

	public function updateJYCount(){
	    if ($this->info['ctime'] < Game::day_0()){
            $this->info['count'] = 0;
            $this->info['guancount'] = 0;
        }

    }
	
	/**
	 * 开启宴会的id
	 * @param int $id   1:家宴  2:官宴
	 * @param int $isOpen 是否公开
	 */
	public function open_yh($id, $isOpen = 0, $addItem1 = 0, $addItem2 = 0, $addItem3 = 0)
    {
        //判断今天办理家宴次数
        $this->updateJYCount();
        if ($id == 1) {
            $UserModel = Master::getUser($this->uid);
            //获得VIP配置
            $vip_cfg_info = Game::getcfg_info('vip', $UserModel->info['vip']);
            if ($this->info['count'] + 1 > $vip_cfg_info['yanhui']) {
                Master::error(BOITE_TODAY_OPEN_LIMIT);
            }
            $this->info['count'] = $this->info['count'] + 1;
        }
        else if ($id == 2){
            $UserModel = Master::getUser($this->uid);
            //获得VIP配置
            $vip_cfg_info = Game::getcfg_info('vip', $UserModel->info['vip']);
            if ($this->info['guancount'] + 1 > $vip_cfg_info['guanyan']) {
                Master::error(BOITE_TODAY_OPEN_GUAN);
            }
            $this->info['guancount'] = $this->info['guancount'] + 1;
        }

        //物品加成
        $addPer = 0;
        if ($addItem1 != 0) {
            Master::sub_item($this->uid, KIND_ITEM, $addItem1, 1);
            $addSys = Game::getcfg_info("boite_add", $addItem1);
            $addPer += $addSys['addition'];
        }
        if ($addItem2 != 0) {
            Master::sub_item($this->uid, KIND_ITEM, $addItem2, 1);
            $addSys = Game::getcfg_info("boite_add", $addItem2);
            $addPer += $addSys['addition'];
        }
        if ($addItem3 != 0) {
            Master::sub_item($this->uid, KIND_ITEM, $addItem3, 1);
            $addSys = Game::getcfg_info("boite_add", $addItem3);
            $addPer += $addSys['addition'];
        }

        if (!empty($this->info['type']) && !Game::is_over($this->outf['ltime']['next'])) {
            Master::error(BOITE_FEAST_PLAYING);
        }

        //获取对应宴会配置
        $yanhui_cfg = Game::getcfg_info('boite_yanhui', $id);
        if (empty($yanhui_cfg)) {
            Master::error(BOITE_FEAST_PARAM_ERROR);
        }
        //家宴第一次举办不消耗道具
        if ($this->info['count'] > 1 || $id == 2){
            //花费宴会材料
            foreach ($yanhui_cfg['pay'] as $v) {
                Master::sub_item($this->uid, KIND_ITEM, $v['id'], $v['count']);
            }
        }


        //花费钻石
        if ($yanhui_cfg['cost'] > 0){
            Master::sub_item($this->uid, KIND_ITEM, 1, $yanhui_cfg['cost']);
        }

        $this->info['type'] = $id;
        $this->info['score'] = 0;
        $this->info['ctime'] = Game::get_now();
        $this->info['addPer'] = $addPer;
        $this->info['list'] = array();
        for ($i = 1; $i <= $yanhui_cfg['xiwei']; $i++) {
            $this->info['list'][$i] = array(
                'id' => $i, //席位id
                'uid' => 0, //来贺礼的玩家id
                'hid' => 0,  //来贺礼的门客id
                'ep' => 0, //门客的势力
                'show' => 0,  //是否已展示 0:未展示   1:已展示
            );
        }

        //联盟信息
        $Act40Model = Master::getAct40($this->uid);
        $cid = $Act40Model->info['cid'];

        //宴会全盟可见
        if ($id == 1 && !empty($cid) && $isOpen == 0) {
            $Sev20Model = Master::getSev20($cid);
            $Sev20Model->add_yh($this->uid);
        }

        //官宴会全服可见
        if ($id == 2) {
            $Sev21Model = Master::getSev21();
            $Sev21Model->add_yh($this->uid);
        }

        //家宴全服可见
        if ($id == 1 && $isOpen == 1) {
            $Sev29Model = Master::getSev29();
            $Sev29Model->add_yh($this->uid);
        }

        //家宴公开或者官宴发聊天广播
        if (($id == 1 && $isOpen == 1) || $id == 2) {
            $Sev6012Model = Master::getSev6012();
            $msg = "#boite#::".($id == 1?$id:$id);
            $Sev6012Model->add_msg($this->uid, $msg, 3);
        }

        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_add(54, 1);

		$this->save();
		

        $data = array();
        $data['type'] = $id;
        $data['count'] = $this->info['count'];
        $data['guancount'] = $this->info['guancount'];
        Master::back_data($this->uid, 'boite', 'yhType', $data);
    }
	
	/**
	 * 清空显示状态
	 */
	public function clear_show(){
		
		$outf = array(  //返回弹窗信息
			'maxnum' => 0,
			'isover' => 1, //是不是结束了
			'allscore' => 0,
			'allep' => 0, //总属性
            'addPer' => 0,
			'list' => array(),  //弹窗列表
		);

		$isOver = 1;
		foreach($this->info['list'] as $k => $v){
			//未坐下的席位
			if(empty($v['uid'])){
				$isOver = 0;
				continue;
			}
			$fUserModel = Master::getUser($v['uid']);
            $outf['allep'] += $v['ep'];
            $outf['list'][] = array(
			    'id' => $v['uid'],
				'name' => $fUserModel->info['name'],
				'ep' => $v['ep'],
			);
            $outf['maxnum'] ++;
		}
        $outf['isover'] = $isOver;
		
		//获取返回配置
		$cfg_yanhui = Game::getcfg_info('boite_yanhui',$this->info['type']);
		//获取剩余时间
		$ltime = $cfg_yanhui['dtime']*3600 + $this->info['ctime']-$_SERVER['REQUEST_TIME'];
		if($ltime <= 0){
			$outf['isover'] = 1;
		}

		$outf['oldtype'] = $this->info['type'];
		$base = empty($cfg_yanhui['base']) ? 0 : $cfg_yanhui['base'];
		$outf['allscore'] =  $base + $outf['maxnum'] * 50 + floor($outf['allep']/ 20000);
		return $outf;
	}
	
	/**
	 * 关闭宴会
	 */
	public function close_yh(){
	    $this->updateJYCount();
		//家宴,删除联盟公共数据
        $this->clear_hid();

		if($this->info['type'] == 1){
			$Act40Model = Master::getAct40($this->uid);
			$cid = $Act40Model->info['cid'];
			if(!empty($cid)){
				$Sev20Model = Master::getSev20($cid);
				$Sev20Model->sub_yh($this->uid);
			}
		}
		//官宴,删除全服公共数据
		$Sev21Model = Master::getSev21();
		$Sev21Model->sub_yh($this->uid);
		
		//家宴公开,删除全服公共数据
		$Sev29Model = Master::getSev29();
		$Sev29Model->sub_yh($this->uid);
		
		$this->info['type'] = 0;
		$this->save();
	}
	
	/**
	 * 检查宴会是否已过期/参加宴会人员已满
	 */
	public function check_yh(){
		if(empty($this->info['type'])){
			Master::error(BOITE_FEAST_END);
		}
		//获取返回配置
		$cfg_yanhui = Game::getcfg_info('boite_yanhui',$this->info['type']);
		if(empty($cfg_yanhui)){
			Master::error(BOITE_FEAST_END);
		}
		//获取剩余时间
		$ltime = $cfg_yanhui['dtime']*3600+$this->info['ctime']-$_SERVER['REQUEST_TIME'];
		//判断参加宴会人员已满
		$flag = 1;  //标志是否参加宴会人员已满 0:未满  1:已满
		foreach($this->info['list'] as $k => $v){
			if(empty($v['uid'])){
				$flag = 0;
			}
		}
		//如果过期  或者参加宴会人员已满
		if($ltime < 0 || $flag){
			Master::error(BOITE_FEAST_END);
		}
	}
	
	/**
	 * 检查宴会是否结束
	 */
	public function is_over(){
		if(empty($this->info['type'])){
			return true;
		}
		//获取返回配置
		$cfg_yanhui = Game::getcfg_info('boite_yanhui',$this->info['type']);
		if(empty($cfg_yanhui)){
			Master::error(BOITE_FEAST_END);
		}
		//获取剩余时间
		$ltime = $cfg_yanhui['dtime']*3600+$this->info['ctime']-$_SERVER['REQUEST_TIME'];
		//判断参加宴会人员已满
		$flag = 1;  //标志是否参加宴会人员已满 0:未满  1:已满
		foreach($this->info['list'] as $k => $v){
			if(empty($v['uid'])){
				$flag = 0;
			}
		}
		//如果过期  或者参加宴会人员已满
		if($ltime < 0 || $flag){
			return true;
		}
		return false;
	}
	
	public function clear_hid(){
        $uids = array();
        foreach ($this->info['list'] as $lv) {
            //过滤空席位
            if (empty($lv['uid'])) {
                continue;
            }
            $uids[] = $lv['uid'];
        }

        foreach ($uids as $keyId) {
            $Act172Model = Master::getAct172($keyId);
            $Act172Model -> setOver($this->uid);
        }
    }
	
	
}
















