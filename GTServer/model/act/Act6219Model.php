<?php
require_once "ActBaseModel.php";
/*
 * 领袖气质
 */
class Act6219Model extends ActBaseModel
{
    public $atype = 6219;//活动编号

    public $comment = "领袖气质";
    public $b_mol = "";//返回信息 所在模块
    public $b_ctrl = "";//子类配置

    /*
     * 初始化结构体
     */
    public $_init =  array(
    );

    /*
     * 检验是否满足条件
     */
    public function check(){
        //获取门客id集合
        $HeroModel = Master::getHero($this->uid);
        if (!empty($HeroModel->info)){
            $heroIds = $HeroModel->get_all_heros();
            $leaderAt = Game::getcfg('hero_leaderat');
            //遍历满足条件的信息
            foreach ($leaderAt as $v){
                if (isset($this->info['group'][$v['id']])){
                    continue;
                }
                $this->leader_add($v,$heroIds);
            }
        }

    }

    /*
     * 新增
     */
    private function leader_add($data,$heros){
            $group = $data['activation'];
            foreach ($heros as $x){
                $key = array_search($x,$group);
                if ($key !== false){
                    unset($group[$key]);
                }
            }
            if (empty($group)){
                $this->info['group'][$data['id']] = 1;
                $leader_cfg = $this->leader_cfg($data['star'],1);
                $this->info['score'][$data['id']] = $leader_cfg['ep'];
                $this->info['drug'][$data['id']] = empty($leader_cfg['drug'])?0:$leader_cfg['drug'];
                $this->_save();
            }
    }

    /*
     * 获取等级配置
     */
    private function leader_cfg($star,$lv){
        $leaderAt = Game::getcfg('hero_leaderexp');
        $lid = intval($star)*1000+intval($lv);
        if (empty($leaderAt[$lid])){
            Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }
        return $leaderAt[$lid];
    }

    /*
     * 升级
     */
    public function leader_lv_up($id){
        if (empty($this->info['group'][$id])){
           Master::error($this->hd_id.GAME_LEVER_UNOPENED);
        }
        $lv = $this->info['group'][$id];
        $leaderAt = Game::getcfg_info('hero_leaderat',$id);
        $leader_cfg_next = $this->leader_cfg($leaderAt['star'],$lv+1);
        Master::sub_item($this->uid,KIND_ITEM,$leader_cfg_next['itemid'],$leader_cfg_next['cost']);
        $this->info['group'][$id] += 1;
        $this->info['score'][$id] = $leader_cfg_next['ep'];
        $this->info['drug'][$id] = empty($leader_cfg_next['drug'])?0:$leader_cfg_next['drug'];
        $this->_save();
        $TeamModel = Master::getTeam($this->uid);
        $TeamModel->reset(1);
        $TeamModel->back_hero();
    }

    /*
     * 获取属性加成
     */
    public function get_add_ep(){
        $this->check();
        $ep[1] = $ep[2] = $ep[3] = $ep[4] = empty($this->info['group'])?0:array_sum($this->info['score']);
        return $ep;
    }

    /*
     * 获取领袖等级
     * $leaderid 领袖组合id
     */
    public function get_leadlv($leaderid){
        return empty($this->info['group'][$leaderid])?0:$this->info['group'][$leaderid];
    }

    /*
     * 获取领袖药物百分比加成
     */
    public function get_lead_drug(){
        if (!isset($this->info['drug']) || array_sum($this->info['drug']) == 0){
            return 0;
        }else{
            $sum = array_sum($this->info['drug'])/10000+1;
            $drug_arr['e1'] = $drug_arr['e2'] = $drug_arr['e3'] = $drug_arr['e4'] = $sum;
            return $drug_arr;
        }
    }


    /*
     * 返回活动信息
     * 使这个函数 无效
     */
    public function back_data(){
        return;
    }
}
















