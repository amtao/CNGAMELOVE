<?php 
require_once "ActBaseModel.php";
/*
 * 小战斗
 */

class Act765Model extends ActBaseModel{
    
    public $atype = 765;//活动编号

	public $comment = "小战斗数据";
	public $b_mol = "user";//返回信息 所在模块
    public $b_ctrl = "pvewin";//返回信息 所在控制器
    
    public $_init = array(
        'round' => 0,               //当前打了多少回合
        'npcEp' => array(),         //npc随机属性 key=>value
        'damage' => 0,              //玩家打出来的伤害
        'hurt' => 0,                //玩家受到的伤害
        'skillCollect' => array(),  //玩家选择属性的技能列表
        'isFirst' => 0,             //是否首通
        'isFinish' => 0,            //战斗是否结束
        'isWin' => 0,//是否胜利
        'cDamage' => 0,//当前打出的伤害
        'cHurt' => 0,//当前受到的伤害
        'isMe' => 0,//是否先后手
        'hp' => 0,
    );

    //pve前置 扣除名声
	public function pve(){

        $this->info = $this->_init;

        $Act764Model = Master::getAct764($this->uid);
        $Act764Model->removeData();

		$UserModel = Master::getUser($this->uid);
		$hit_smap = $UserModel->info['smap']+1;
		$smap_cfg = Game::getcfg_info('pve_smap',$hit_smap,"已经通关");
		if (Game::ispvb($UserModel->info['smap'],$UserModel->info['bmap'])){
			Master::error(GAME_LEVER_GT_BMAP);
		}
		//还有没有小兵
		if ($UserModel->info['army'] <= 0){
			Master::error(GAME_LEVER_NO_SOLDIER,4);
		}
		$map_army = $smap_cfg['army'];

		//获取我的阵法属性
		$team = Master::get_team($this->uid);
		//我的武力值
		$wuli = $team['allep'][1];
		$wuli = $wuli == 0?1:$wuli;
		$need_army = round($map_army * $smap_cfg['ep1'] / $wuli);
		if($smap_cfg['bmap'] <= 10){
			$need_army = round($map_army/2 +  $map_army/2* $smap_cfg['ep1'] / $wuli);
		}
		$need_army = $need_army > 0 ? $need_army : 1;
		if($UserModel->info['army'] < $need_army){
			Master::error(GAME_LEVER_NO_SOLDIER);
		}
		//扣除兵力
		Master::sub_item($this->uid,KIND_ITEM,4,$need_army);

		Master::back_win('user','pvewin','deil',$need_army);
        Master::back_win('user','pvewin','pvewin',$need_army);
        
        $Act764Model->randCards();
        $this->info['hp'] = $Act764Model->getFightHp();
        
        //玩家打出伤害值
        $this->info['damage'] = empty($this->info['damage'])?0:$this->info['damage'];

        //玩家受到伤害
        $this->info['hurt'] = empty($this->info['hurt'])?0:$this->info['hurt'];

        $this->randEp($smap_cfg);
        $this->save();

    }

