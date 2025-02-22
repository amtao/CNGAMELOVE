<?php
require_once "ActBaseModel.php";
/*
 * 亲家列表
 */
class Act133Model extends ActBaseModel
{
	public $atype = 133;//活动编号
	
	public $comment = "亲家列表";
	public $b_mol = "friends";//返回信息 所在模块
	public $b_ctrl = "qjlist";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		
		
	);
	

	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		
		$outof = self::qj_out();
    	//好感度
    	if(!empty($outof)){
    		 //好感度
	    	$Act134Model = Master::getAct134($this->uid);
	    	$myqj = $Act134Model->info['my'];
            $Act90Model = Master::getAct90($this->uid); //新加亲家闪光
	    	foreach($outof as $fk => $fv){
	    		 if(empty($fv['job'])){
	    		 	$outof[$fk]['job'] = 1;
	    		 	$outof[$fk]['level'] = 2;
	    		 }
	    		 $outof[$fk]['num'] = empty($myqj[$fk]['num'])?0:$myqj[$fk]['num']; //亲家亲密度
	    		 $outof[$fk]['num3'] = Game::is_today($myqj[$fk]['time'])?1:0; //1:已拜访 0:未拜访

                $outof[$fk]['tip'] = 0;
                //闪光特效
                if( $fv['num2'] <= 1 && !empty($Act90Model->info['qjlist'][$fk]) ){
                    $outof[$fk]['tip'] = 1;			//亲家闪光  0:不闪 1:闪光
                }

	    	}
	    	$outof = array_values($outof);
    	}
		//默认输出直接等于内部存储数据
		$this->outf = $outof;
	}
	
	/*
	 * 构造输出结构体
	 */
	public function qj_out(){
		$outof = array();
		//这部分不常更新,用缓存存放
		$key = $this->uid.'_qingjia_msg';
		$cache = Common::getDftMem();
	    $outof = $cache->get($key);
	    if( empty($outof) ){
	    	//亲家列表
	    	$team = Master::get_team($this->uid);
	    	if(!empty($team['qingjia'])){
	    		arsort($team['qingjia']);

                //$Act97Model = Master::getAct97($this->uid);

	    		$data = array();
	    		foreach( $team['qingjia'] as $k => $v ){

                    $user_info =  Master::fuidInfo($k);  //亲家信息

	    			$data[$k] = $user_info;  //亲家信息
	    			$data[$k]['num2'] = $v;			//亲家子嗣人数
	    		}
	    		$cache->set($key,$data,600);
	    		$outof = $data;
	    	}
	    }
	    return empty($outof)?array():$outof;
	}
	
	/*
	 * 返回活动信息
	 */
	public function back_data(){
		$this->make_out();
		Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
	}
	
	/*
	 * 构造输出结构体
	 */
	public function del_key(){
		$key = $this->uid.'_qingjia_msg';
		$cache = Common::getDftMem();
	    $cache->delete($key);
	}
	
}

