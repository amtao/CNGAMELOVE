<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 二十四节气
 */
class Act6211Model extends ActHDBaseModel
{
	public $atype = 6211;//活动编号
	
	public $comment = "二十四节气";
    public $b_mol = "solarterms";//返回信息 所在模块
    public $b_ctrl = "purchase";//返回信息 所在控制器
    public $hd_id = 'huodong_6211';//活动配置文件关键字
    
    /*
     * 初始化结构体
     */
    public $_init = array(

    );

    /*
	 * 免费
	 */
    public function free($id = 0){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        $riqi = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        if (!empty($this->info[$riqi][$id])){
            Master::error(WELFARE_QIANDAO_LIMIT);
        }
        $rinfo  = $this->rinfo();
        if (empty($rinfo[$id])){
            Master::error(ACTHD_NO_REWARD);
        }
        if ($rinfo[$id]['type'] != 0){
            Master::error(PARAMS_ERROR);
        }
        //领取奖励
        Master::add_item3($rinfo[$id]['items']);
        $this->info[$riqi][$id] = 1;
        $this->save();
    }

    /*
	 * 元宝
	 */
    public function cash($id,$num = 1){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        $riqi = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        $rinfo  = $this->rinfo();
        if (empty($this->info[$riqi][$id])){
            $this->info[$riqi][$id] = 0;
        }
        if (empty($rinfo[$id])){
            Master::error(ACTHD_NO_REWARD);
        }
        if ($rinfo[$id]['type'] != 1){
            Master::error(PARAMS_ERROR);
        }
        if ($num < 1){
            Master::error(PARAMS_ERROR);
        }
        $this->info[$riqi][$id] += $num;
        if ($rinfo[$id]['islimit'] && $rinfo[$id]['limit'] < $this->info[$riqi][$id]){
            Master::error(HD_TYPE8_DONT_SHOPING);
        }
        //扣除元宝
        Master::sub_item($this->uid,KIND_ITEM,1,$rinfo[$id]['present']*$num);
        //领取奖励
        Master::add_item3($rinfo[$id]['items']);
        $this->save();
    }

    /*
	 * 构造输出结构体
	 */
    public function make_out(){
        if( self::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //默认输出直接等于内部存储数据
        $this->outf = array();
        $rounds = $this->rounds();
        $outof = array();
        $riqi = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
        //获取活动信息
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        $hd_cfg['count'] = $rounds;
        $hd_rwd = Game::get_key2id($hd_cfg['rwd'][$rounds],'id');
        unset($hd_cfg['rwd']);
        unset($hd_cfg['info']['no']);
        foreach ($hd_rwd as $id=>$v) {
            $value = $v;
            $value['id'] = $id;
            //是否限购
            if ($v['islimit'] == 1) {
                $c = empty($this->info[$riqi][$id])?0:$this->info[$riqi][$id];
                $value['limit'] = $v['limit'] - $c;
                $value['limit'] = $value['limit'] < 0?0:$value['limit'];
            } else {
                $value['limit'] = 0;
            }
            $outof[] = $value;
        }
        $hd_cfg['msg'] = $hd_cfg['msg'][$rounds];
        $this->outf = $outof;
        Master::back_data($this->uid,$this->b_mol,'cfg',$hd_cfg);
    }

    /*
	 * 获取当天活动第几轮
	 */
    public function rounds(){
        $rounds = ceil(($_SERVER['REQUEST_TIME']-$this->hd_cfg['info']['sTime'])/86400);
        return $rounds;
    }

    /*
	 * 获取轮次奖励活动
	 */
    public function rinfo(){
        $rounds = $this->rounds();
        $rinfo = Game::get_key2id($this->hd_cfg['rwd'][$rounds],'id');
        return $rinfo;
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
            $rinfo  = $this->rinfo();
            $riqi = intval(date('Ymd', $_SERVER['REQUEST_TIME']));
            if(!empty($rinfo)){
                foreach ($rinfo as $k=>$v){
                    if (empty($this->info[$riqi][$k]) || $this->info[$riqi][$k]<$v['limit']){
                        $news = 1; //可以领取
                    }
                }
            }
        }
        return $news;
    }
}

