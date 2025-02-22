<?php
require_once "ActHDBaseModel.php";
/*
 * 女生节
 */
class Act6228Model extends ActHDBaseModel
{
	public $atype = 6228;//活动编号
	
	public $comment = "读书节";
    public $b_mol = "studyday";//返回信息 所在模块
    public $b_ctrl = "mirror";//返回信息 所在控制器
    public $hd_id = 'huodong_6228';//活动配置文件关键字
    public $hero_pve = array();

    /*
     * 初始化结构体
     */
    public $_init = array(

    );

    /*
	 * 抽奖
	 */
    public function play($num = 1){

        if( self::get_state() == 0 ){
            Master::error(ACTHD_OVERDUE);
        }
        if (!in_array($num,array(1,10))){
            Master::error(PARAMS_ERROR);
        }
        $need = Game::getcfg_info('item',$this->hd_cfg['need']);  //消耗物品
        //扣除道具
        Master::sub_item($this->uid,KIND_ITEM,$need['id'],$num);
        $item_win = array();//抽奖结果
        $get = false;       //是否抽到羁绊
        $list = $this->hd_cfg['list'];
        $Sev6228Model = Master::getSev6228($this->hd_cfg['info']['id']);
        for ($i = 0;$i < $num;$i++){
            $key = Game::get_rand_key1($list,'prob');
            if ($list[$key]['kind'] == 96){
                //抽到羁绊移除  同次抽奖不能有相同的羁绊
                $get = true;
                //抽到羁绊添加日志
                $jb_Cfg = Game::getcfg_info('hero_pve', $list[$key]['id']);
                if ($jb_Cfg['star'] == 3 || $jb_Cfg['star'] == 4){
                    $Sev6228Model->add($this->uid,$list[$key]);
                }
                $item_win[] = $list[$key];
                $this->hero_pve[] =  $list[$key]['id'];
                unset($list[$key]);
                continue;
            }
            $item_win[] = $list[$key];
        }
        //领取奖励
        Master::add_item3($item_win);
        //抽到羁绊或剧情处理
        if ($get){
            //日志刷新
            $Sev6228Model->back_data();
            //剧情id 前端专用
            if (!empty($this->hero_pve)){
                Master::back_data($this->uid,'user','plotFragments',$this->hero_pve);
            }

        }
        Master::back_data($this->uid,$this->b_mol,'res',array('TorF'=>$get==false?0:1));
    }

    /*
	 * 构造输出结构体
	 */
    public function make_out(){
        //默认输出直接等于内部存储数据
        $this->outf = array();
        if( self::get_state() == 0 ){
            Master::error(ACTHD_OVERDUE);
        }
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        unset($hd_cfg['shop']);
        foreach ($hd_cfg['list'] as $k=>$v){
            unset($hd_cfg['list'][$k]['prob']);
        }
        $this->outf = $hd_cfg;
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
            $need = $this->hd_cfg['need'];
            $ItemModel = Master::getItem($this->uid);
            if(!empty($need) && !empty($ItemModel->info[$need]['count'])){
                $news = 1; //可以领取
            }
        }
        return $news;
    }
}

