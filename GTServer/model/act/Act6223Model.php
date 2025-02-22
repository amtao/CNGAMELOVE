<?php
require_once "ActHDBaseModel.php";
/*
 * 清明节 - 拼图
 */
class Act6223Model extends ActHDBaseModel
{
    public $atype = 6223;//活动编号

    public $comment = "清明节-拼图";
    public $b_mol = "jigsaw";//返回信息 所在模块
    public $b_ctrl = "cfg";//返回信息 所在控制器
    public $hd_id = 'huodong_6223';//活动配置文件关键字
    public $need = array (10210, 10211, 10212, 10213, 10214, 10215);//活动配置 全套拼图id

    /*
     * 初始化结构体
     */
    public $_init =  array(
        /*
         * id=>num  商城档次id => 已购买数量
        */
    );

    /*
     * 添加
     * */
    public function add($id,$num = 1)
    {
        if (self::get_state() == 0){
            Master::error(ACTHD_ACTIVITY_UNOPEN);
        }
        if(!is_int($num)){
            Master::error(ACT_HD_ADD_SCORE_NO_INT);
        }
        $this->info['debris'][$id] +=$num;
        $this->save();
    }

    /*
     * 扣除道具
     * */
    public function sub($id,$num = 1)
    {
        if (self::get_state() == 0){
            Master::error(ACTHD_ACTIVITY_UNOPEN);
        }
        if(!is_int($num)){
            Master::error(ACT_HD_ADD_SCORE_NO_INT);
        }
        if ($this->info['debris'][$id]<1){
            Master::error(USER_ITEMS_SHORT);
        }
        $this->info['debris'][$id] -=$num;
    }

    /*
     * 添加一条获赠信息
     */
    public function addlog($uid,$item,$type){
        if ($type==0){
            $this->sub($item['id']);
        }
        $this->info['log'][] = array(
            'name' => $uid,
            'item' => $item,
            'type' => $type,
        );
        //截取数据表
        $max_num = 50;
        if (count($this->info['log']) > $max_num){
            $this->info['log'] = array_slice($this->info['log'],-$max_num);
        }
        $this->save();
    }

    /**
     * 获得奖励
     */
    public function get_rwds(){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        $this->info['rwd'] += 1;
        //奖励信息
        $need = $this->need;
        if(empty($need)){
            Master::error(ACTHD_NO_REWARD);
        }
        foreach ($this->info['debris'] as $v){
            if (empty($v)){
                Master::error(ACTHD_NO_REWARD);
            }
        }
        //是否凑齐
        if (!$this->check()){
            Master::error(ACTHD_NO_REWARD);
        }
        //扣掉消耗道具 一套拼图
        foreach ($this->info['debris'] as $x=>$y){
            $this->info['debris'][$x] -= 1;
        }
        //领取奖励
        Master::add_item3($this->hd_cfg['rwd']);
        $this->save();
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
            $news = $this->check()?1:0;
        }
        return $news;
    }

    /**
     * 判断是否凑齐拼图
     */
    public function check(){
        //判断是否凑齐拼图
        $need = $this->need;
        if (!empty($this->info['debris'])){
            foreach ($this->info['debris'] as $k=>$v){
                if ($v <= 0){
                    return false;
                }
                $key = array_search($k,$need);
                if ($key !== false){
                    unset($need[$key]);
                }
            }
        }
        if (empty($need)){
            return true;
        }else{
            return false;
        }
    }

    /*
     * 构造输出结构体
     */
    public function make_out(){
        //构造输出
        $this->outf = array();
        $debris = array();
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        $this->outf = $hd_cfg;

        foreach ($this->need as $id){
            if (!empty($this->info['debris'][$id])){
                $debris[] = array('id'=>$id,'num'=>$this->info['debris'][$id]);
                continue ;
            }
            $debris[] = array('id'=>$id,'num'=>0);
        }

        $this->outf['debris'] = $debris;
    }

    /*
     * 输出日志数据
     */
    public function mk_outf(){
        $outf = array();
        $temparra = $this->info['log'];//倒序输出
        if (!empty($this->info['log'])){
            foreach($temparra as $k => $v){
                $UserModel = Master::getUser($v['name']);
                $name = $UserModel->info['name'];
                $fuidInfo['name'] = $name;
                $fuidInfo['item'] = $v['item'];
                $fuidInfo['type'] = $v['type'];
                $outf[] = $fuidInfo;
            }
        }
        return $outf;
    }
}
