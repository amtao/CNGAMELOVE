<?php
require_once "ActHDBaseModel.php";

/*
 * 活动6137
 */
class Act6137Model extends ActHDBaseModel
{
    public $atype = 6137;//活动编号
    public $comment = "伙伴语音包活动";
    public $b_mol = "voice";//返回信息 所在模块
    public $b_ctrl = "voices";//子类配置
    public $hd_cfg ;//活动配置
    public $hd_id = 'huodong_6137';//活动配置文件关键字


    /*
     * 初始化结构体
     */
    public $_init =  array(
//        'heroVoice'=>array(),
//        'wifeVoice'=>array(),

    );


    /**
     * @param unknown_type $uid   玩家id
     * @param unknown_type $id    活动id
     */
    public function __construct($uid)
    {
        $this->uid = intval($uid);
        //获取活动配置
        Common::loadModel('HoutaiModel');
        $this->hd_cfg = HoutaiModel::get_huodong_info($this->hd_id);
        if(!empty($this->hd_cfg['info']['id'])){
            parent::__construct($uid,$this->hd_cfg['info']['id']);//执行基类的构造函数
        }
    }

    /**
     * 获取是否有红点  (可领取)
     * $news 0:不可以领取   1:可以领取
     */
    public function get_news(){
        $news = 0; //不可领取
        return $news;
    }

    /**
     * 获得奖励
     * $id   语音包id
     * $type 语音包类型 1:伙伴 2:知己
     */
    public function buy($id){
        //活动状态
        if( parent::get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }
        //匹配活动数据
        $brwd = $this->hd_cfg['rwd'];
        $voice = array();
        foreach ($brwd as $v){
            if ($v['uid'] == $id){
                $voice = $v;
            }
        }

        //校验数据
        if(empty($voice) || empty($voice['need'])){
            Master::error(PARAMS_ERROR);
        }

        //判断类型
        $key = $voice['type'] == 1 ?'hero':'wife';
        //
        $get_voicecfg = Game::getcfg($key.'_talk');
        foreach ($voice['voiceid'] as $x){
            if (empty($get_voicecfg[$x])){
                Master::error(NOT_HERO_OR_WIFE_CONFIG);
            }

            if (!empty($this->info[$key.'Voice']) && in_array($x,$this->info[$key.'Voice'])){
                Master::error(HAVE_VOICED);
            }
            $this->info[$key.'Voice'][]= $x;
        }
        //扣除元宝
        Master::sub_item($this->uid,KIND_ITEM,1,$voice['need']);

        $this->save();

    }

    /*
     * 基本的免费语音包
     */
    public function free_voice(){
        $hero_voice = Game::getcfg('hero_talk');
        $wife_voice = Game::getcfg('wife_talk');
        if ( empty($hero_voice) && empty($wife_voice)){
            Master::error(TALKFIFE_NOT_DATA);
        }
        $free_hero = array();
        $free_wife = array();
        foreach ($hero_voice as $a){
            if ($a['type'] == 1){
                array_push($free_hero,$a['voiceid']);
            }
        }
        foreach ($wife_voice as $i){
            if ($i['type'] == 1){
                array_push($free_wife,$i['voiceid']);
            }
        }

        return $data = array(
            'heroVoice'=> $free_hero,
            'wifeVoice'=> $free_wife,
        );


    }

    /*
     * 构造输出结构体
     */
    public function make_out(){
        //构造输出
        $this->outf = array();

        //我的语音数据
        if (empty($this->info)){
            $this->outf = $this->free_voice();
        }else{
            $free_voice = $this->free_voice();
            $this->info['heroVoice'] = empty($this->info['heroVoice'])?$free_voice['heroVoice']:array_unique(array_merge($this->info['heroVoice'],$free_voice['heroVoice']));
            $this->info['wifeVoice'] = empty($this->info['wifeVoice'])?$free_voice['wifeVoice']:array_unique(array_merge($this->info['wifeVoice'],$free_voice['wifeVoice']));
            $this->outf = $this->info;
        }
        //活动配置
        if (!empty($this->hd_cfg)){
            $hd_cfg = $this->hd_cfg;
            $this->outf['cfg'] = $hd_cfg['rwd'];
        }


    }

    /*
	 * 返回活动信息
	 */
    public function back_data(){
        $this->make_out();
        Master::back_data($this->uid,$this->b_mol,$this->b_ctrl,$this->outf);
    }

}
