<?php
require_once "ActBaseModel.php";
/*
 *  邮件
 */
class Act6005Model extends ActBaseModel
{
	public $atype = 6005;//活动编号

	public $comment = "羁绊剧情等级";
	public $b_mol = "scpoint";//返回信息 所在模块
	public $b_ctrl = "jbItem";//返回信息 所在控制器

	/*
	 * 初始化结构体
	 */
	public $_init =  array(
	    'jbItem'=>array(),
        'group' =>array(),
	);

    public function make_out(){
        if (!isset($this->info['jbItem']))$this->reset();
        $data = array();
        foreach ($this->info['jbItem'] as $k => $v){
            $item = array('id'=> $k);
            $item['jibans'] = array();
            foreach ($v as $key => $jb){
                if ($key == "prop")continue;
                $item['jibans'][] = array('id'=>$key, 'level' => $jb['lv'], 'exp' => $jb['exp']);
            }
            $prop = $v['prop'];
            $item['ep'] = array('e1'=>$prop[1],'e2'=>$prop[2],'e3'=>$prop[3],'e4'=>$prop[4]);
            $data[] = $item;
        }
        $this->outf = $data;
    }

	public function addItem($id, $count = 1){
        if (!isset($this->info['jbItem']))$this->reset();
        $item = Game::getcfg_info('hero_pve',$id);
        $heroid = $item['roleid'];
        if (empty($this->info['jbItem'][$heroid])){
            $this->info['jbItem'][$heroid] = array();
        }

        $lastLv = 0;
        $list = $this->info['jbItem'][$heroid];
        if (empty($list[$id])){
            $list[$id] = array('lv' => 1, 'exp' => $count - 1);
            $this->check();
        }
        else {
            $lastLv = $list[$id]['lv'];
            $list[$id]['exp'] += $count;
        }

        $lvs = Game::getcfg("hero_jb_lv");
        $lvMax = 1;
        $flag = false;
        foreach ($lvs as $lSys){
            $lvMax = $lSys['story_lv'] > $lvMax?$lSys['story_lv']:$lvMax;
            if ($lSys['story_num'] > $list[$id]['exp']){
                $list[$id]['lv'] = $lSys['story_lv'];
                $flag = true;
                break;
            }
        }
        if (!$flag){
            $list[$id]['lv'] = $lvMax;
        }
        if ($lastLv != $list[$id]['lv']){
            $list['prop'] = $this->getProp($list);
        }
        $this->info['jbItem'][$heroid] = $list;
        $this->save();
        Game::cmd_flow(6005, $id, 1, $count);
        if ($lastLv != $list[$id]['lv']){
            $TeamModel = Master::getTeam($this->uid);
            $TeamModel->reset(5);
            $TeamModel->back_hero();
        }
    }

    public function getHeroId($id){
        if (!isset($this->info['jbItem']))$this->reset();
        if (empty($this->info['jbItem'][$id] )){
            return array(1 => 0,2 => 0,3 => 0,4 => 0);
        }
        return $this->info['jbItem'][$id]['prop'];
    }

    public function isHave($hid, $id){
        if (!isset($this->info['jbItem']))$this->reset();
        if (empty($this->info['jbItem'][$hid]) || empty($this->info['jbItem'][$hid][$id]))return false;
        return true;
    }

    function getGroupProp(){
        $ep = array(1 => 0,2 => 0,3 => 0,4 => 0);
        if (!empty($this->info['group'])){
            foreach ($this->info['group'] as $v){
                $ep = Game::epaddr1($ep,$v);
            }
        }
        return $ep;
    }

    private function getProp($list){
        $ep = array(1 => 0,2 => 0,3 => 0,4 => 0);
        foreach ($list as $k => $v){
            if ($k == 'prop')continue;
            $item = Game::getcfg_info('hero_pve', $k);
            if ($item['specMsg'] == 0)continue;
            $lv = Game::getcfg_info('hero_jb_prop', $v['lv']);
            foreach($lv['count'] as $prop){
                if ($prop['star'] == $item['star']){
                    $ep[$item['specMsg']] += $prop['count'];
                    continue;
                }
            }
        }
        return $ep;
    }

    //改写数据格式
    private function reset(){
        if (!isset($this->info['group']) && !isset($this->info['jbItem'])){
            if (empty($this->info)){
                $this->info = $this->_init;
            }
        }
        $this->check();
    }
    //剧情图鉴
    private function check(){
        if (!isset($this->info['jbItem'])){
            $info = $this->info;
            $this->info = $this->_init;
            $this->info['jbItem'] = $info;
        }
        //拿到已拥有的所有羁绊剧情id集合
        $jbItem = array();
        foreach ($this->info['jbItem'] as $hid=>$hjb){
            foreach ($hjb as $key => $val){
                if ($key!='prop'){
                    $jbItem[] = $key;
                }
            }
        }
        $group_cfg = Game::getcfg('hero_treegroup');
        //判断是否凑齐 凑齐存入加成
        $flg = false;//是否刷新缓存
        foreach ($group_cfg as $gid=>$ginfo){
            if (isset($this->info['group'][$gid])){
                continue;
            }
            $group = $ginfo['group'];
            $diff = array_diff($group,$jbItem);
            if (count($diff) == 1){
                $flg = true;
                $ep = array(1 => 0,2 => 0,3 => 0,4 => 0);
                $prop = $ginfo['prop'][0];
                $ep[$prop['prop']] = $prop['value'];
                $this->info['group'][$gid] = $ep;
            }
        }
        $this->_save();
        //刷新缓存
        if ($flg){
            $TeamModel = Master::getTeam($this->uid);
            $TeamModel->reset(5);
            $TeamModel->back_hero();
        }

    }




}
