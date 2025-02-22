<?php
require_once "ActBaseModel.php";
/*
 * 日常任务
 */
class Act35Model extends ActBaseModel
{
	public $atype = 35;//活动编号
	
	public $comment = "日常任务";
	public $b_mol = "daily";//返回信息 所在模块
	public $b_ctrl = "";//返回信息 所在控制器
	
	
	public $outf_u;//活动更新输出数据
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(
		//奖励领取信息
		//每个任务的完成信息
	);

    public function __construct($uid,$hid = 1)
    {
        parent::__construct($uid,$hid);

        //这段代码需要优化
        $hasUpdate = false;
        if (!isset($this->info['act'][21])) {
            $dailyrwd_cfg = Game::getcfg("dailyrwd");
            foreach ($dailyrwd_cfg as $v){
                if ($v['id'] == 21) {
                    $hasUpdate = true;
                    $this->info['act'][21] = array(0,0);
                    break;
                }
            }
        }
        if ($hasUpdate) {
            $this->save();
        }
        //这段代码需要优化 end
    }

	/*
	 * 初始化函数
	 */
	public function do_init()
	{
		//读取日常任务配置
		$dailyrwd_cfg = Game::getcfg("dailyrwd");
		$dailyrwd_rwd_cfg = Game::getcfg("dailyrwd_rwd");
		
		$init = array(
			'score' => 0,	//活跃值
			'act' => array(),	//任务档次
			'rwd' => array(),	//活跃度奖励领取情况
		);
		//任务档次 完成次数/领奖情况
		foreach ($dailyrwd_cfg as $v){
			$init['act'][$v['id']] = array(0,0);
		}
		
		//活跃值领奖档次
		foreach ($dailyrwd_rwd_cfg as $v){
			$init['rwd'][$v['id']] = 0;
		}
		return $init;
	}
	
	
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$this->outf = array();
		
		//当前活力值
		$this->outf['score'] = $this->info['score'];
		
		//活力值奖励列表
		foreach ($this->info['rwd'] as $k => $v){
			$this->outf['rwds'][] = array(
				'id' => $k,
				'rwd' => $v,
			);
		}
		
		//任务完成情况列表
        $maps[$k] = 1;
		foreach ($this->info['act'] as $k => $v){
			$this->outf['tasks'][] = array(
				'id' => $k,
				'num' => $v[0],
				'rwd' => $v[1],
			);
            $maps[$k] = 1;
		}

