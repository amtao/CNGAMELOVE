<?php
require_once "ActBaseModel.php";
/*
 * 酒楼-个人宴会信息
 */
class Act50Model extends ActBaseModel
{
	public $atype = 50;//活动编号
	
	public $comment = "个人宴会信息";
	public $b_mol = "jiulou";//返回信息 所在模块
	public $b_ctrl = "yhInfo";//返回信息 所在控制器
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'type' => 0, //1:家宴  2:官宴 
		'ctype' => 0, //1:家宴  2:官宴 备用
		'score' => 0, //当前宴会分数
		'ctime' => 0, //开宴时间
		'list' => array(
		
			/*
			 席位id => array(
				 uid ,  来贺礼的玩家id
				 礼金类型   ,: 1:100礼金  2:500 礼金   3:礼盒  4:老鼠
				 show ,  是否已显示
			 )
			 */
		), //参加宴会列表
	);
	
	
	/**
	 * 占席位
	 * @param unknown_type $wxid   席位id
	 * @param unknown_type $uid    对应的玩家id
	 * @param unknown_type $type   礼金类型
	 */
	public function add_xiwei($wxid,$uid,$type){
		if(empty($this->info['type'])){
			Master::error(BOITE_FEAST_END);
		}
		$yanhui_cfg = Game::getcfg_info('jl_yanhui',$this->info['type']);
		$count = 0; //记录已参加宴会的人数
		foreach($this->info['list'] as $k => $v){
			if($v['uid'] == $uid){
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
		
		$this->info['list'][$wxid] = array(
			'uid'  => $uid, //来贺礼的玩家id
			'type' => $type, //1:100礼金  2:500 礼金   3:礼盒  4:老鼠 
			'show' => 0,  //是否已展示 0:未展示   1:已展示
		);
		
		$this->save();
	}
	
	/**
	 * 开启宴会的id
	 * @param unknown_type $id   1:家宴  2:官宴
	 */
	public function open_yh($id,$isOpen = 0){
		
		if(!empty($this->info['type']) && !Game::is_over($this->outf['ltime']['next'])){
			Master::error(BOITE_FEAST_PLAYING);
		}
		//获取对应宴会配置
		$yanhui_cfg = Game::getcfg_info('jl_yanhui',$id);
		if(empty($yanhui_cfg)){
			Master::error(BOITE_FEAST_PARAM_ERROR);
		}
		//花费宴会材料
		foreach($yanhui_cfg['pay'] as $v ){
			Master::sub_item($this->uid,KIND_ITEM,$v['id'],$v['count']);
		}

		//花费钻石
		Master::sub_item($this->uid,KIND_ITEM,1,$yanhui_cfg['cost']);

		$this->info['type'] = $id;
		$this->info['ctype'] = $id;
		$this->info['score'] = 0;
		$this->info['ctime'] = $_SERVER['REQUEST_TIME'];
		$this->info['list'] = array();
		for( $i = 1; $i <= $yanhui_cfg['xiwei']; $i++){
			$this->info['list'][$i] = array(
				'id' => $i, //席位id
				'uid'  => 0, //来贺礼的玩家id
				'type' => 0, //1:100礼金  2:500 礼金   3:礼盒  4:老鼠
				'show' => 0,  //是否已展示 0:未展示   1:已展示
			);
		}
		
		//联盟信息
		$Act40Model = Master::getAct40($this->uid);
		$cid = $Act40Model->info['cid'];
		
		//宴会全盟可见
		if($id == 1 && !empty($cid) && $isOpen == 0){
			$Sev20Model = Master::getSev20($cid);
			$Sev20Model->add_yh($this->uid);
		}
		
		//官宴会全服可见
		if($id == 2){
			$Sev21Model = Master::getSev21();
			$Sev21Model->add_yh($this->uid);
		}
		
		//家宴全服可见
		if( $id == 1 && $isOpen == 1){
			$Sev29Model = Master::getSev29();
			$Sev29Model->add_yh($this->uid);
		}
		
		$this->save();
	}
	
	
	/*
	 * 构造输出结构体
	 * 修改保存结构体
	 */
	public function make_out(){
		
		//获取配置
		$next = 0;  //倒计时绝对时间
		$name = ''; //宴会名字
		if(in_array($this->info['type'],array(1,2))){
			$cfg_yanhui = Game::getcfg_info('jl_yanhui',$this->info['type']);
			$name = $cfg_yanhui['name'];
			//获取剩余时间
			$next = $cfg_yanhui['dtime']*3600+$this->info['ctime'];
		}
		
		//构造输出
		$fUserModel = Master::getUser($this->uid);
			
		$this->outf = array();
		$this->outf['id'] = $this->info['ctype'];   //宴会类型
		$this->outf['uid'] = $this->uid;   //玩家uid
		$this->outf['job'] = $fUserModel->info['job'];   //头像编号
		$this->outf['sex'] = $fUserModel->info['sex'];   //性别1男2女
		$this->outf['level'] = $fUserModel->info['level'];   //官阶
		$this->outf['name'] = $fUserModel->info['name'];   //玩家名字
		$this->outf['score'] = 0;    //当前宴会获得积分
		$this->outf['ltime'] = array(   //倒计时
			'next' => $next,//下次绝对时间
			'label' => 'yhInfoltime',
		);
		
		$num = 0; //参加宴会的人数
		$this->outf['list'] = array();
		if(!empty($this->info['list'])){
			
			//宴会列表
			foreach($this->info['list'] as $k => $v){
				switch($v['type']){
					case 1:
						$this->outf['score'] += 100;
						break;
					case 2:
						$this->outf['score'] += 500;
						break;
					case 3:
						$this->outf['score'] += 1000;
						break;
					case 4:
						$this->outf['score'] -= 1000;
						break;
					default:
						continue;
				}
				$this->outf['list'][$k]['uid'] = $v['uid'];
				$this->outf['list'][$k]['type'] = $v['type'];
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
		
	}
	
	/**
	 * 清空显示状态
	 */
	public function clear_show(){
		
		$list = array(); //如果没有结束 记录宴会人数列表信息
		$outf = array(  //返回弹窗信息
			'maxnum' => 0,
			'bad' => 0,  //有几个方老鼠
			'isover' => 1, //是不是结束了
			'allscore' => 0,//总积分
			'list' => array(),  //弹窗列表
		);  
		$this->info['score'] = 0;
		foreach($this->info['list'] as $k => $v){
			if($v['type'] == 4){
				$outf['bad'] ++;
			}
			//返回弹窗信息
			switch($v['type']){
				case 1:
					$score = 100;
					break;
				case 2:
					$score = 500;
					break;
				case 3:
					$score = 1000;
					break;
				case 4:
					$score = -1000;
					break;
				default:
					continue;
			}
			if(empty($v['uid'])){
				$outf['isover'] = 0;
				continue;
			}
			$this->info['score'] += $score;    //当前宴会分数
			if($v['show']){
				continue;
			}
			$outf['allscore'] += $score;
			$fUserModel = Master::getUser($v['uid']);
			//修改是否显示状态
			$this->info['list'][$k]['show'] = 1;
			$outf['list'][] = array(
				'id' => $v['uid'],
				'name' => $fUserModel->info['name'],
				'score' => $score,
			); 
			$outf['maxnum'] ++;
		}
		
		
		//获取返回配置
		$cfg_yanhui = Game::getcfg_info('jl_yanhui',$this->info['type']);
		//获取剩余时间
		$ltime = $cfg_yanhui['dtime']*3600+$this->info['ctime']-$_SERVER['REQUEST_TIME'];
		if($ltime <= 0){
			$outf['isover'] = 1;
		}
		
		$this->save();
		$outf['oldtype'] = $this->info['type'];
		
		
		return $outf;
	}
	
	/**
	 * 关闭宴会
	 */
	public function close_yh(){
		//家宴,删除联盟公共数据
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
		$cfg_yanhui = Game::getcfg_info('jl_yanhui',$this->info['type']);
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
		$cfg_yanhui = Game::getcfg_info('jl_yanhui',$this->info['type']);
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
	
	
	
	
}
















