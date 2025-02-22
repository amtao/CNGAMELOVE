<?php
require_once "ActBaseModel.php";
/*
 * 寻访
 */
class Act28Model extends ActBaseModel
{
	public $atype = 28;//活动编号
	
	public $comment = "寻访-赈灾-转运";
	public $b_mol = "xunfang";//返回信息 所在模块
	public $b_ctrl = "zhenZai";//返回信息 所在控制器
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(  
		'used' => 0,  //已使用的免费转运次数 
		'num' => 0,  //赈灾次数 
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		
		$outf = array(
			'used' => $this->info['used'],
			'num' => $this->info['num'],
		);
		//构造输出
		$this->outf = $outf;
	}
	
	/**
	 * 银两,粮草 赈灾
	 * @param $type   2:银两  3:粮草
	 */
	public function zhenzai($type){
		
		$this->info['num'] ++;
		//扣资源
        $Act1Model = Master::getAct1($this->uid);
        $res = $Act1Model->get_onetime_Num($type);
        $need = Game::getCfg_formula()->city_lucky($res, $this->info['num']);
		Master::sub_item($this->uid,KIND_ITEM,$type,$need);
		
		//加运势
		$Act27Model = Master::getAct27($this->uid);
		$Act27Model->add_ys(2);  //每次加2点运势
		
		$this ->save();
		
		//活动消耗 - 限时赈灾次数
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->xianshi_huodong('huodong218',1);
	}
	
	/**
	 * 黄金转运
	 */
	public function zhuanyun(){
		
		//用户信息
		$UserModel = Master::getUser($this->uid);
		//获得VIP配置
		$vip_cfg_info = Game::getcfg_info('vip',$UserModel->info['vip']);
		//免费转运次数
		$vip_cfg_info['free_zy'];
		//有免费次数则使用免费次数
		if($vip_cfg_info['free_zy'] > $this->info['used'] ){
			$this->info['used'] ++;
		}else{
			//使用黄金--初始化
			$pay_init = array(
		    // 运势  =>  需要多少黄金
			    0 => 10,
			    61 => 30,
			    81 => 50,
			);
			//获取当前运势值
			$Act27Model = Master::getAct27($this->uid);
			$Act27Model->info['num'];//当前运势
			
			//需要花费的黄金
			$need = 0; 
			foreach($pay_init as $k => $v){
				if($Act27Model->info['num'] >= $k){
					$need = $v;
				}
			}
			Master::sub_item($this->uid,KIND_ITEM,1,$need);
		}
		
		//加运势
		$Act27Model = Master::getAct27($this->uid);
		$Act27Model->add_ys(10);  //钻石每次加10点运势
		
		$this ->save();
	}
	
	/**
	 * 银两,粮草自动赈灾
	 * @param $auto2 0:自动银两赈灾未设置  1:自动银两赈灾已设置
	 * @param $auto3 0:自动粮草赈灾未设置  1:自动粮草赈灾已设置
	 * @param $ysSet  运势设置
	 * 先扣除粮草  在扣除银两
	 */
	public function auto_zhenzai($auto2,$auto3,$ysSet){
		if(!$auto2 && !$auto3){
			return 0;
		}
		if($ysSet > 90){
			Master::error("auto_zhenzai_ysSet_err_".$ysSet);
		} 

		//获取当前运势
		$Act27Model = Master::getAct27($this->uid);
		$UserModel = Master::getUser($this->uid);
		$orgNum = $this->info['num'];

		//自动赈灾粮草
		if($auto3){  //有设置自动赈灾
            $this->auto_zzType($UserModel->info['food'], $ysSet, 3);
		}
		
		//自动赈灾银两
		if($auto2){  //有设置自动赈灾
			//在获取  银两  可赈灾次数
            $this->auto_zzType($UserModel->info['coin'], $ysSet, 2);
		}

		if(($auto3 || $auto2) && $ysSet > $Act27Model->info['num']){
			Master::error_msg(LOOK_FOR_GOODS_SHORT);
			return;
		}

		$this->save();
		
		//活动消耗 - 限时赈灾次数
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->xianshi_huodong('huodong218', $this->info['num'] - $orgNum);
	}

	private function auto_zzType($all, $ysSet, $type){
        $Act27Model = Master::getAct27($this->uid);
        $Act1Model = Master::getAct1($this->uid);

        $num = $this->info['num']; //玩家赈灾次数
        $yunshi = $Act27Model->info['num']; //当前运势
        //获取可赈灾次数
        $c = ceil(($ysSet - $yunshi) / 2);
        $need = 0;
        $add = 0;
        $res = $Act1Model->get_onetime_Num($type);
        //实际可赈灾次数
        for($i = 0; $i < $c; $i ++){
            $n = Game::getCfg_formula()->city_lucky($res, $i + $num + 1);
            if ($need + $n > $all){
                break;
            }
            $need += $n;
            $add += 1;
        }

        if($need > 0){
            $this->info['num'] += $add;  //加赈灾次数
            $Act27Model->add_ys($add*2, 100,false); //加运势
            Master::sub_item($this->uid,KIND_ITEM, $type, $need);
        }
    }
}
















