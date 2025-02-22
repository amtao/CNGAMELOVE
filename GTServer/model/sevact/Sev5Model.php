<?php
/*
 * 历代王爷
 */
require_once "SevBaseModel.php";
class Sev5Model extends SevBaseModel
{
	public $comment = "历代王爷";
	public $act = 5;//活动标签
	protected $_save_msg_cd = 600;
	protected $_use_lock = false;//是否加锁


	public $_init = array(//初始化数据

	);

	/*
	 * 构造业务输出数据
	 */
	public function mk_outf(){
		$outf = array();
        $chenghao_cfg = Game::getcfg('chenghao');
		foreach($this->info as $chid => $chinfo){
            $eData = end($chinfo);
            if (Game::is_over($eData['endT'])){
                continue;
            }
            //1:跨服势力 2:跨服好感 3:跨服宫斗 4:跨服宫殿宫斗
			$key = $chenghao_cfg[$chid]['kuatype'];
			$userInfo = $this->get_user_info($eData['uid']);
			$outf[] = array('topKey'=>$key,'topPlayer'=>$userInfo);
		}
		return $outf;
	}

    /*
     * 返回未过期的榜单key集合
     */
    public function get_keys(){
        $keys = array();
        $chenghao_cfg = Game::getcfg('chenghao');
        foreach($this->info as $chid => $chinfo){
            $eData = end($chinfo);
            if (Game::is_over($eData['endT'])){
                continue;
            }

            $keys[] = $chenghao_cfg[$chid]['kuatype'];

        }
        return $keys;
    }


	/**
	 * 插入王爷信息
	 * $uid   玩家id
	 * $chid  称号id
	 */
	public function add_wangye($uid,$chid){
		//过滤称号错误
		$chenghao_cfg = Game::getcfg_info('chenghao',$chid);
		if(empty($chenghao_cfg)){
			Master::error(DESIGN_ERROR);
		}
		//到期时间(默认30天)
		$endT = $_SERVER['REQUEST_TIME'] +30*86400;
		$this->info[$chid][] = array(
			'uid' => $uid,
			'getT' => $_SERVER['REQUEST_TIME'],   //获得时间
			'endT' => $endT,
		);
		$this ->save();
		$this->back_data();
	}

    /*
     * 返回协议信息
     */
    public function get_user_info($uid){
        $Sev_Cfg = Common::getSevidCfg(Game::get_sevid($uid));//子服ID
        Common::loadModel('ServerModel');
        $serverInfo = ServerModel::getServInfo($Sev_Cfg['he']);

        //获取公共基础信息
        $cinfo = Master::fuidData($uid);
        if(strpos($serverInfo['name']['zh'],'|') !== false){
            $serverZh = $serverInfo['name']['zh'];
            $serverName = explode('|',$serverZh);
            $cinfo['sevname'] = $serverName[1];
        }else{
            $cinfo['sevname'] = $serverInfo['id'].'区';
        }
        return $cinfo;
    }

	/*
	 * 返回协议信息
	 */
	public function back_data(){
		$outf = $this->get_outf();
        Master::back_data(0,'fengxiandian','info',$outf);
	}
}
