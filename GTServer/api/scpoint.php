<?php
//讨伐
class scpointMod extends Base{

    /*
     * 存储门客的特殊关卡
     * params 关卡id
     * */
    public function recored($params) {
        $id = Game::intval($params, 'id');
        $heropvecfg = Game::getcfg_info('hero_pve', $id);
        if(empty($heropvecfg)){
            Master::error(STORY_SP_COPY_LIMIT);
        }
        $scpoint_type = $heropvecfg['type'];
        $scpoint_roleid = $heropvecfg['roleid'];

        if (!$this->isCanEnterHeroPve($heropvecfg)){
            Master::error(STORY_SELECT_CHOSE_LIMIT);
        }

        if ($heropvecfg['isfight'] == 1){

        }


        $Act6000Model = Master::getAct6000($this->uid);
        $Act6000Model->do_save($id, $scpoint_type, $scpoint_roleid);

        // 刷新任务
        $guanq = Game::get_peizhi('gq_status');
        if( !empty($guanq['mainTask']) && $id > $guanq['mainTask']){
            Master::error(PARAMS_ERROR.$id);
        }
        $Act39Model = Master::getAct39($this->uid);
        $Act39Model->task_refresh(48);
        $taskType = 126 + $heropvecfg['roleid'];
        $Act39Model->task_add($taskType,1);

        $Act6000Model->back_data();
    }

    private function isCanEnterHeroPve($heropvecfg){
        $type = $heropvecfg['unlocktype'];
        $param = $heropvecfg['unlock'];
        switch ($type){
            case 1: // 加羁绊值
                $Act6001Model = Master::getAct6001($this->uid);
                if ($heropvecfg['type']== 1){
                    return $Act6001Model->getHeroJB($heropvecfg['roleid']) >= $param;
                }
                else if ($heropvecfg['type']== 2){
                    return $Act6001Model ->getWifeJB($heropvecfg['roleid']) >= $param;
                }
                return true;
            case 2: // 寻访好感
                $Act29Model = Master::getAct29($this->uid);
                $haogan = $Act29Model->xf_get_haogan($heropvecfg['roleid']);
                return $haogan >= $param;
            case 3: //主线任务
                $Act39Model = Master::getAct39($this->uid);
                return $Act39Model->info['id'] > $param;
            case 4: // 羁绊道具
                $Act6005Model = Master::getAct6005($this->uid);
                return $Act6005Model->isHave($heropvecfg['roleid'], $heropvecfg['id']);
            case 10: //卡牌羁绊
                $Act6006Model = Master::getAct6006($this->uid);
                return $Act6006Model->info[$heropvecfg['id']] >= $param;

        }
        return true;
    }

    /*
     * 校验玩家选择了一个剧情选项
     * params 剧情ID
     * */
    public function story($params){
        $id = Game::strval($params, 'id');
        $this->selectAward($id);
    }

    /*
     * 校验玩家选择了一个剧情选项
     * params 剧情ID
     * */
    public function zwStory($params){
        $id = Game::strval($params, 'id');
        $act2Model = Master::getAct2($this->uid);
        $info = $act2Model->info;
        $this->selectAward($id, $info['lastHeroId']);
        $act2Model->clearLast();
    }

    /*
     * 校验玩家选择了一个剧情选项
     * params 剧情ID
     * */
    public function jyStory($params){
        $id = Game::strval($params, 'id');
        $act1Model = Master::getAct1($this->uid);
        $info = $act1Model->info;
        $this->selectAward($id, $info['win']['heroid']);
        $act1Model -> deal_event();
    }

    /*
     * 校验玩家选择了一个邮件剧情选项
     * params 剧情ID
     * */
    public function emailStory($params){
        $id = Game::strval($params, 'id');
        $act6004Model = Master::getAct6004($this->uid);
        $act6004Model->addReward($id);

        //保存选择组id
        $act6001Model = Master::getAct6001($this->uid);
        if (!$act6001Model->saveCurGroup($id)){
            Master::error(STORY_DATA_EMAIL_SELECT);
        }

        $award = Game::getcfg_info('award', $id);
        if (empty($award)){
            Master::error(STORY_STORY_AWARD_UNFIND);
        }

        $this->addAward($award['res'], 1);
        $this->addAward($award['item'], 2);
        $this->addAward($award['wife'], 3);
        $this->addAward($award['sub'], 4);
        $this->addAward($award['effect'], 5);
    }

