<?php
require_once "ActBaseModel.php";
/*
 * 成就
 */
class Act108Model extends ActBaseModel
{
	public $atype = 108;//活动编号
	
	public $comment = "视频";
	public $b_mol = "video";//返回信息 所在模块
	public $b_ctrl = "list";//返回信息 所在控制器
	
	/*
	 * 初始化结构体
	 */
	public $_init =  array(

	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){
		$this->outf = $this->info;
	}

	public function rwd($id){
		// 获得配置信息
        $videoRwdInfo = Game::getcfg_info("videorwd",$id);
        if (!isset($this->info[$id])) {
            $this->info[$id] = array(
                'id' => $id,
                'dailyNum' => 0, // 每日次数
                'roundNum' => 0, // 当前轮数的次数
                'roundVideoNum' => 0, // 当前轮数看视频的次数
                'rwd' => array(), // 当前看视频的奖励
                'status' => 0, // 是否可以观看
            );
        }

        if($this->info[$id]['dailyNum'] >= $videoRwdInfo['dayNum']) {
            Master::error(VIDEO_MAX_TIME);
        }

        if ($id == 1) {
            foreach ($videoRwdInfo['rwd'] as $val) {
                Master::add_item($this->uid, $val['kind'] , $val['id'],$val['count']);
            }
        } else if ($id == 2) {
            if (!$this->info[$id]['status']) {
                Master::error(VIDEO_NOT_WATCH);
            }

            if($this->info[$id]['roundVideoNum'] >= $videoRwdInfo['maxTime']) {
                Master::error(VIDEO_ROUND_MAX_TIME);
            }

            Master::add_item($this->uid, $this->info[$id]['rwd']['kind'] , $this->info[$id]['rwd']['itemId'], $this->info[$id]['rwd']['count']);

            $this->info[$id]['roundVideoNum']++;
            $this->info[$id]['rwd'] = array();

        } else if ($id == 3) {
            Master::add_item($this->uid, $this->info[$id]['rwd']['kind'] , $this->info[$id]['rwd']['itemId'], $this->info[$id]['rwd']['count']);
            $this->info[$id]['rwd'] = array();
        } else if ($id == 4) {
            if (!$this->info[$id]['status']) {
                Master::error(VIDEO_NOT_WATCH);
            }

            $Act40Model = Master::getAct40($this->uid);
            $cid = $Act40Model->info['cid'];
            if(empty($cid)){
                Master::error(CLUB_IS_NULL);
            }

            $ClubModel = Master::getClub($cid);
            foreach ($this->info[$id]['rwd'] as $k => $v){
                switch($k){
                    case 'exp':
                        $ClubModel->add_exp($this->uid,$v);
                        break;
                    case 'fund':
                        $ClubModel->add_fund($this->uid,$v);
                        break;
                    case 'gx':
                        $Act40Model->add_gx($v);
                        break;
                }
            }

            $this->info[$id]['rwd'] = array();

        } else if ($id == 5) {
            if (!$this->info[$id]['status']) {
                Master::error(VIDEO_NOT_WATCH);
            }

            if($this->info[$id]['roundVideoNum'] >= $videoRwdInfo['maxTime']) {
                Master::error(VIDEO_ROUND_MAX_TIME);
            }

            $Act16Model = Master::getAct16($this->uid);
            if ($Act16Model->info[$this->info['info'][$id]['rwd']['id']]['hid'] != $this->info[$id]['rwd']['hid']) {
                Master::error(VIDEO_DIFF_HERO_ID);
            }

            $over = $Act16Model->info['info'][$this->info[$id]['rwd']['id']]['over']  - 3600 * $videoRwdInfo['hour'];

            $Act16Model->info['info'][$this->info[$id]['rwd']['id']] = array(
                'hid' => $this->info[$id]['rwd']['hid'],
                'over' => $over
            );

            $Act16Model->save();
            $this->info[$id]['roundVideoNum']++;
            $this->info[$id]['rwd'] = array();

        } else if ($id == 6) {
            if (!$this->info[$id]['status']) {
                Master::error(VIDEO_NOT_WATCH);
            }

            if($this->info[$id]['roundVideoNum'] >= $videoRwdInfo['maxTime']) {
                Master::error(VIDEO_ROUND_MAX_TIME);
            }

            $Act6101Model = Master::getAct6101($this->uid);
            if ($Act6101Model->info[$this->info[$id]['rwd']['id']]['wid'] != $this->info[$id]['rwd']['wid']) {
                Master::error(VIDEO_DIFF_HERO_ID);
            }

            $Act6101Model->info[$this->info[$id]['rwd']['id']] = array(
                'wid' => $this->info[$id]['rwd']['wid'],
                'over' => Game::get_now(),
                'itemId' => $this->info[$id]['rwd']['itemId']
            );

            $Act6101Model->save();
            $this->info[$id]['roundVideoNum']++;
            $this->info[$id]['rwd'] = array();

        } else if ($id == 7){
            foreach ($this->info[$id]['rwd'] as $item) {
                $item_id = isset($item['id'])?$item['id']:$item['itemid'];
                $item['kind'] = empty($item['kind'])?1:$item['kind'];
                Master::add_item($this->uid, $item['kind'], $item_id, $item['count']);
            }
            $this->info[$id]['rwd'] = array();
        }

        $this->info[$id]['dailyNum']++;
        $this->info[$id]['status'] = 0;
        $this->save();

        Master::back_data($this->uid,$this->b_mol,'info',$this->info[$id]);
	}

	public function updateRwd($id,$rwd) {
        if (!isset($this->info[$id])) {
            $this->info[$id] = array(
                'id' => $id,
                'dailyNum' => 0, // 每日次数
                'roundNum' => 0, // 当前轮数的次数
                'roundVideoNum' => 0, // 当前轮数看视频的次数
                'rwd' => array(), // 当前看视频的奖励
                'status' => 0, // 是否可以观看
            );
        }

        $this->info[$id]['status'] = 0;

        $videoRwdInfo = Game::getcfg_info("videorwd",$id);

        if ($this->info[$id]['dailyNum'] >= $videoRwdInfo['dayNum']) {
            Master::back_data($this->uid,$this->b_mol,'info',$this->info[$id]);
            Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->info);
            return;
        }

        if ($videoRwdInfo['group'] && $this->info[$id]['roundNum'] >= $videoRwdInfo['group']) {
            $this->info[$id]['roundNum'] = 1;
            $this->info[$id]['roundVideoNum'] = 0;
            $this->info[$id]['status'] = 0;
        } elseif ($videoRwdInfo['group']) {
            if ($videoRwdInfo['roundVideoNum'] < $videoRwdInfo['maxTime']) {
                // 当前最少次数未满足
                $num = $this->info[$id]['roundVideoNum'] - $videoRwdInfo['minTime'];
                if ($num < 0) {
                    $roundNum = $videoRwdInfo['group'] - $this->info[$id]['roundNum'];
                    if ($roundNum > abs($num)) {
                        $num = mt_rand(1,100);
                        if ($num <= $videoRwdInfo['pro']) {
                            $this->info[$id]['status'] = 1;
                        }
                    } else {
                        $this->info[$id]['status'] = 1;
                    }
                }
            }
            $this->info[$id]['roundNum']++;
        } else {
            $this->info[$id]['status'] = 1;
        }

        $this->info[$id]['rwd'] = $rwd;
        $this->save();
        Master::back_data($this->uid,$this->b_mol,'info',$this->info[$id]);
    }


    public function back_data_u() {
        parent::back_data_u();
    }
}
