        $this->checkNewCJ($maps);
	}

	private function checkNewCJ($maps){
        $dailyrwd_cfg = Game::getcfg("dailyrwd");
        if (count($this->info['act']) != count($dailyrwd_cfg)){
            foreach ($dailyrwd_cfg as $v){
                $k = $v['id'];
                if ($maps[$k] == 1)continue;
                $this->info['act'][$k] = array(0,0);
                $this->outf['tasks'][] = array(
                    'id' => $v['id'],
                    'num' => 0,
                    'rwd' => 0,
                );
            }

            $ActModel = Master::getAct($this->uid,$this->atype);
            $ActModel->setAct($this->atype,array(
                'id'=>$this->hid,
                'data' => $this->info,
            ));
            $this->_update = true;
        }
	}
	
	//通用接口，所以任务任务类型统一
	public function setTask($type,$num){
		$dailyRwdCfg = Game::getcfg("dailyrwd");
		foreach($dailyRwdCfg as $id => $task){
			if($type == $task['task_type']){
				$this->do_act($id,$num);
			}
		}
	}
	
	/*
	 * 更新任务数量
	 */
	public function do_act($id,$num){
		$this->info['act'][$id][0] += $num;
		$this->save();
		
		//更新信息
		$this->outf_u['tasks'][] = array(
			'id' => $id,
			'num' => $this->info['act'][$id][0],
		);
	}
	
	/*
	 * 领取任务奖励
	 */
	public function task_rwd($id){
		//读取日常任务配置
		$dailyrwd_cfg_info = Game::getcfg_info("dailyrwd",$id);
		
		//任务数量是否已达成
		if ($dailyrwd_cfg_info['num'] > $this->info['act'][$id][0]){
			Master::error(DAILY_UN_COMPLETE);
		}
		//奖励是否已领过
		if ($this->info['act'][$id][1] > 0){
			Master::error(DAILY_IS_RECEIVE);
		}
		
		if($dailyrwd_cfg_info['type'] == 'mooncard'){
			$Act68Model = Master::getAct68($this->uid);
			$shengxiao = $Act68Model->find_ka(1);
			if(!$shengxiao){
				Master::error(MONTH_UNBUY);
			}
		}
		
		if($dailyrwd_cfg_info['type'] == 'yearcard'){
			$Act68Model = Master::getAct68($this->uid);
			$shengxiao = $Act68Model->find_ka(2);
			if(!$shengxiao){
				Master::error(YEAR_UNBUY);
			}
		}
		
		//领取奖励
		$team = Master::get_team($this->uid);
		foreach ($dailyrwd_cfg_info['rwd'] as $v){
			//构造数量
			$item = Game::auto_count($v,$team['allep']);
			Master::add_item2($item);
		}
		
		$this->info['act'][$id][1] = 1;
		$this->save();
		
		//更新信息
		$this->outf_u['tasks'][] = array(
			'id' => $id,
			'rwd' => $this->info['act'][$id][1],
		);
	}
	
	/*
	 * 加上活跃值
	 */
	public function add_score($num){
		$this->info['score'] += $num;
		$this->save();

		$Act6106Model = Master::getAct6106($this->uid);
		$Act6106Model->add_type(1, $this->info['score']);
		
		//更新信息
		$this->outf_u['score'] = $this->info['score'];
	}
	
	/*
	 * 领取档次奖励
	 */
	public function act_rwd($id){
		//奖励配置
		$dailyrwd_rwd_cfg_info = Game::getcfg_info("dailyrwd_rwd",$id);
		
		//分值是否已达到
		if ($dailyrwd_rwd_cfg_info['need'] > $this->info['score']){
			Master::error(DAILY_NO_RECEIVE);
		}
		//是否还没领过
		if ($this->info['rwd'][$id] > 0){
			Master::error(DAILY_IS_RECEIVE);
		}
		
		//领取奖励
		$team = Master::get_team($this->uid);
		
		//下发固定奖励
		foreach($dailyrwd_rwd_cfg_info['rwd'] as $rwd){
			$item = Game::auto_count($rwd,$team['allep']);
			Master::add_item2($item);
		}
		
		//下发概率奖励
		$rwd_prob = $dailyrwd_rwd_cfg_info['rwd_prob'];
		if(!empty($rwd_prob)){
			$rk = Game::get_rand_key(100,$rwd_prob,'prob_100');
			if(!empty($rwd_prob[$rk])){
				Master::add_item2($rwd_prob[$rk]);
			}

            //双旦活动道具产出
            $Act292Model = Master::getAct292($this->uid);
            $Act292Model->chanChu(1);
		}
		
		//记录领取信息
		$this->info['rwd'][$id] = 1;
		$this->save();
		
		//更新信息
		$this->outf_u['rwds'][] = array(
			'id' => $id,
			'rwd' => $this->info['rwd'][$id],
		);
	}
	
	/*
	 * 返回活动数据
	 * 初始化返回
	 * 更新返回?
	 */
	public function back_data_u(){
		Master::$bak_data['u'][$this->b_mol] = $this->outf_u;
		
		//判断改变的字段
		//按改变的返回
	}
	
	/*
	 * 返回活动信息
	 */
	public function back_data(){
		Master::$bak_data['a'][$this->b_mol] = $this->outf;
	}
	
	/*
	 * 经营分类
	 * 
	 * */
	public function jytype($id,$num=1) {
	    switch ($id){
	        case 2:
	            self::do_act(6, $num);
	            break;
	        case 3:
	            self::do_act(7, $num);
	            break;
	        case 4:
	            self::do_act(8, $num);
	            break;
	    }
	}
	
}
















