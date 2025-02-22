<?php
require_once "ActBaseModel.php";
/*
 * 跑马灯--/客服
 */
class Act24Model extends ActBaseModel
{
	public $atype = 24;
	
	public $comment = "跑马灯--系统/客服";
	public $b_mol = "user";//返回信息 所在模块
	public $b_ctrl = "system";//返回信息 所在控制器


	/*
	 * 初始化结构体
	 */
	public $_init =  array(
        'system'  => array(),  // 类型 => 下发时间
        'kefu'  => array(),  // 类型 => array(下发时间,下发次数)
	);
	
	/*
	 * 构造输出结构体
	 */
	public function make_out(){

    }
    public function get_outf(){
        $outf = array();


        $cfg_data = Game::get_peizhi('paoMaDeng');
        $cfg = empty($cfg_data['system'])?array():$cfg_data['system'];
        if(!empty($cfg)){
            foreach ( $cfg as $k => $v ){

                switch ($k){
                    case 11:  //中午boss开启
                        $stime = strtotime(date("Y-m-d")) + 12 * 60 * 60 ;  //开始时间
                        $etime = strtotime(date("Y-m-d")) + 14 * 60 * 60 ;  //结束时间
                        break;
                    case 12:  //晚上boss开启
                        $stime = strtotime(date("Y-m-d")) + 20 * 60 * 60 ;  //开始时间
                        $etime = strtotime(date("Y-m-d")) + 21 * 60 * 60 ;  //结束时间
                        break;
                }

                //有时间范围 且不在时间范围内
                if(!empty($stime) && !empty($etime)){
                    if( Game::get_now() < $stime || Game::get_now() >= $etime ){
                        continue;
                    }
                }

                $xftime =   $this->info['system'][$k] + $v['time'] * 60;
                //如果时间未到,不下发
                if( Game::dis_over( $xftime ) ){
                    continue;
                }

                //下发
                $outf[$k] = array(
                    'ef' => empty($v['ef'])?1:$v['ef'],  //特效: 1:默认
                    'ob' => 2, //产生对象:1:玩家,2:系统,3:客服
                    'time' => Game::get_now(), //时间
                    'msg' => $v['msg'],
                );

                //记录下发日志
                 $this->info['system'][$k] = Game::get_now();
            }
            $this->save();
        }


        return $outf;
	}

    /*
     * 返回活动信息
     */
    public function back_data(){


    }


    /*
     * 构造输出结构体
     * $type : 类型
     */
    public function kefu($type){
        if(empty($this->info['kefu'][$type])){
            $this->info['kefu'][$type] = array(
                'time' => 0,
                'num' => 0,
            );
        }
        $this->info['kefu'][$type]['time'] = Game::get_now();
        $this->info['kefu'][$type]['num'] += 1;
        $this->save();
    }

}
