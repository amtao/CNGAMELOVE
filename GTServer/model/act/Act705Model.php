<?php

require_once "ActBaseModel.php";

/*
*赴约--战斗
*/

class Act705Model extends ActBaseModel{
    
    public $atype = 705;//活动编号

    public $comment = "赴约--战斗";
    public $b_mol = "fuyue";//返回信息 所在模块
    public $b_ctrl = "fight";//返回信息 所在控制器
    
    public $_init = array(
        //对手uid
        //对手heroid
        //战斗结果
        'fightResult' => array(),
        'isPick' => 0,
    );

    //选择对手id
    public function selectEnemyId(){
        if($this->info['fUid'] != 0 || $this->info['fHeroId'] != 0){
            return;
        }
        //对手获取宫斗排行榜上的数据
        $Redis6Model = Master::getRedis6();
        $fuid = $Redis6Model->rand_f_uid($this->uid);
        if ($fuid == 0){
            //没有能打的人
            return 0;
        }
        $this->info['fUid'] = $fuid;
        //随机一个英雄
        $heroid = $this->rand_hero();
        $this->info['fHeroId'] = $heroid;
    }

    //随机hero
    public function rand_hero(){
        $fTeamModel= Master::getTeam($this->info['fUid']);

        $fHeroModel= Master::getHero($this->info['fUid']);
        $fHeroArr = array();
        foreach($fHeroModel->info as $hero){
            $fHeroArr[$hero['heroid']] = array_sum($fTeamModel->info['heros'][$hero['heroid']]['aep']);
        }
        //array根据值排序
        arsort($fHeroArr);
        //获取array第一个的key 
        return key($fHeroArr);  
    }

    //开始战斗
    public function fight(){
        $Act703Model = Master::getAct703($this->uid);
        if(count($Act703Model->info['randStoryIds'])< 5 || count($this->info['fightResult']) >= 3){
            Master::error(THE_FIGHT_IS_FINISH);
        }

        $this->selectEnemyId();

        $fTeamModel= Master::getTeam($this->info['fUid']);
        $fmember = $fTeamModel->get_pvp_buyid($this->info['fHeroId']);
 
        //我方门客信息
        $TeamModel= Master::getTeam($this->uid);
        $member = $TeamModel->get_pvp_buyid($Act703Model->info['chooseInfo']['heroId']);

        //等级加入战斗中
        $member['prop']['level'] = $member['base']['level'];
        $fmember['prop']['level'] = $fmember['base']['level'];

        //执行战斗
        $members = array(
            0 => $member['prop'],
            1 => $fmember['prop'],
        );
        
        $log = $this->do_pk($members);

        //胜负
        $is_win = 0;//胜利标签

        $zonggushiCfg = Game::getcfg_info('zong_gu_shi',$Act703Model->info['themeId']);
        $fightCount = count($this->info['fightResult']);
        
        if ($log['win'] == 0){//胜利
            $is_win = 1;

            array_push($this->info['fightResult'],$zonggushiCfg[$Act703Model->info['randSmallStoryId']]['gushisl_id'][$fightCount]);
            
            $fuyueCfg = Game::getcfg_info('fu_yue',$fightCount+2);
            Master::add_item3($fuyueCfg['reward']);
        }else{
            //失败
            $is_win = 0;
            array_push($this->info['fightResult'],$zonggushiCfg[$Act703Model->info['randSmallStoryId']]['gushisb_id'][$fightCount]);
            $fightCount = count($this->info['fightResult']);
            $Act703Model->info['randStoryIds'] = array_slice($Act703Model->info['randStoryIds'],0,$fightCount+1);
            
            //失败奖励
            $fuyueCfg = Game::getcfg_info('fu_yue',1);
            Master::add_item3($fuyueCfg['reward']);
        }

        $base_member = array(
            0 => $member['base'],
            1 => $fmember['base'],
        );
        $back_data['prop'] = $members;
        $back_data['base'] = $base_member;
        $back_data['isWin'] = $is_win;
        Master::back_data($this->uid,$this->b_mol,"fight",$back_data);
        
        //战斗结束清除数据
        $this->info['fUid'] = 0;
        $this->info['fHeroId'] = 0;
        //保存
        $this->save();
        $Act703Model->save();
    }

