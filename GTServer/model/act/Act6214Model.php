<?php
require_once "ActHDBaseModel.php";
/*
 * 祈福
 */
class Act6214Model extends ActHDBaseModel
{
	public $atype = 6214;//活动编号
	
	public $comment = "福星锦鲤活动";
    public $b_mol = "luckyCharm";   //返回信息 所在模块
    public $b_ctrl = "share";       //返回信息 所在控制器
    public $hd_id = 'huodong_6214'; //活动配置文件关键字
    public $sign = 'a881ea41897d08205af346ed00851180';
    public $sharepicture = 'http://gt-cdn.zanbugames.com/shareicon/shareicon-%E7%9B%B4%E8%A7%92-512.png';
    public $shareurl = array(        //分享链接
        1=>'https://gtmz.xingyuhudong.com/h5_2019fuqi/share01.html?stage=1&name=%{name}&sign=%{sign}&serverid=%{serverid}&uid=%{uid}&os_type=%{pf}',
        2=>'https://gtmz.xingyuhudong.com/h5_2019fuqi/share02.html?stage=2&name=%{name}&sign=%{sign}&serverid=%{serverid}&uid=%{uid}&os_type=%{pf}',
        3=>'https://gtmz.xingyuhudong.com/h5_2019fuqi/share03.html?stage=3&name=%{name}&sign=%{sign}&serverid=%{serverid}&uid=%{uid}&os_type=%{pf}',
        4=>'https://gtmz.xingyuhudong.com/h5_2019fuqi/roller.html?stage=4&name=%{name}&sign=%{sign}&serverid=%{serverid}&uid=%{uid}&os_type=%{pf}',
    );
    public $lookurl = array(        //查看进度链接
        1=>'https://gtmz.xingyuhudong.com/h5_2019fuqi/check01.html?stage=1&name=%{name}&serverid=%{serverid}&uid=%{uid}&os_type=%{pf}',
        2=>'https://gtmz.xingyuhudong.com/h5_2019fuqi/check02.html?stage=2&name=%{name}&serverid=%{serverid}&uid=%{uid}&os_type=%{pf}',
        3=>'https://gtmz.xingyuhudong.com/h5_2019fuqi/check03.html?stage=3&name=%{name}&serverid=%{serverid}&uid=%{uid}&os_type=%{pf}',
        4=>'https://gtmz.xingyuhudong.com/h5_2019fuqi/choujiang.html?stage=4&name=%{name}&serverid=%{serverid}&uid=%{uid}&os_type=%{pf}',
    );

    
    /*
     * 初始化结构体
     */
    public $_init = array(

    );

    /*
	 * 分享次数获得
	 */
    public function add($num=1){
       $hd_info = $this->get_stage();
       if (empty($hd_info)){
           error_log('huodong_6214_not_hd_info'.$_SERVER['REQUEST_TIME']);
       }
       if ($hd_info['id'] == 4){
           return ;
       }
       if (empty($this->info['cons'])){//可抽奖次数
           $this->info['cons'] = 0;
       }
       $this->info[$hd_info['id']] = empty($this->info[$hd_info['id']])?$num:$this->info[$hd_info['id']]+$num;
       //满足条件 增加抽奖次数
       if ($this->info[$hd_info['id']] >= $hd_info['need'] && $this->info['cons'] < $hd_info['id']){
           $this->info['cons'] += 1;
       }
       $this->save();
    }

    /*
	 * 抽奖
	 */
    public function play($num = 1){
        $hd_info = $this->get_stage();
        if (empty($hd_info)){
            Master::error(ACT_HD_INFO_ERROR);
        }
        if ($hd_info != 4){
            Master::error(SHOP_ACTIVITY_UNOPEN);
        }
        if ($this->info['cons'] < $num){
            Master::error(JINGYING_COUNT_LIMIT);
        }
        $key = Game::get_rand_key1($hd_info['rwd'],'prob');
        //领取奖励
        Master::add_item3($hd_info[$key]);
        $this->info['cons'] -= $num;
        $this->save();
    }
    /*
	 * 获取当前阶段
	 */
    public function get_stage(){
        $res = array();
        foreach ($this->hd_cfg['stage'] as $k=>$v){
            $stime = strtotime($v['stime']);
            $nexttime = strtotime($this->hd_cfg['stage'][$k+1]['stime']);
            $nowtime = $_SERVER['REQUEST_TIME'];
            if ($nowtime >= $stime && $nowtime < $nexttime){
                return $this->hd_cfg['stage'][$k];
            }
        }
        return $res;
    }

    /*
	 * 构造输出结构体
	 */
    public function make_out(){
        //默认输出直接等于内部存储数据
        $this->outf = array();
        $hd_cfg = $this->hd_cfg;
        $hd_cfg['info']['id'] = $hd_cfg['info']['no'];
        unset($hd_cfg['info']['no']);
        foreach ($hd_cfg['stage'] as $k=>$v){
            if (!empty($this->lookurl[$v['id']])){
                $hd_cfg['stage'][$k]['url'] = $this->lookurl[$v['id']];
                $hd_cfg['stage'][$k]['share'] = $this->shareurl[$v['id']];
            }
        }
        $rinfo = $this->get_stage();
        //判断活动进行阶段
        $current = 0;
        if (empty($rinfo)){
            $current = $_SERVER['REQUEST_TIME']<$hd_cfg['stage'][0]['stime']?1:4;  // 1:活动开始前 4:活动结束后展示
        }
        $hd_cfg['current'] = empty($current)?$rinfo['id']:$current;
        $hd_cfg['sharepicture'] = $this->sharepicture;
        $hd_cfg['sign'] = $this->sign;
        $this->outf = $hd_cfg;
    }

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        if( self::get_state() == 0){
            $news = 0;
        }else{
            $news = 1; //可以领取
        }
        return $news;
    }
}

