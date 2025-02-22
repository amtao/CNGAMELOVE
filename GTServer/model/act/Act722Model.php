<?php 
require_once "ActBaseModel.php";
/*
 * 弹劾内信息
 * npc随机属性 属性值
 * 玩家伤害 （NPC总的血量-玩家伤害）
 * 玩家受伤血量
 * 是否为首通
 * 当前回合数
 * 当前关卡
 */

class Act722Model extends ActBaseModel{
    
    public $atype = 722;//活动编号

	public $comment = "弹劾信息";
	public $b_mol = "tanhe";//返回信息 所在模块
    public $b_ctrl = "info";//返回信息 所在控制器
    
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

    //根据关卡id获取当前关卡信息
    public function getInfoById($copyId){
        $Act720Model = Master::getAct720($this->uid);
        $Act721Model = Master::getAct721($this->uid);
        //每次开始重新刷数据
        $this->info = $this->_init;

        $Act764Model = Master::getAct764($this->uid);
        $Act764Model->removeData();

        /**
         * $copyId = 0
         * 默认最大关卡的下一关
         * */
        if($copyId == 0){
            $copyId = $Act721Model->info['maxCopy'] + 1;
        }else{
            if($copyId > $Act721Model->info['maxCopy']+1){
                Master::error(TANHE_COPYID_ERROR);
            }
        }
        if(in_array($copyId,$Act720Model->info['pickCopy'])){
            Master::error(TANHE_NO_WIPE_COUNT);
        }

        $tanheTotalCfg = Game::getCfg("tanhe");
        
        //判断是否超过最大关卡
        if($copyId > end($tanheTotalCfg)['id']){
            $copyId = end($tanheTotalCfg)['id'];
        }

        $Act721Model->info['currentCopy'] = $copyId;
        //读取弹劾配置 获取战斗数据
        $tanheCfg = $tanheTotalCfg[$Act721Model->info['currentCopy']];
        //判断 条件是否满足
        $this->checkCondition($tanheCfg);
        
        $Act721Model->save();

        
        $Act764Model->randCards();
        $this->info['hp'] = $Act764Model->getFightHp();

        //随机npc属性 对应属性以及属性值
        $this->randEp($tanheCfg);
        
        //玩家打出伤害值
        $this->info['damage'] = empty($this->info['damage'])?0:$this->info['damage'];

        //玩家受到伤害
        $this->info['hurt'] = empty($this->info['hurt'])?0:$this->info['hurt'];

        $this->save();
    }
    //每次进战斗前随机属性
    public function randEp($tanheCfg){
        if(empty($this->info['npcEp'])){
            $this->info['npcEp'] = array( 'ep' => 0, 'value' => 0);
        }
        $index = array_rand($tanheCfg['epnum'], 1);
        $ep = $tanheCfg['epnum'][$index];
        $epValue = rand($tanheCfg['ep'.$ep][0], $tanheCfg['ep'.$ep][1]);
        $this->info['npcEp']['ep'] = $ep;
        $this->info['npcEp']['value'] = $epValue;
    }

    public function checkCondition($tanheCfg){
        $UserModel = Master::getUser($this->uid);
        $TeamModel = Master::getTeam($this->uid);
        $value = 0;
        switch ($tanheCfg['condition']) {
            case '1':
                $value = $UserModel->info['level'];
                break;
            case '2':
                $value = array_sum($TeamModel->info['cardep']);
                break;
            case '3':
                $value = $UserModel->info['bmap'] + $UserModel->info['smap'] -1;
                break;
            case '4':
                $value = $TeamModel->info['cardep'][1];
                break;
            case '5':
                $value = $TeamModel->info['cardep'][4];
                break;
            case '6':
                $value = $TeamModel->info['cardep'][3];
                break;
            case '7':
                $value = $TeamModel->info['cardep'][2];
            default:
                break;
        }
        if($value < $tanheCfg['set']){
            Master::error(TANHE_CONDITION_ERROR);
        }
    }

    /**
     * 开始战斗
     * cardIds 选中打出的卡牌
     * id 凑成羁绊的id
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
        
        $Act721Model = Master::getAct721($this->uid);
        $tanheCfg = Game::getcfg_info("tanhe",$Act721Model->info['currentCopy']);
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
            $npcHp = $tanheCfg['xueliang']-$this->info['damage'];
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
                $Act757Model = Master::getAct757($this->uid);
                $skillRate = $Act757Model->getSkillProp(10);
                $Act720Model = Master::getAct720($this->uid);
                $maxCount = Game::getcfg_param("tanhe_max");
                array_push($Act720Model->info['pickCopy'],$Act721Model->info['currentCopy']);
                if(end($Act720Model->info['pickCopy']) == $Act721Model->info['currentCopy'] && $Act721Model->info['currentCopy'] <= $Act721Model->info['maxCopy']){
                    $Act721Model->info['currentCopy']++;
                    $Act721Model->save();
                }
                if($Act721Model->info['currentCopy'] > $Act721Model->info['maxCopy']){
                    $Act721Model->info['maxCopy'] = $Act721Model->info['currentCopy'];
                    if($Act721Model->info['currentCopy'] >= $maxCount){
                        $Act721Model->info['currentCopy'] = $maxCount;
                    }else{
                        $Act721Model->info['currentCopy']++;
                    }
                    $Act721Model->save();
                    $this->info['isFirst'] = 1;
                    foreach($tanheCfg['firstrwd'] as $item){
                        $addCount = ceil($item['count'] *(1+$skillRate[$item['id']]/100));
                        Master::add_item($this->uid,$item['kind'],$item['id'],$addCount);
                    }
                }
                foreach($tanheCfg['rwd'] as $item){
                    $addCount = floor($item['count'] *(1+$skillRate[$item['id']]/100));
                    Master::add_item($this->uid,$item['kind'],$item['id'],$addCount);
                }
                
                if($Act720Model->info['count'] == 0){
                    $Act39Model = Master::getAct39($this->uid);
                    $Act39Model->task_add(150,$Act721Model->info['maxCopy']);
                    $Act39Model->task_add(151,1);

                    $Act720Model->setTanheCount();
                }
                $Act720Model->save();
            } 
            $this->info['isFinish'] = 1;
            $this->info['isWin'] = $isWin;
            $Act764Model->removeData();
        }
        if(count($this->info['skillCollect']) >= 3){
            $this->info['skillCollect'] = array();
        }
        $this->randEp($tanheCfg);
        $this->save();
        
    }

    public function make_out(){
        $this->outf = $this->info;
    }

}
