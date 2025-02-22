<?php
/*
 * 跑马灯--玩家触发
 */
require_once "SevListBaseModel.php";
class Sev91Model extends SevListBaseModel
{
	public $comment = "跑马灯--玩家触发";
	
    public $b_mol = "user";//返回信息 所在模块
    public $b_ctrl = "paomadeng";//返回信息 所在控制器
	public $act = 91;//活动标签
	protected $_use_lock = false;//是否加锁

    private $h_time = 60; //缓冲时间,几秒后再次检测 (系统,客服)
	
	public $_init = array(//初始化数据

	);
	
	/*
     * 添加一条信息
     */
    public function add_msg($params){

        $type = $params[0];
        $cfg_data = Game::get_peizhi('paoMaDeng');
        $cfg = empty($cfg_data['user'])?array():$cfg_data['user'];

        if( empty($type) || empty($cfg[$type]) ){
            return false;
        }
        //如果有配置条件,获取条件
        if(!empty($cfg[$type]['need'])){
            $need  = Game::serves_str_arr($cfg[$type]['need']);
        }
        //获取跑马灯播放语句
        switch ( $type ){
            case 101: //玩家充值达到VIP5-11
                //判断条件
                if(!empty($need) && !in_array($params[2],$need) ){
                    return false;
                }
                $msg = sprintf($cfg[$type]['msg'],$params[1],$params[2]);
                break;
            case 102:  //玩家发放特定红包
                $msg = sprintf($cfg[$type]['msg'],$params[1],$params[2]);
                break;
            case 103:   //玩家最后一击击杀匈奴王
                $msg = sprintf($cfg[$type]['msg'],$params[1]);
                break;
            case 104:   //玩家邮箱领取，获得委任状
                //判断条件
                if(!empty($need) && !in_array($params[2],$need) ){
                    return false;
                }
                $msg = sprintf($cfg[$type]['msg'],$params[1],$params[3]);
                break;
            case 105:  //玩家获得VIP赠送的佳人
                //判断条件
                if(!empty($need) && !in_array($params[2],$need) ){
                    return false;
                }
                //获取佳人名字
                $cfg_wife = Game::getcfg('wife');
                if(empty($cfg_wife[$params[2]]['wname'])){
                    return false;
                }
                $msg = sprintf($cfg[$type]['msg'],$params[1],$cfg_wife[$params[2]]['wname']);
                break;
            case 106:  //玩家获得VIP赠送的门客
                //判断条件
                if(!empty($need) && !in_array($params[2],$need) ){
                    return false;
                }
                //获取门客名字
                $cfg_hero = Game::getcfg('hero');
                if(empty($cfg_hero[$params[2]]['name'])){
                    return false;
                }
                $msg = sprintf($cfg[$type]['msg'],$params[1],$cfg_hero[$params[2]]['name']);
                break;
            case 107:  //玩家新建帮会时提示
                $msg = sprintf($cfg[$type]['msg'],$params[1],$params[2]);
                break;
            case 108:  //玩家开启官宴时提示
                //判断条件
                if(!empty($need) && !in_array($params[2],$need) ){
                    return false;
                }
                $msg = sprintf($cfg[$type]['msg'],$params[1]);
                break;
            case 109:  //玩家开启官宴时提示
                //判断条件
                if(!empty($need) && !in_array($params[3],$need) ){
                    return false;
                }
                $msg = sprintf($cfg[$type]['msg'],$params[1],$params[2]);
                break;
            case 110:   //玩家击杀活动boss
                $msg = sprintf($cfg[$type]['msg'],$params[1]);
                break;
        }

        if( empty($msg) ){
            return false;
        }

    	$data = array(
    	    'type' => $type,
            'ef' => empty($cfg[$type]['ef'])?1:$cfg[$type]['ef'],  //特效: 1:默认
            'ob' => 1, //产生对象:1:玩家,2:系统,3:客服
            'time' => Game::get_now(), //时间
			'msg' => $msg,
		);
		parent::list_push($data);

    }


    /*
     * 构造输出
     */
    public function mk_outf(){
        $out_data = array();
        if(!empty($this->info)){
            foreach ($this->info as $k => $v) {
                $_data = $this->list_mk_outf($v);
                //$_data['id'] = $k;
                $out_data[$k] = $_data;
            }
        }
        return $out_data;
    }


    /**
     * 列表类 用户滚动信息
     * 检查更新输出
     * @param $uid
     * @param $model
     * @param $act
     * @return array
     */
    public function list_click($uid){

        $out_put = array();

        $h_key = 'paoMaDeng_HuanCun_'.$uid;

        $cache = $this->_getCache();
        $h_info = $cache->get($h_key);//缓存获取活动信息

        if(Game::is_over($h_info['reftime'])){
            //添加客服跑马灯
            $Sev93Model = Master::getSev93();
            $kefu = $Sev93Model->get_outf($uid);
            if(!empty($kefu)){
                foreach ($kefu as $kk => $kv){
                    $out_put[] = $kv;
                }
            }

            //添加系统跑马灯
            $Act24Model = Master::getAct24($uid);
            $system = $Act24Model->get_outf();
            if(!empty($system)){
                foreach ($system as $sk => $sv){
                    $out_put[] = $sv;
                }
            }
            //设置缓冲时间
            $reftime = Game::get_now() + $this->h_time;
            $cache->set($h_key,array('reftime' => $reftime ));
        }


        //所有信息
        $data = $this->get_outf();
        if (!empty($data)){
            end($data);
            $now_id = key($data);//把指针指向最后一个key
            //用户当前key
            $u_lid = parent::get_user_list_id($uid);
            //最多刷新 self::chat_info_num 条信息 从下向上取
            $UserModel = Master::getUser($uid);

            //最后一次登陆时间
            $Act48Model = Master::getAct48($uid);
            $get_ltime = $Act48Model->get_ltime();

            //兼容注册
            $lastlogin = max($get_ltime,$UserModel->info['regtime']);
            for($i=0; $i < $this->chat_info_num; $i++){
                if ($u_lid >= $now_id - $i){
                    break;
                }

                if( $lastlogin > $data[$now_id - $i]['time'] ){

                    continue;
                }
                $out_put[] = $data[$now_id - $i];
            }
        }

        //如果有输出 则改变用户序列ID
        if (!empty($out_put)){
            //写入ID
            parent::set_user_list_id($uid,$now_id);

            Master::back_data($uid,$this->b_mol,$this->b_ctrl,$out_put);
        }


    }



}