    /*
     * 校验玩家选择了一个邮件子嗣历练剧情选项
     * params 剧情ID
     * */
    public function emailSonStory($params){
        $sid = Game::strval($params, 'sid');
        $id = Game::strval($params, 'id');
        $time = Game::strval($params, 'time');

        $award = Game::getcfg_info('award', $id);
        if (empty($award)){
            Master::error(STORY_STORY_AWARD_UNFIND1);
        }
        //领取奖励储存选项
        $Act6134Model = Master::getAct6134($this->uid);
        $Act6134Model->addReward($sid,$id,$time);
        //改变已读书信的状态
        $Act6133Model = Master::getAct6133($this->uid);
        $Act6133Model->clearMsgId($sid,$time);

        $this->addAward($award['res'], 1);
        $this->addAward($award['item'], 2);
        $this->addAward($award['effect'], 5);
    }

    /*
     * 一键读取历练信件
     * params 剧情ID
     * */
    public function yjEmailSonStory($params){
        //领取奖励储存选项
        $Act6134Model = Master::getAct6134($this->uid);
        $rwds = $Act6134Model->yjAddReward();
        foreach ($rwds as $j){
            $award = Game::getcfg_info('award', $j);
            if (empty($award)){
                Master::error(STORY_STORY_AWARD_UNFIND1);
            }

            $this->addAward($award['res'], 1);
            $this->addAward($award['item'], 2);
            $this->addAward($award['effect'], 5);
        }

    }

    /*
     * 校验玩家选择了一个伙伴知己闲谈剧情选项
     * params 剧情ID
     * */
    public function heroOrwifeStory($params){
        $pid = Game::strval($params, 'pid');
        $type = Game::strval($params, 'type');
        $id = Game::strval($params, 'id');
        $type = $type == 1?'hero':'wife';
        $award = Game::getcfg_info('award', $id);
        if (!empty($award)){
            //改变门客当天闲谈领奖状态
            if ($award['wife'] !='null' || $award['sub'] !='null'){
                $act6138Model = Master::getAct6138($this->uid);
                $act6138Model->modify_get($pid,$type);
            }

            $this->addAward($award['res'], 1);
            $this->addAward($award['item'], 2);
            $this->addAward($award['wife'], 3, $pid);
            $this->addAward($award['sub'], 4, $pid);
            $this->addAward($award['effect'], 5);
        }



    }

    /*
     * 校验玩家选择了一个剧情选项
     * params 剧情ID
     * */
    public function selectAward($id, $heroid = 0){
        $story = Game::getcfg_info('xuanxiang', $id);
        $act6001Model = Master::getAct6001($this->uid);
        if (empty($story)){
            Master::error(STORY_SELECT_LIMIT);
        }

        $chose = $story['chose1'];
        if (!$this->isCanSelect($chose)){
            Master::error(STORY_SELECT_CHOSE_LIMIT);
        }

        if ($story['tiaojian'] != 0){
            $str = $story['para'];
            $arr = is_string($str) ?explode('|', $str):$str;
            $id = count($arr) > 1?intval($arr[0]):$arr;
            $param = count($arr) > 1?intval($arr[1]):$id;
            if ($param > $this->get_ep($story['tiaojian'], $id)){
                Master::error(STORY_STORY_SELECT_LIMIT);
            }
        }

        if (!$act6001Model->saveCurGroup($story['group'])){

            if (isset($story['choice']) && $story['choice'] == 0) {
                return;
            }
            error_log("find select dup group id ".$story['group'], 0);
            Master::error(STORY_DATA_EMAIL_SELECT);
        }

        if (stripos($story['group'], "xunfang") === 0){
            $act29Model = Master::getAct29($this->uid);
            $heroid = $act29Model->info['lastId'];
            $act29Model->saveLastNpc(0, 0);
        }

        $award = Game::getcfg_info('award', $story['award1']);
        if (empty($award)){
            Master::error(STORY_STORY_AWARD_UNFIND);
        }

        $this->addAward($award['res'], 1);
        $this->addAward($award['item'], 2);
        $this->addAward($award['wife'], 3, $heroid);
        $this->addAward($award['sub'], 4, $heroid);
        $this->addAward($award['effect'], 5);
    }


