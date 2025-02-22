<?php
require_once "ActBaseModel.php";
/*
 * 御花园
 */
class Act6106Model extends ActBaseModel
{
	public $atype = 6106;//活动编号
	
	public $comment = "科举";
    public $b_mol = "daily";//返回信息 所在模块
    public $b_ctrl = "base";//返回信息 所在控制器
    
    /*
     * 初始化结构体
     */
    public $_init = array(//
        /*
         * Id 类型
         * num 剩余次数
         * answer 第几题
         * error 错几题
         * itemid 奖励物品
         * itennum 奖励的数量
         * count 举行了多少次
         */

        'types' =>array(),
        'lasttime' => 0,
        'curValue' => 0,
    );

    public function clear($flag = false){
        if ($this->info['lasttime'] < Game::day_0()){
            $types = [];
            $this->info['lasttime'] = Game::get_now();
            $this->info['curValue'] = 0;
            foreach ($this->info['types'] as $t) {
                if ($t['id'] == 1){
                    $t['num'] = 0;
                    $t['answer'] = 0;
                    $t['error'] = 0;
                }
                $types[] = $t;
            }
            $this->info['types'] = $types;
            if ($flag){
                $this->save();
            }
            else {
                $this->_save();
            }
        }
    }

    /*
     * 构造输出结构体
     */
    public function make_out(){
        //默认输出直接等于内部存储数据
        $this->clear();
        $arr = array();
        foreach ($this->info['types'] as $t) {
            $item = array();
            $item['id'] = $t['id'];
            $item['num'] = $t['num'];
            $item['answer'] = $t['answer'];
            $item['error'] = $t['error'];
            $item['item'] = array('kind'=>1, 'id'=>$t['itemid'], 'count'=>$t['itennum']);
            $item['count'] = $t['count'];
            $arr[] = $item;
        }
        $this->outf = $arr;
    }

    public function add_type($type, $param){
        if ($type > 2)return false;
        $sys = Game::getcfg_info('exam_type', $type);
        $ft = $this->getType($type);
        $flag = false;
        foreach ($sys['ticket'] as $item){
            if ($item['type'] == 1){
                if ($this->info['curValue'] < $item['value'] && $param >= $item['value']){
                    $this->info['curValue'] = $item['value'];
                    $ft['num'] += 1;
                    $flag = true;
                }
            }
            else if ($item['type'] == $type){
                if ($param % $item['value'] == 0 && $param != 0){
                    $ft['num'] += 1;
                    $flag = true;
                }
            }
        }
        if ($flag){
            $this->saveType($ft);
        }
        return $flag;
    }

    private function getType($type){
        foreach ($this->info['types'] as $t) {
            if ($t['id'] == $type){
                $ft = $t;
                continue;
            }
        }
        return empty($ft)?$ft = array('id' => $type,
            'num' => 0,
            'answer' => 0,
            'error' => 0,
            'itemid' => 0,
            'itennum' => 0,
            'count' => 0):$ft;
    }

    private function saveType($type){
        $types = array();
        $flag = false;
        foreach ($this->info['types'] as $t) {
            if ($t['id'] == $type['id']){
                $types[] = $type;
                $flag = true;
                continue;
            }
            $types[] = $t;
        }
        if (!$flag)$types[] = $type;
        $this->info['types'] = $types;
        $this->save();
    }

    public function answer($id){
        $type = floor($id / 10000);
        $answer = $id % 10000;

        $sys = Game::getcfg_info('exam_type', $type);
        $ft = $this -> getType($type);

        if (($ft['num'] < 1 && $answer == 0)){
            Master::error(KEJU_COUNT_TIP);
        }

        if (count($sys['reward']) > 0){
            $r = floor(rand(0, count($sys['reward'])));
            $item = $sys['reward'][$r];
            $ft['itemid'] = empty($item)?0:$item['itemid'];
            $ft['itennum'] = empty($item)?0:$item['count'];
        }
        else {
            $ft['itemid'] = 0;
            $ft['itennum'] = 0;
        }

        if ($ft['answer'] == 0 && $answer != 0){
            Master::error(KEJU_ANSWER_ERROR);
        }

        switch ($answer){
            case 0:
                $ft['answer'] = 1;
                $ft['error'] = 0;
                break;
            case 1:
                $ft['answer'] += 1;
                $Act6107Model = Master::getAct6107($this->uid);
                $Act6107Model -> addExp(1);
                if ($ft['itemid'] != 0 && $ft['itennum'] != 0)Master::add_item($this->uid, KIND_ITEM, $ft['itemid'], $ft['itennum']);
                break;
            case 2:
                $ft['answer'] += 1;
                $ft['error'] += 1;
                break;
        }

        if ($ft['answer'] > $sys['total']){
            if ($ft['error'] < $sys['lose']){
                $Act6107Model = Master::getAct6107($this->uid);
                $lvSys = Game::getcfg_info('exam_lv', $Act6107Model->info['level']);
                $rwds = $lvSys["typereward".$type];
                $key = Game::get_rand_key1($rwds, 'prob_10000');
                $rwd = $rwds[$key];
                if(!empty($rwd) && !empty($rwd['type'])){
                    //阵法
                    $TeamModel = Master::getTeam($this->uid);
                    $rwd['count'] = Game::type_to_count($rwd['type'],$TeamModel->info['allep']);
                }

                if (!empty($rwd)){
                    Master::add_item2($rwd);
                }
            }
            $ft['num'] -= 1;
            $ft['answer'] = 0;
            $ft['error'] = 0;
            if ($type == 1)$ft['count'] += 1;
            $this->add_type($type + 1, $ft['count']);
        }

        $this->saveType($ft);
    }


}














