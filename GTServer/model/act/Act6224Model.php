<?php
require_once "ActHDBaseModel.php";
/*
 * 清明节 - 拼图
 */
class Act6224Model extends ActHDBaseModel
{
    public $atype = 6224;//活动编号

    public $comment = "舞狮大会";
    public $b_mol = "liondance";    //返回信息 所在模块
    public $b_ctrl = "cfg";         //返回信息 所在控制器
    public $hd_id = 'huodong_6224'; //活动配置文件关键字
    public $need = 1105;            //活动道具 绣球

    /*
     * 初始化结构体
     */
    public $_init =  array(
        'date' => 0,            //日期记录 刷新任务用
        'cons' => 0,            //绣球数量
        'isGold' => 0,          //是否开启金狮奖
        'silver' =>array(       //银狮奖数据列表
            //array('id'=>0或1),是否领取银狮奖
        ),

        'gold' =>array(         //金狮奖数据列表
//            array(
//                  array('id'=>0或1),是否领取银狮奖
//            ),
        ),

        'task' =>array(         //活动任务数据列表
//            array(
//                'id'=>1,      //任务id
//                'num'=>1      //完成情况
//            ),
        ),

  );

    /*
	 * 解锁
	 */
    public function buyone(){
        $this->info['isGold'] = 1;
        $this->save();
    }

    /**
     * 活动奖励
     */
    public function get_rwds($id){
        //活动未开启
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //获取对应类型奖励数据 0:银狮奖 1:金狮奖
        $hdinfo = Game::get_key2id($this->hd_cfg['rwd'],'id');
        //绣球未满解锁条件
        if ($hdinfo[$id]['coin'] > $this->info['cons']){
            Master::error(PARAMS_ERROR.__LINE__);
        }
        //银,金狮已领过
        if (!empty($this->info['silver'][$id]) && !empty($this->info['gold'][$id])){
            Master::error(PARAMS_ERROR.__LINE__);
        }
        //银狮领过 金狮未解锁
        if (!empty($this->info['silver'][$id]) && empty($this->info['isGold'])){
            Master::error(PARAMS_ERROR.__LINE__);
        }
        $ymd = Game::get_today_id();

        //银狮
        $isAgLion = 1;
        if (empty($this->info['silver'][$id])){
            Master::add_item3($hdinfo[$id]['silver']);
            $this->info['silver'][$id] = $ymd;
        }
        //金狮
        if (!empty($this->info['isGold']) && empty($this->info['gold'][$id])){
            Master::add_item3($hdinfo[$id]['gold']);
            $this->info['gold'][$id] = $ymd;
            $isAgLion = 2;
        }
        $this->save();
    }

    /**
     * 活动任务奖励
     */
    public function get_task_rwds($id){
        //活动未开启
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        $task_info = Game::getcfg_info('lion_task',$id);
        if (empty($this->info['task'][$id]) || $this->info['task'][$id]['num'] < $task_info['num']){
            Master::error(PARAMS_ERROR);
        }
        //领取奖励
        $this->info['cons'] += $task_info['rwd'];
        $this->info['task'][$id]['get'] = 1;
        $this->save();
    }

    /**
     *任务进度
     */
    public function task_add($id,$num){
        $task_cfg = Game::getcfg('lion_task');
        $keys = array_keys($task_cfg);
        if (!in_array($id,$keys)){
            return ;
        }
        $task_info = Game::getcfg_info('lion_task',$id);
        if (empty($this->info['task'][$id]) || $this->info['task'][$id]['get']==1){
            return ;
        }
        $this->info['task'][$id]['num'] = min($this->info['task'][$id]['num']+$num,$task_info['num']);
        $this->save();
    }

    /**
     *随机任务
     */
    public function rand_task($type){
        $task_cfg = Game::getcfg('lion_task');
        $task_arr = array_rand($task_cfg,6);
        if (!empty($this->info['task'])){
            unset($this->info['task']);
        }
        foreach ($task_arr as $v){
            $this->info['task'][$v]['num'] = 0;
            $this->info['task'][$v]['get'] = 0;
        }
        if ($type){
            //扣除元宝
            Master::sub_item($this->uid,KIND_ITEM,1,50);
            $this->save();
        }else{
            $ymd = date('ymd',$_SERVER['REQUEST_TIME']);
            $this->info['date'] = $ymd;
            $this->_save();
        }
    }

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取
        if( self::get_state() == 0){
            $news = 0;
        }else{
            //奖励信息
            $task_cfg = Game::getcfg('lion_task');
            if (!empty($this->info['task'])){
                foreach ($this->info['task'] as $j=>$z){
                    if ($this->info['task'][$j]['num'] == $task_cfg[$j]['num']){
                        $news = 1;
                        break;
                    }
                }
            }
            if(!empty($this->info['cons'])){
                foreach ($this->hd_cfg['rwd'] as $k=>$v){
                    if ($v['coin'] <= $this->info['cons'] && empty($this->info['silver'][$v['id']])){
                        $news = 1;
                        break;
                    }
                }
            }
        }
        return $news;
    }

    /*
     * 构造输出结构体
     */
    public function make_out(){
        //构造输出
        $this->outf = array();
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        //增加领取状态和解锁状态
        foreach ($hd_cfg['rwd'] as $k=>$v){
            $hd_cfg['rwd'][$k]['sGet'] = 0;
            if (!empty($this->info['silver'][$v['id']])){
                $hd_cfg['rwd'][$k]['sGet'] = 1;
            }
            $hd_cfg['rwd'][$k]['gGet'] = 0;
            if (!empty($this->info['gold'][$v['id']])){
                $hd_cfg['rwd'][$k]['gGet'] = 1;
            }
        }
        $this->outf = $hd_cfg;
        $this->outf['isGold'] = $this->info['isGold'];
        $this->outf['cons'] = $this->info['cons'];
        //任务初始化和每天刷新
        $ymd = date('ymd',$_SERVER['REQUEST_TIME']);
        if (empty($this->info['task']) || $this->info['date']!=$ymd){
            $this->rand_task(false);
        }
        foreach ($this->info['task'] as $tid => $val){
            $this->outf['task'][] = array('id'=>$tid,'num'=>$val['num'],'get'=>$val['get']);
        }
    }


}
