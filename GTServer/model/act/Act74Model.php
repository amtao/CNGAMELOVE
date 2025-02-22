<?php
require_once "ActBaseModel.php";
/*
 * 用户场景
 */
class Act74Model extends ActBaseModel
{
	public $atype = 74;//活动编号
	
	public $comment = "用户场景";
	public $b_mol = "user";//返回信息 所在模块
	public $b_ctrl = "changjing";//返回信息 所在控制器
	
	public $ids = array();  //获取解锁场景的所有id
	
	/*
	 * 初始化结构体
	 * 存储非系统触发(官品,vip等)获得的场景id
	 */
	public $_init =  array(
	    'ver' => 1,
		'set' => 0, //设置场景id  默认未0
		'list' => array(),  //存储非系统触发(官品,vip等)获得的场景id
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//默认输出直接等于内部存储数据
		$this->get_allid();
		
		//获得的场景id
		$outf = array();
		foreach($this->ids as $k => $v){
		    $num = $v > 0 && Game::is_over($v)?1:0; //1 :过期  0:不过期
		    if($num && $this->info['set'] == $k ){
                $this->info['set'] = 0;
            }
			$outf[] = array(
				'id' => $k,
                'cd' => array(
                    'num' => $num,  //1 :过期  0:不过期
                    'next' => Game::is_over($v)?0:$v,
                    'label' => 'userChangJingTime',
                ),
			);
		}
		
		$this->outf = array(
			'ver' => empty($this->info['ver'])?1:$this->info['ver'],
			'set' => $this->info['set'],
			'list' => $outf,
		);
	}
	
	
	/**
	 * 添加场景id
     * $id :场景id
     * $day : 天
	 */
	public function add($id,$day){
		
		//判断是否已经拥有
        $cfg_user_back = self::get_user_back();
        if(empty($cfg_user_back)) {
            Master::error(PARAMS_ERROR . $id);  //参数错误
        }

        $enT = $day * 60 * 60 *24 ; //结束新增时间

        //新增场景
        if(isset($this->info['list'][$id]) && $this->info['list'][$id] == 0){  //本来永久
            $this->info['list'][$id] = 0;
        }elseif($enT == 0){  //新增永久
            $this->info['list'][$id] = 0;
        }else{  //不永久
            //初始化
            if(empty($this->info['list'][$id])){
                $this->info['list'][$id] = 0;
            }
            if(!Game::is_over($this->ids[$id])){
                $this->info['list'][$id] += $enT;//未过期
            }else{
                $this->info['list'][$id] = $enT + $_SERVER['REQUEST_TIME'];
            }
        }
		$this->save();
	}
	
	
	/**
	 * 设置场景id
	 */
	public function set($id){
		
		//判断是否已经拥有
        if( !isset($this->ids[$id]) ){
            Master::error(USER_CJ_HAS);
        }
		if( $this->ids[$id] > 0 && Game::is_over($this->ids[$id]) ){
            Master::error(ACT68_OVERDUE);
		}
		
		$this->info['set'] = $id;
		$this->save();
	}

    /**
     * 获得技能加成
     * 基础值10000
     * $id 场景id
     */
    public function get_sAdd($id){
        $add = 0;

        //判断是否已经拥有
        if( !isset($this->ids[$id]) ){
            return $add;
        }
        if( $this->ids[$id] > 0 && Game::is_over($this->ids[$id]) ){
            return $add;
        }

        switch ($id){
            case 3:
                $cfg_user_back = self::get_user_back();
                $add = $cfg_user_back[3]['add'];
                break;
        }
        return $add;
    }

    /*
     *
     */
    public function get_user_back(){

        $SevidCfg = Common::getSevidCfg();
        //开服绝对时间
        Common::loadModel('ServerModel');
        $show_time = ServerModel::getShowTime($SevidCfg['sevid']);
        //新版本
        $openHome2 = Game::get_peizhi('openHome2');
        if(!empty($openHome2['ver'])){
            $this->info['ver'] = $openHome2['ver'];
        }
        if( isset($openHome2['time']) && $show_time >  strtotime($openHome2['time'])  ){
            $this->info['ver'] = 2;
            $cfg_user_back = Game::getcfg('user_back_2');
        }else{
            $cfg_user_back = Game::getcfg('user_back');
        }
        return empty($cfg_user_back)?array():$cfg_user_back;
    }



	/**
	 * 获取解锁场景的所有id
	 */
	public function get_allid()
	{
        $cfg_user_back = self::get_user_back();
		$this->ids = array();
		
		//场景配置
	    $UserModel = Master::getUser($this->uid);
	    
	    foreach($cfg_user_back as $k => $v){
	    	switch($v['open']){
	    		case 0 :   //无条件解锁
	    			$this->ids[$v['id']] = $v['days'];
	    			break;
	    		case 1 :   //官品解锁
	    			//官品不足
	    			if($UserModel->info['level'] < $v['level'] ){
	    				continue;
	    			}
                    $this->ids[$v['id']] = $v['days'];
	    			break;
	    		case 2:   //vip解锁
	    			//vip不足
	    			if($UserModel->info['vip'] < $v['level'] ){
	    				continue;
	    			}
                    $this->ids[$v['id']] = $v['days'];
	    			break;
	    			
	    		default :
	    			continue;
	    			break;
	    	}
	    }

	    //添加 非系统触发(官品,vip等)获得的场景id
        if(!empty($this->info['list'])){
	        foreach($this->info['list'] as $k1 => $v1){
                $this->ids[$k1] = $v1;
            }
        }
	}
	
	
	/*
	 * 返回活动信息
	 */
	public function back_data(){
		
	}
	
	/*
	 * 返回活动信息
	 */
	public function back_data_a(){
		Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
	}
	
}


















