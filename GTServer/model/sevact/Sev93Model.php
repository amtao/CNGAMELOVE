<?php
/*
 * 跑马灯--客服
 */
require_once "SevListBaseModel.php";
class Sev93Model extends SevListBaseModel
{
	public $comment = "跑马灯--客服";
	
    public $b_mol = "user";//返回信息 所在模块
    public $b_ctrl = "kefu";//返回信息 所在控制器
	public $act = 93;//活动标签
	protected $_use_lock = false;//是否加锁
    protected $_server_type = 3;//1：合服，2：跨服，3：全服
	
	public $_init = array(//初始化数据

	);


    /*
	 * 构造输出结构体
	 */
    public function make_out(){

    }

    public function get_outf($uid){

        $out_data = array();
        if(!empty($this->info)){
            foreach ($this->info as $k => $v) {

                //不在时间内不下发
                $stime = strtotime( $v['sTime'] );  //开始时间
                $etime = strtotime( $v['eTime'] );  //结束时间

                //有时间范围 且不在时间范围内
                if(!empty($stime) && !empty($etime)){
                    if( Game::get_now() < $stime || Game::get_now() >= $etime ){
                        continue;
                    }
                }

                //无消息不下发
                if(empty($v['msg'])){
                    continue;
                }

                //间隔时间未达到不下发
                $Act24Model = Master::getAct24($uid);
                if( !empty($Act24Model->info['kefu'][$k]) ){
                    $xftime =   $Act24Model->info['kefu'][$k]['time'] + $v['time'] * 60;
                    if( Game::dis_over( $xftime ) ){
                        continue;
                    }
                    $xfnum =  $v['num'] - $Act24Model->info['kefu'][$k]['num'] ;
                    if( $xfnum <= 0 ){
                        continue;
                    }
                }

                $out_data[$k] = array(
                    'ef' => empty($v['pmdef'])?1:$v['pmdef'],  //特效: 1:默认
                    'ob' => 3, //产生对象:1:玩家,2:系统,3:客服
                    'time' => Game::get_now(), //时间
                    'msg' => $v['msg'],
                );

                //更新数据
                $Act24Model->kefu($k);

            }
        }
        return $out_data;
    }

    /*
     * 添加一条信息
     */
    public function add_msg($params){

        if(!empty($this->info)){
            //超过7天清掉
            foreach ($this->info as $k => $v ){
                if(Game::day_count(strtotime($v['eTime'])) > 7){
                    unset($this->info[$k]);
                }
            }
        }

        $this->info[$params['pmdno']] = $params;
        $this->save();

    }

    /*
     * 添加一条信息
     */
    public function del_msg($key){
        unset($this->info[$key]);
        $this->save();
    }


}