    //每次进战斗前随机属性
    public function randEp($pveCfg){
        if(empty($this->info['npcEp'])){
            $this->info['npcEp'] = array( 'ep' => 0, 'value' => 0);
        }
        $index = array_rand($pveCfg['epnum'], 1);
        $ep = $pveCfg['epnum'][$index];
        $this->info['npcEp']['ep'] = $ep;
        $this->info['npcEp']['value'] = $pveCfg['jisuan_number'];
    }
	/*
	 * 打地图
	 */
	public function fight($cardId){
        
        if($this->info['isFinish']== 1){
            Master::error(TANHE_FIGHT_END);
        }
        $this->info['round']++;
        $this->info['cDamage'] = 0;
        $this->info['cHurt'] = 0;
        $Act764Model = Master::getAct764($this->uid);
        //获取总血量
        $totalHp = $this->info['hp'];

        //判断卡牌羁绊关系 是否可以释放出技能
        $data = $Act764Model->releaseSkill($cardId,$this->info['round'],$this->info['npcEp']['ep'],$this->info['npcEp']['value'],$this->info['skillCollect']);
        

        $mydamage = $data['totalDamage'];
        $epId = $data['myEp'];
        $this->info['npcEp']['ep'] = $data['enemyEp'];
        $this->info['npcEp']['value'] = $data['hurt'];

        $isMe = $Act764Model->judgeIsMyFirst($epId,$mydamage,$this->info['npcEp']['ep'],$this->info['npcEp']['value']);

        $this->info['skillCollect'] = $data['skillPoint'];

        if($data['isRestrain']){
            $isMe = 1;
        }
        
        $UserModel = Master::getUser($this->uid);

        $hit_smap = $UserModel->info['smap']+1;

        $smap_cfg = Game::getcfg_info('pve_smap',$hit_smap,"已经通关");

        //判定大小关逻辑修改
		if (Game::ispvb($UserModel->info['smap'],$UserModel->info['bmap'])){
			Master::error(GAME_LEVER_GT_BMAP);
        }
        
        $cfg_rwd = array ( //过关奖励  配置数据太大,这边写死
            array ( 'itemid' => 2,  'type' => '$e2*0.12497+1000', ),
            array ( 'itemid' => 5, 'count' => 5, ),
        );

        $isWin = 0;
        $isFinish = 0;
        $userHp = 0;
        $npcHp = 0;
        $this->info['isMe'] = $isMe;
        $this->info['cDamage'] = $mydamage;
        $this->info['cHurt'] = $data['hurt'];
        for($i = 0; $i < 2; $i++){
            //我方先出手
            if($isMe == 1){
                $this->info['damage'] += $mydamage;
                $isMe = 0;
            }else{
                $this->info['hurt'] += $data['hurt'];
                $isMe = 1;
            }
            $userHp = $totalHp - $this->info['hurt'];
            $npcHp = $smap_cfg['xueliang']-$this->info['damage'];
            if($userHp <= 0 || $npcHp <= 0){
                break;
            }
        }
        $team = Master::get_team($this->uid);
        $result = $Act764Model->checkIsWin($userHp,$npcHp);
        if($result['isFinish']){
            $isFinish = 1;
            $isWin = $result['isWin'];
        }
        //战斗结束之后
        if($isFinish == 1 && $this->info['isFinish'] == 0){
            if($isWin == 1){
                //足够 胜利 标示这一关已经打过
                $u_update['smap'] = $hit_smap;//关卡ID更新
                $u_update['mkill'] = 0;//已击溃清0

                //奖励倍数
                $beishu = Game::pv_beishu('pve');

                //加上过关奖励
                foreach ($cfg_rwd as $rv){
                    //构造数量
                    $item = Game::auto_count($rv,$team['allep']);
                    $item['count'] = $item['count'] == 5?$item['count']:ceil($item['count'] * $beishu);
                    Master::add_item2($item,'user','pvewin');
                }
                //通过一个中关卡获得的额外奖励
                if( !empty($smap_cfg['rwd_prob_100']) ){
                    $extra = $smap_cfg['rwd_prob_100'];
                    $rk = Game::get_rand_key(10000,$extra,'prob_10000');
                    if(!empty($extra[$rk])){
                        $count = $extra[$rk]['itemid'] == 5?$extra[$rk]['count']:ceil($extra[$rk]['count']* $beishu);
                        $item = array(
                            'kind' => $extra[$rk]['kind']?$extra[$rk]['kind']:1,
                            'itemid' => $extra[$rk]['itemid'],
                            'count' => $count,
                        );
                        Master::add_item2($item,'user','pvewin');
                    }
                }
                
                //日常任务
                $Act39Model = Master::getAct39($this->uid);
                $Act39Model->task_add(154,1);
                
                //更新关卡排行
                $Redis2Model = Master::getRedis2();
                $Redis2Model->zAdd($this->uid,$UserModel->info['bmap'] + $u_update['smap'] - 1);

                //关卡冲榜
                $HuodongModel = Master::getHuodong($this->uid);
                $HuodongModel->chongbang_huodong('huodong251',$this->uid,1);

                //小关卡流水
                Game::cmd_flow(17, 1, 1, $hit_smap);

                //咸鱼日志
                Common::loadModel('XianYuLogModel');
                XianYuLogModel::copy($UserModel->info['platform'], $this->uid, $hit_smap, '地图小关');
            }else{
                $Act750Mdoel = Master::getAct750($this->uid);
                $Act750Mdoel->setIsPop(1,1);
                $Act750Mdoel->setIsPop(5,1);
            }
            $this->info['isFinish'] = 1;
            $this->info['isWin'] = $isWin;
            $UserModel->update($u_update);
            //主线任务 - 刷新
            $Act39Model = Master::getAct39($this->uid);
            $Act39Model->task_refresh(7);

            $Act764Model->removeData();
        }
    
        if(count($this->info['skillCollect']) >= 3){
            $this->info['skillCollect'] = array();
        }
        $this->randEp($smap_cfg);
        $this->save();
	}

    public function make_out(){
        $this->outf = $this->info;
    }

}
