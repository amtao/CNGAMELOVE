<?php
require_once "ActBaseModel.php";
/*
 * 名望
 */
class Act20Model extends ActBaseModel
{
	public $atype = 20;//活动编号
	
	public $comment = "名望";
	public $b_mol = "laofang";//返回信息 所在模块
	public $b_ctrl = "mingwang";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		'eday' => 0,  //每日产出
		'mw' => 0,   //当前名望值
	
		'time' => 0, //计算回复声望的时间  最后一次消耗声望的时间
	);

	/*
	 * 构造输出结构体
	 */
	public function make_out(){
        //数据重置
        if (empty($this->info['add'])){
            $this->reset();
        }else{
            //如果不是今天
            if(!Game::is_today($this->info['time'])){
                //过了几天(跨过几个0点)
                $day_count = Game::day_count($this->info['time']);
                //今天0点
                $this->info['time'] = Game::day_0();
                //加名望值
                $this->info['mw'] += ($this->info['eday']+$this->info['add'][2]) * $day_count;

                //最大值不能超过上限
                $this->info['mw'] = min($this->info['mw'],$this->info['eday']*2+$this->info['add'][1]);
            }
        }
		//默认输出直接等于内部存储数据
		$this->outf = array(
			'eday' => $this->info['eday']+$this->info['add'][2],  //每日产出
            'mw' => $this->info['mw'],   //当前名望值
            'maxmw' => $this->info['eday']*2+$this->info['add'][1],   //最大名望值
		);
	}
	
	/**
	 * @param $mw 增加名望值
	 */
	public function add_mw($mw){
		$this->info['mw'] += $mw;
		//最大值不能超过上限
        $this->info['mw'] = min($this->info['mw'],$this->info['maxmw']);
		$this ->save();
	}
	
	/**
	 * @param $mw 减少名望值
	 */
	public function sub_mw($mw){
		if($mw <= 0){
			Master::error(JAIL_RENOWN_SHORT,10001);
		}
        $mw = $mw*(1-$this->info['add'][3]/10000);
        $mw = ceil($mw);
        $this->info['mw'] -= $mw;
		if($this->info['mw'] < 0 ){
			Master::error(JAIL_RENOWN_SHORT,10001);
		}
		$this ->save();
	}
	
	/**
	 * 更新每日产出
	 * @param unknown_type $bmap   大关卡id
	 */
	public function update_eday($bmap){
		$this->info['eday'] = $bmap * 25;
		$this->info['maxmw'] = $this->info['eday'] * 2;
		$this ->save();
	}

    /**
     * 特权
     */
    public function addition($id,$type=false){
        if (empty($this->info['add'][$id])){
            $add = 0;
            $clothe_cfg = Game::getcfg('use_clothe');
            $Act6140Model = Master::getAct6140($this->uid);
            foreach ($clothe_cfg as $k=>$v){
                if ($v['pet_type'] == $id && in_array($v['id'],$Act6140Model->info['clothes'])){
                    $add = $v['pet_data'];
                }
            }
            $this->info['add'][$id] = $add;
            if ($type){
                $this->save();
            }
        }
    }
    private function reset(){
        $this->addition(1);
        $this->addition(2);
        $this->addition(3);
        $this->addition(4);

        $UserModel = Master::getUser($this->uid);

        $this->info['eday'] = ($UserModel->info['bmap']-1) * 25;

        $this->info['maxmw'] = $this->info['eday'] * 2 + $this->info['add'][1];

        //最大值不能超过上限
        $this->info['mw'] = $this->info['maxmw'];
        $this->info['time'] = Game::day_0();
        $this->_save();
    }

}