    /*
	 * 计算一次征收的资源数量
	 */
    private function get_ep($id, $param){
        //获取阵法信息
        if ($id == 5){
            $act6001Model = Master::getAct6001($this->uid);
            return $act6001Model->info['belief'];
        }
        else if ($id == 6){
            $act6001Model = Master::getAct6001($this->uid);
            return $act6001Model->getHeroJB($param);
        }
        else if ($id == 7){
            $act6001Model = Master::getAct6001($this->uid);
            return $act6001Model->getWifeJB($param);
        }
        else if ($id < 5 && $id > 0){
            $team = Master::get_team($this->uid);
            return $team['allep'][$id];
        }
        return 0;
    }

    private function addAward($str, $type, $heroid = 0){
        if (empty($str) || $str == '0' || $str == 'null')return;
        $arr = is_string($str) ?explode('|', $str):$str;
        $totalItems = count($arr);
        $rk = -1;
        if($type == 2 && count($arr) > 2){
            $rk = Game::get_rand_key(100,$arr,'prob');
            $totalItems = 1;
        }
        $act6001Model = Master::getAct6001($this->uid);
        for($i = 0; $i <$totalItems; $i++){
            $ss = is_string($arr[$i])?explode(',', $arr[$i]):$arr[$i];
            switch ($type) {
                case 1:
                    Master::add_item($this->uid,empty($ss['kind'])?KIND_OTHER:$ss['kind'], $ss['id'], $ss['count']);
                    break; 
                case 2:
                    if($rk >= 0){
                        if(!empty($arr[$rk])){
                            $aitemid = $arr[$rk]['id'];
                            $acount = $arr[$rk]['count'];
                            Master::add_item($this->uid,$arr[$rk]['kind'],$aitemid,$acount);
                        }
                    }else {
                        Master::add_item($this->uid,empty($ss['kind'])?KIND_ITEM:$ss['kind'], $ss['id'], $ss['count']);
                    }
                    break;
                case 3:
                    $wifeid = intval($ss[0]) == 0?$heroid:intval($ss[0]);
                    if ($wifeid != 0){
                        $act6001Model->addWifeJB($wifeid,intval($ss[1]));
                    }
                    break;   
                case 4:
                    $heroid = intval($ss[0]) == 0?$heroid:intval($ss[0]);
                    if ($heroid != 0){
                        $act6001Model->addHeroJB($heroid, intval($ss[1]));
                    }                    
                    break;
                case 5:
                    $act6001Model->addHeroSW(intval($ss[0]),intval($ss[1]));
                    break;
            }
        }
    }

    private function isCanSelect($chose){
        if (empty($chose) || $chose == '0' || $chose == 'null' || $chose == 0)return true;
        $sArr = explode('|', $chose);
        $act6001Model = Master::getAct6001($this->uid);

        $key = intval($sArr[0]);
        $id = intval($sArr[1]);
        switch ($key) {
            case 1:
                $itemModel = Master::getItem($this->uid);
                return $itemModel->sub_item(intval($id), 1);
            case 2:
                return $act6001Model->getWifeJB(intval($id)) >= intval($sArr[2]);
            case 3:
                return $act6001Model->getHeroJB(intval($id)) >= intval($sArr[2]);
            case 4:
                $userModel = Master::getUser($this->uid);
                return $userModel->info['level'] >= intval($id);
            case 5:
                return $act6001Model->getHeroSW(intval($id)) >= intval($sArr[2]);
            case 6:
                return $act6001Model->getHeroSW(intval($id)) >= $act6001Model->getMaxSW();
            case 7:
                $heroModel = Master::getHero($this->uid);
                return $heroModel->check_info(intval($id), true) != false;
            case 8:
                $wifeModel = Master::getWife($this->uid);
                return $wifeModel->check_info(intval($id), true) != false;
        }

        return false;
    }

}
