<?php
require_once "ActBaseModel.php";
/*
 * 称号
 */
class Act25Model extends ActBaseModel
{
	public $atype = 25;//活动编号
	
	public $comment = "称号";
	public $b_mol = "chenghao";//返回信息 所在模块
	public $b_ctrl = "chInfo";//返回信息 所在控制器
	

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
        'setid' => 0,  //设置称号
        'list'	=> array(), //称号列表
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		//过滤过期
		$outf = array();
		foreach($this->info['list'] as $chid => $chInfo){
			if( !empty($chInfo['endT']) && Game::is_over($chInfo['endT'])){
				//清掉 设置称号
				if($this->info['setid'] == $chid){
					$this->info['setid'] = 0;
				}
				continue;
			}
			$check = $chInfo['checked'] === NULL?1:$chInfo['checked'];//兼容 是否有红点
			$outf['list'][] = array(
				'chid' => $chid,
				'getT' => $chInfo['getT'],
				'endT' => $chInfo['endT'],
                'checked'=>$check,
			);
		}
		$outf['setid'] = $this->info['setid'];
		//构造输出
		$this->outf = $outf;

	}
	
	/**
	 * 添加称号
	 * @param int $chid  称号id
	 * @param array $section 区间
	 */
	public function add_chenghao($chid,$section){

		//获取称号id
		$item_info = Game::getcfg_info('item',$chid,TITLE_IS_ERROR);
		$chenghao_cfg = Game::getcfg_info('chenghao',$chid);
		$endD = $chenghao_cfg['limtime']*3600;
		//到期时间
		if($this->info['list'][$chid]['endT'] > $_SERVER['REQUEST_TIME']){
			$endT = $this->info['list'][$chid]['endT'] + $endD;
		}else{
			$endT = $_SERVER['REQUEST_TIME'] + $endD;
		}
		$this->info['list'][$chid] = array(
			'getT' => $_SERVER['REQUEST_TIME'],   //获得时间
			'endT' => $endT,  //过期时间
            'checked'=> 0, //是否点击 0否 1是
		);
		$this->save();
	}
	
	/**
	 * 设置称号
	 * @param int $chid   称号id
	 */
	public function set_chenghao($chid){
		
		if(empty($this->info['list'][$chid])){
			Master::error(TITLE_NO_GET);
		}
		if(Game::is_over($this->info['list'][$chid]['endT'])){
			Master::error(TITLE_IS_OVERDUE);
		}
		$this->info['setid'] = $chid;
		$this ->save();
	}
	
	/**
	 * 取消称号
	 */
	public function off_chenghao(){
		$this->info['setid'] = 0;
		$this ->save();
	}
	
	/**
	 * 判断是否存在有效期的王爷称号
	 */
	public function has_wangye(){
		//没有称号
		if(empty($this->info['list'])){
			return false;
		}
		
		foreach($this->info['list'] as $k => $v){
			//过滤不是王爷
			if( !Game::is_ye($k)){
				continue;
			}
			//过滤过期王爷
			if( Game::is_over($v['endT']) ){
				continue;
			}
			//生效期王爷
			return true;
		}
		//没有生效期的王爷
		return false;
	}
	/*
	 * 判断当前王爷是否在有效期内
	 * */
	public function is_effect($cid) {
	    if(empty($this->info['list'][$cid]) || !Game::is_ye($cid) ){
	        Master::error(DESIGN_MISS);
	    }
	    if(Game::is_over($this->info['list'][$cid]['endT'])){
	        Master::error(DESIGN_EXPIRE);
	    }
	}
	
}




