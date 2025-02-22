<?php 
require_once "ActBaseModel.php";
/*
 * 郊游战斗内信息
 * npc随机属性 属性值
 * 玩家伤害 （NPC总的血量-玩家伤害）
 * 玩家受伤血量
 * 是否为首通
 * 当前回合数
 * 当前关卡
 */

class Act744Model extends ActBaseModel{
    
    public $atype = 744;//活动编号

	public $comment = "郊游战斗信息";
	public $b_mol = "jiaoyou";//返回信息 所在模块
    public $b_ctrl = "fightInfo";//返回信息 所在控制器
    
    public $_init = array(
        'round' => 0,               //当前回合数
        'npcEp' => array(),         //npc随机属性 key=>value
        'userEp' => array(),        //user随机两个属性1,2,3...
        'damage' => 0,              //玩家打出来的伤害
        'hurt' => 0,                //玩家受到的伤害
        'skillCollect' => array(),  //玩家选择属性的技能列表
        'isFinish' => 0,            //战斗是否结束
        'isWin' => 0,//是否胜利
        'copyId' => 0,
        'heroId' => 0,
        'cDamage' => 0,//当前打出的伤害
        'cHurt' => 0,//当前受到的伤害
        'isMe' => 0,//是否先后手
        'hp' => 0,//玩家血量
    );

    //根据关卡id获取当前关卡信息
    public function getInfoById($heroId){
        if($heroId == 0){
            Master::error(JIAOYOU_FIGHT_NO_HERO);
        }
        //每次开始重新刷数据
        $this->info = $this->_init;

        $Act764Model = Master::getAct764($this->uid);
        $Act764Model->removeData();

        $Act740Model = Master::getAct740($this->uid);
        $copyId = 0;
        if(empty($Act740Model->info['copyInfo'][$heroId])){
            $copyId = 1;
        }else{
            $copyId = $Act740Model->info['copyInfo'][$heroId] + 1;
        }

        $jiaoyouCfg = Game::getcfg_info("jiaoyou",$heroId);
        
        //判断是否超过最大关卡
        if($copyId > end($jiaoyouCfg)['stage']){
            // $copyId = end($jiaoyouCfg)['stage'];
            Master::error(JIAOYOU_MAX_COPY);
        }

        //读取郊游战斗配置 获取战斗数据
        $jyCfg = $jiaoyouCfg[$copyId];
        //每场战斗需要消耗名声
        Master::sub_item($this->uid,KIND_ITEM,4,$jyCfg['mingsheng']);
        //判断 条件是否满足
        $this->checkCondition($jyCfg);

        $Act764Model->randCards($heroId);
        $this->info['hp'] = $Act764Model->getFightHp($heroId);

        //随机npc属性 对应属性以及属性值
        $this->randEp($jyCfg);
        
        //玩家打出伤害值
        $this->info['damage'] = empty($this->info['damage'])?0:$this->info['damage'];

        //玩家受到伤害
        $this->info['hurt'] = empty($this->info['hurt'])?0:$this->info['hurt'];
        $this->info['copyId'] = $copyId;
        $this->info['heroId'] = $heroId;

        $this->save();
    }
    //每次进战斗前随机属性
    public function randEp($jyCfg){
        if(empty($this->info['npcEp'])){
            $this->info['npcEp'] = array( 'ep' => 0, 'value' => 0);
        }
        $index = array_rand($jyCfg['epnum'], 1);
        $ep = $jyCfg['epnum'][$index];
        $epValue = rand($jyCfg['ep'.$ep][0], $jyCfg['ep'.$ep][1]);
        $this->info['npcEp']['ep'] = $ep;
        $this->info['npcEp']['value'] = $epValue;
    }

    public function checkCondition($jyCfg){
        $value = 0;
        switch ($jyCfg['condition']) {
            case '0':
                return;
            case '1':
                $Act6001Model = Master::getAct6001($this->uid);
                $value = $Act6001Model->getHeroJBLv($jyCfg['heroType']);
                break;
            default:
                break;
        }
        if($value < $jyCfg['set']){
            Master::error(TANHE_CONDITION_ERROR);
        }
    }

    //开始战斗--参数：选择的属性id
    public function fight($cardId){
        if($this->info['isFinish'] == 1){
            Master::error(TANHE_FIGHT_END);
        }
        $this->info['round']++;
        $this->info['cDamage'] = 0;
        $this->info['cHurt'] = 0;

        $Act764Model = Master::getAct764($this->uid);
        //获取总血量
        $totalHp = $this->info['hp'];
        
        
        //判断卡牌羁绊关系 是否可以释放出技能
        $data = $Act764Model->releaseSkill($cardId,$this->info['round'],$this->info['npcEp']['ep'],$this->info['npcEp']['value'],$this->info['skillCollect'],$this->info['heroId']);

        $mydamage = $data['totalDamage'];
        $epId = $data['myEp'];
        $this->info['npcEp']['ep'] = $data['enemyEp'];
        $this->info['npcEp']['value'] = $data['hurt'];

        $isMe = $Act764Model->judgeIsMyFirst($epId,$mydamage,$this->info['npcEp']['ep'],$this->info['npcEp']['value']);

        $this->info['skillCollect'] = $data['skillPoint'];

        if($data['isRestrain']){
            $isMe = 1;
        }
        
        $jiaoyouCfg = Game::getcfg_info("jiaoyou",$this->info['heroId']);
        $jyCfg = $jiaoyouCfg[$this->info['copyId']];
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
            $npcHp = $jyCfg['xueliang']-$this->info['damage'];
            if($userHp <= 0 || $npcHp <= 0){
                break;
            }
        }
        $result = $Act764Model->checkIsWin($userHp,$npcHp);
        if($result['isFinish']){
            $isFinish = 1;
            $isWin = $result['isWin'];
        }
        //战斗结束之后
        if($isFinish == 1 && $this->info['isFinish'] == 0){
            if($isWin == 1){
                $Act740Model = Master::getAct740($this->uid);
                $heroId = $this->info['heroId'];
                if(empty($Act740Model->info['copyInfo'][$heroId])){
                    $Act740Model->info['copyInfo'][$heroId] = 0;
                }
                if($Act740Model->info['copyInfo'][$heroId] > $this->info['copyId']){
                    Master::error(JIAOYOU_ID_ERR);
                }
                $Act740Model->info['copyInfo'][$heroId] = $this->info['copyId'];
                $Act740Model->save();
                Master::add_item3($jyCfg['firstrwd']);
                if($jyCfg['keguaji'] == 1){
                    $Act745Model = Master::getAct745($this->uid);
                    $Act745Model->setGuardList($jyCfg['id'],$heroId,$this->info['copyId']);
                }
            }
            $this->info['isFinish'] = 1;
            $this->info['isWin'] = $isWin;
            $Act39Model = Master::getAct39($this->uid);
            $Act39Model->task_add(153,1);

            $Act764Model->removeData();
        }

        if(count($this->info['skillCollect']) >= 3){
            $this->info['skillCollect'] = array();
        }

        $this->randEp($jyCfg);
        $this->save();
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
