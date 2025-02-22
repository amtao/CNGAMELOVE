<?php
require_once "ActBaseModel.php";
/*
 * 寻访
 */
class Act26Model extends ActBaseModel
{
	public $atype = 26;//活动编号

	public $comment = "寻访";
	public $b_mol = "xunfang";//返回信息 所在模块
	public $b_ctrl = "xfInfo";//返回信息 所在控制器
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		'num' => 0,    //当前体力
		'time' => 0,   //开始恢复时间
        'dayCount'=>0,//今日次数
        'lastTime'=>0,//定点最后一次时间
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		
		//用户信息
		$UserModel = Master::getUser($this->uid);
		//获得VIP配置
		$vip_cfg_info = Game::getcfg_info('vip',$UserModel->info['vip']);
		//最大体力
        $max_num = $vip_cfg_info['tili'];
        //$max_num = 10;

		//计算恢复时间
        //$hf_num = Game::hf_num($this->info['time'],60*30,$this->info['num'],$max_num);
        $hf_num = Game::hf_num($this->info['time'],60*30,$this->info['num'],$max_num);

		$this->info['num'] = $hf_num['num'];
		$this->info['time'] = $hf_num['stime'];
        $this->info['dayCount'] = $this->info['lastTime'] < Game::day_0()?0:$this->info['dayCount'];
        $this->info['lastTime'] = Game::get_now();
		
		//输出结构体
		$outf = array(
		    'count' => $this->info['dayCount'],
			'lastTime' => $this->info['lastTime'],
			'num' => $this->info['num'],
		    'next' => $hf_num['next'],//下次绝对时间
			'label' => 'xunfangtili',
		);
		
		//构造输出
		$this->outf = $outf;

	}
	
	/**
	 * 加体力
	 */
	public function add_num($count){
		//用户信息
		$UserModel = Master::getUser($this->uid);
		//获得VIP配置
		$vip_cfg_info = Game::getcfg_info('vip',$UserModel->info['vip']);
		
		$this->info['num'] = $vip_cfg_info['tili']*$count;
		$this->save();
	}

    /**
     * 做任务加体力
     */
    public function add_num_task($num){
        $this->info['num'] += $num;
        $this->save();
    }
	
	
	/**
	 * 寻访-消耗体力
	 */
	public function sub_num($num, $build = 0){
		$this->info['num'] -= $num;
		if($this->info['num'] < 0 ){
			Master::error(LOOK_FOR_POWER_SHORT);
		}
		if ($build != 0){
		    $this->sub_dingdian();
        }
		$this->save();
	}

    /**
     * 寻访-扣除定点黄金
     */
    public function sub_dingdian(){
        if ($this->info['lastTime'] < Game::day_0()){
            $this->info['dayCount'] = $this->info['lastTime'] < Game::day_0()?0:$this->info['dayCount'];
            $this->info['lastTime'] = Game::get_now();
        }
        $count = $this->info['dayCount'];
        $this->info['dayCount'] = $count + 1;
        $cost = Game::getcfg_param("xunfang_city_jiage");
        $add = Game::getcfg_param("xunfang_city_jiage_add");
        $c = $cost + $add * $count;
        Master::sub_item($this->uid,KIND_ITEM,1, $c);
    }
	
}