    private function do_pk($members){
        //$a_id;//出手者
        //$b_id;//防御者
        $rand_num = rand(1,$members[0]['level']+$members[1]['level']);
        if($rand_num <= $members[0]['level']){
            $a_id = 0; // 我先
            $b_id = 1; // 对手先
        }else{
            $a_id = 1;
            $b_id = 0;
        }

        //战斗循环
        $log = array();//战斗日志
        $win = $a_id;//胜利方
        $dead = false;
        //伤害值
        $damge = 0;
        $dtype = 0;//效果0无 1 暴击
        
        //出手方数据
        $a_mb = &$members[$a_id];
        //防守方数据
        $b_mb = &$members[$b_id];

        $damge = round($a_mb['attack'] * rand(90,110) / 100);

        //暴击概率
        if (rand(1,10000) < $a_mb['bprob']){
            //暴击伤害
            $damge = round($damge * $a_mb['bhurt'] / 100);
            $dtype = 1;//效果:暴击
        }
        //扣血
        $b_mb['hp'] -= $damge;

        $log[] = array(
            'aid' => $a_id,//出手方
            'damge' => $damge,
            'type' => $dtype,
        );
        
        //死亡判定
        if ($b_mb['hp'] <= 0){
            $win = $a_id;
            $dead = true;
        }
        
        $a_id = $b_id;
        $b_id = ($a_id+1)%2;//防御者

        if(!$dead){//回合结束没有门客死亡，判定双方血量
            $win = $members[$a_id]['hp'] > $members[$b_id]['hp']?$a_id:$b_id;
            Game::cmd_flow(99, 'fight_not_dead', $members[0]['hp'], $members[1]['hp']);
        }

        return array(
            'win' => $win,//胜利方
            'log' => $log,
        );
    }

    //领取通关奖励
    public function pickAward(){
        if($this->info['isPick'] >= 1){
            Master::error(BAOWU_HAVE_UNLOCK_CLOTHE);
        }
        //999是固定通关奖励
        $fuyueCfg = Game::getcfg_info('fu_yue',999);
        $Act703Model = Master::getAct703($this->uid);
        if((count($Act703Model->info['randStoryIds']) + count($this->info['fightResult'])) < 8){
            Master::error(STORY_NOT_CLEARANCE);
        }
        $heroId = $Act703Model->info['chooseInfo']['heroId'];
        $HeroModel = Master::getHero($this->uid);
        $HeroModel->check_info($heroId);
        $Act6001Model = Master::getAct6001($this->uid);
        $Act6001Model->addHeroJB($heroId,$fuyueCfg['love']);
        Master::add_item3($fuyueCfg['reward']);
        $this->info['isPick'] = 1;
        //修改为羁绊值
        $love = $fuyueCfg['love']>0?$fuyueCfg['love']:0;
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,array("love"=>$love));
        $this->save();
    }

    //清除数据（通关之后或者失败结束本次故事之后清除原本数据）
    public function removeData(){
        $this->info['fightResult'] = array();
        $this->info['fUid'] = 0;
        $this->info['fHeroId'] = 0;
        $this->info['isPick'] = 0;
        $this->save();
    }
    
    public function make_out(){
        // if(!empty($this->info['fUid'])){
        //     $f_user = Master::fuidInfo($this->info['fUid']);    
        // }
        // $this->outf = array(
        //     'fightResult' => $this->info['fightResult'],
        //     'fUid' => empty($this->info['fUid'])?0:$this->info['fUid'],
        //     'fHeroId' => empty($this->info['fHeroId'])?0:$this->info['fHeroId'],
        //     'fUser' => empty($f_user)?array():$f_user,
        // );
        $this->outf = $this->info;
    }
}