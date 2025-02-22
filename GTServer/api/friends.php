<?php
//排行榜
class FriendsMod extends Base
{
	/**
	 * 好友列表
	 */
	public function flist(){
		$Act130Model = Master::getAct130($this->uid);
		$Act130Model->back_data();
	}

	/**
	 * 好友申请列表 - 同意
	 * @param $params 0:所有 1：个人
	 */
	public function fok($params){
		$fuid = Game::intval($params,"fuid");
		//申请列表
		$Act131Model = Master::getAct131($this->uid);
		$Act130Model = Master::getAct130($this->uid);
		$Act132Model = Master::getAct132($this->uid);
		$FriendModel = Master::getFriend($this->uid);
		if (!$fuid) {
			$userModel = Master::getUser($this->uid);
			$vip_cfg_info = Game::getcfg_info('vip',$userModel->info['vip']);
			$friendNum = count($Act130Model->info['list']);
			$Act132Model = Master::getAct132($this->uid);
			foreach($Act131Model->info['list'] as $k => $v){
				if ($friendNum >= $vip_cfg_info['friendNum']) {
					break;
				}
				$Act97Model = Master::getAct97($k);
				if ( isset($Act97Model->info['list'][$this->uid]) ) {
					continue;
				}

				$Act130Model->add1($k);
				$Act130Model = Master::getAct130($k);
				$Act130Model->add1($this->uid);
				$Act131Model->sub($k);
				$Act132Model->sub($k);
				$Act132Model = Master::getAct132($k);
				$Act132Model->sub($this->uid);
				$Act131Model = Master::getAct131($k);
				$Act132Model->sub($this->uid);
				$friendNum++;
			}
		} else {
			if(empty($Act131Model->info['list'][$fuid])){
				Master::error(FRIEND_APPLY_NO);
			}

			$Act97Model = Master::getAct97($this->uid);
			if ( isset($Act97Model->info['list'][$fuid]) ) {

				//删除申请记录
				$Act131Model = Master::getAct131($this->uid);
				$Act131Model->sub($fuid);

				$Act132Model = Master::getAct132($fuid);
				$Act132Model->sub($this->uid);
				Master::error(FRIEND_OTHER_HEIMINGDAN);
			}

			$Act97Model = Master::getAct97($fuid);
			if ( isset($Act97Model->info['list'][$this->uid]) ) {

				//删除申请记录
				$Act131Model = Master::getAct131($this->uid);
				$Act131Model->sub($fuid);

				$Act132Model = Master::getAct132($fuid);
				$Act132Model->sub($this->uid);

				Master::error(FRIEND_HEIMINGDAN);
			}

			$Act130Model->add($fuid);
			$Act130Model = Master::getAct130($fuid);
			$Act130Model->add($this->uid);
			$Act131Model->sub($fuid);
			$Act132Model->sub($fuid);
			$Act132Model = Master::getAct132($fuid);
			$Act132Model->sub($this->uid);
			$Act131Model = Master::getAct131($fuid);
			$Act132Model->sub($this->uid);
		}
	}

	/**
	 * 好友 - 申请
	 * @param $params
	 */
	public function fapply($params){
		$fuid = $params['fuid'];
		//合服范围限制
		Game::isHeServerUid($fuid);
		if($fuid == $this->uid) {
			return;
		}

		$Act130Model = Master::getAct130($this->uid);
		if (key_exists($fuid,$Act130Model->info['list'])) {
			Master::error(FRIEND_IS_ADD);
		}

		$userModel = Master::getUser($this->uid);
		$vip_cfg_info = Game::getcfg_info('vip',$userModel->info['vip']);
		$friendNum = count($Act130Model->info['list']);
		if ($friendNum >= $vip_cfg_info['friendNum']) {
			Master::error(FRIEND_NUM_MAX);
		}

		$userModel = Master::getUser($fuid);
		$vip_cfg_info = Game::getcfg_info('vip',$userModel->info['vip']);
		$Act130Model = Master::getAct130($fuid);
		$friendNum = count($Act130Model->info['list']);
		if ($friendNum >= $vip_cfg_info['friendNum']) {
			Master::error(FRIEND_OTHER_NUM_MAX);
		}

		$Act97Model = Master::getAct97($this->uid);
		if ( isset($Act97Model->info['list'][$fuid]) ) {
			Master::error(FRIEND_OTHER_HEIMINGDAN);
		}

		$Act97Model = Master::getAct97($fuid);
		if ( isset($Act97Model->info['list'][$this->uid]) ) {
			Master::error(FRIEND_HEIMINGDAN);
		}

		$Act132Model = Master::getAct132($this->uid);
		if (key_exists($fuid,$Act132Model->info)) {
			Master::error(FRIEND_IS_APPLY);
		}

		$Act132Model->add($fuid);

		//申请
		$Act131Model = Master::getAct131($fuid);
		$Act131Model->apply($this->uid);
	}

	/**
	 * 好友申请列表
	 * @param $params
	 */
	public function fapplylist($params){
		$Act131Model = Master::getAct131($this->uid);
		$Act131Model->back_data();
	}

	/**
	 * 好友申请列表 - 拒绝
	 * @param $params
	 * ['fuid'] : 玩家编号    0:标识一键拒绝
	 */
	public function fno($params){
		$fuid = $params['fuid'];

		//申请列表
		$Act131Model = Master::getAct131($this->uid);
		//删除申请记录
		$Act131Model->sub($fuid);

		$Act132Model = Master::getAct132($fuid);
		$Act132Model->sub($this->uid);
	}

	/**
	 * 好友列表 - 删除
	 * @param $params
	 * ['fuid'] : 玩家编号
	 */
	public function fsub($params){
		$fuid = $params['fuid'];
		$Act130Model = Master::getAct130($this->uid);
		$Act130Model->sub($fuid);
		$Act130Model = Master::getAct130($fuid);
		$Act130Model->sub($this->uid);
		$Act135Model = Master::getAct135($this->uid);
		$Act135Model->sub($fuid);
		$Act135Model = Master::getAct135($fuid);
		$Act135Model->sub($this->uid);
	}

	/**
	 * 亲家列表
	 */
	public function qjlist($params){

		$Act133Model = Master::getAct133($this->uid);
		$Act133Model->back_data();

		$team = Master::get_team($this->uid);
		$sonshili = empty($team['qjsonshili'])?0:$team['qjsonshili'];
		Master::back_data($this->uid,'friends','sonshili',array('num' => $sonshili));
	}

	/**
	 * 亲家拜访
	 * ['fuid'] : 0:一键拜访          >0:uid拜访
	 */
	public function qjvisit($params){

		$fuid = $params['fuid'];
		$Act133Model = Master::getAct133($this->uid);
		$get_outf = $Act133Model->qj_out();
		if(empty($get_outf)){
			Master::error(FRIEND_QJ_NOHAS);
		}

		if(empty($fuid)){  //0:一键拜访
			$is_has = 0;
			foreach($get_outf as $k => $v){

                $Act90Model = Master::getAct90($this->uid);
                $Act90Model->clearQjTip($k,0);

				$Act134Model = Master::getAct134($this->uid);
				$flag = $Act134Model->myadd($k,false);
				if($flag){
					$is_has = 1;
					$Act134Model = Master::getAct134($k);
					$Act134Model->fadd($this->uid);
				}
			}
			if(!$is_has){
				Master::error(FRIEND_QJ_BAIFANG);
			}
		}else{  //单人拜访
			if(empty($get_outf[$fuid])){
				Master::error(FRIEND_QJ_ERROR);
			}
            $Act90Model = Master::getAct90($this->uid);
            $Act90Model->clearQjTip($fuid,0);

			$Act134Model = Master::getAct134($this->uid);
			$Act134Model->myadd($fuid);
			$Act134Model = Master::getAct134($fuid);
			$Act134Model->fadd($this->uid);
		}

		$Act133Model->back_data();
		//返回更新信息
		$SonModel = Master::getSon($this->uid);
		$SonModel->getBase();

		//重新构造阵法
		$TeamModel  = Master::getTeam($this->uid);
		$TeamModel->reset(3);
	}

	/**
	 * 检测聊天信息
	 * ['fuid']: 对象
	 */
	public function ffchat($params){
		//是否合服范围内
		$fuid = $params['fuid'];
		Game::isHeServerUid($fuid);
        $Act97Model = Master::getAct97($fuid);
        if(!empty($Act97Model->info['list']) && isset($Act97Model->info['list'][$this->uid]) ){
            Master::error(FRIEND_HEIMINGDAN);
        }

		$Act135Model = Master::getAct135($this->uid);
		$Act135Model->add($fuid);

		$Act135Model = Master::getAct135($fuid);
		$Act135Model->add($this->uid);

		$FriendChat = Master::getFriendChat($this->uid);
		foreach($Act135Model->info as $k => $v){
			$FriendChat->listReset($this->uid,$k);
			$FriendChat->listCheck($this->uid,$k,$fuid);
		}
		$FriendChat->back_data_au($this->uid);
	}

	/**
	 * 刷新聊天信息
	 * ['fuid']: 对象
	 */
	public function frchat($params){
		$Act135Model = Master::getAct135($this->uid);
		$FriendChat = Master::getFriendChat($this->uid);
		foreach($Act135Model->info as $k => $v){
			$FriendChat->listReset($this->uid,$k);
			$FriendChat->listCheck($this->uid,$k,$fuid);
		}
		$FriendChat->back_data_au($this->uid);
	}

	/**
	 * 发送私聊
	 * ['fuid']: 对象
	 * ['msg']:  语句
	 */
	public function fschat($params){
		$fuid = $params['fuid'];
		$msg = Game::strval($params,"msg");
		//是否合服范围内
		Game::isHeServerUid($fuid);
		//敏感字符判定
		$msg = Game::str_feifa($msg,1);
		$msg = Game::str_mingan($msg,1);

		//我加入语句
		$FriendChat = Master::getFriendChat($this->uid);
		$FriendChat-> addMsg($this->uid, $msg ,$fuid);
		$FriendChat->listCheck($this->uid,$fuid);
		$FriendChat->back_data_au($this->uid,true);

		$FriendChat = Master::getFriendChat($fuid);
		$FriendChat->listCheck($fuid,$this->uid);
		$FriendChat->back_data_au($fuid,true);
	}

	/**
	 * 私聊列表 - 删除私聊玩家
	 */
	public function fssub($params){

		$fuid = $params['fuid'];
		//是否合服范围内
		Game::isHeServerUid($fuid);
		$Act135Model = Master::getAct135($this->uid);
		$Act135Model->sub($fuid);

		$FriendChat = Master::getFriendChat($this->uid);
		foreach($Act135Model->info as $k => $v){
			$FriendChat->listReset($this->uid,$k);
			$FriendChat->listCheck($this->uid,$k);
		}
		$FriendChat->back_data_au($this->uid);
	}

	/**
	 * 私聊列表 - 删除私聊玩家
	 */
	public function fhistory($params){

		$fuid = $params['fuid'];
		//是否合服范围内
		Game::isHeServerUid($fuid);

		$id = $params['id'];
		$FriendChat = Master::getFriendChat($this->uid);
		$FriendChat->listHistory($this->uid, $fuid, $id);
	}

	/**
	 * 推荐列表
	 */
	public function rlist() {

		$servid = Game::get_sevid($this->uid);
		$db = Common::getDbBySevId($servid);
		$openIdList = HoutaiModel::queryData($servid);
		shuffle($openIdList);
		$i = 0;
		// 黑名单列表
		$Act97Model = Master::getAct97($this->uid);
		$hmd = $Act97Model->info['list'];
		// 申请列表
		$Act131Model = Master::getAct131($this->uid);
		$sq = $Act131Model->info['list'];
		// 好友列表
		$Act130Model = Master::getAct130($this->uid);
		$hy = $Act130Model->info['list'];
		$data = array();
		$Act132Model = Master::getAct132($this->uid);

		$Redis1Model = Master::getRedis1();
        $score = $Redis1Model->zScore($this->uid);
        $maxScore = intval($score * 1.25);
        $minScore = intval($score * 0.75);

		foreach($openIdList as $value) {
			$uid = $this->queryUserId($db,$value['openid']);
			$uScore = $Redis1Model->zScore($uid);

			if (!$uid || $uid == $this->uid || isset($hmd[$uid]) || key_exists($uid,$sq) || key_exists($uid,$hy) || $maxScore < $uScore || $minScore > $uScore) {
				continue;
			}

			$i++;
			$friendInfo = Master::getFriendInfo($uid);
			$friendInfo["isApply"] = 0;
			if (key_exists($uid,$Act132Model->info)) {
				$friendInfo["isApply"] = 1;
			}
			$data[] = $friendInfo;
			if ($i > 4) {
				break;
			}
		}

		if (count($data) <= 0) {
			foreach($openIdList as $value) {
				$uid = $this->queryUserId($db,$value['openid']);
				$uScore = $Redis1Model->zScore($uid);

				if (!$uid || $uid == $this->uid || isset($hmd[$uid]) || key_exists($uid,$sq) || key_exists($uid,$hy) || $uScore < 2000) {
					continue;
				}

				$i++;
				$friendInfo = Master::getFriendInfo($uid);
				$friendInfo["isApply"] = 0;
				if (key_exists($uid,$Act132Model->info)) {
					$friendInfo["isApply"] = 1;
				}
				$data[] = $friendInfo;
				if ($i > 4) {
					break;
				}
			}
		}

		Master::back_data($this->uid,'recommend','list',$data);
	}

	public function search($params) {

		$fuid = (int)$params['fuid'];
		$servid = Game::get_sevid($fuid);
		$db = Common::getDbBySevId($servid);
		if (!$this->queryUser($db,$fuid)) {
			Master::error(USER_ACCOUNT_NO_EXIT);
		}

		$data = Master::getFriendInfo($fuid);
		$data["isApply"] = 0;
		$Act132Model = Master::getAct132($this->uid);
		if (key_exists($fuid,$Act132Model->info)) {
			$data["isApply"] = 1;
		}

		Master::back_data($this->uid,'search','list',$data);
	}

	/**
	 * @param $db Db
	 * @param $ustr
	 * @return int
	 */
	public function queryUserId($db,$ustr) {
		$sql = "select `uid` from `gm_sharding` where `ustr` = '{$ustr}' limit 1";
		$result = $db->fetchArray($sql);
		if (!$result) {
			return 0;
		}
		$uid = $result[0]['uid'];
		return (int)$uid;
	}

	public function queryUser($db,$userId) {
		$sql = "select `uid` from `gm_sharding` where `uid` = '{$userId}' limit 1";
		$result = $db->fetchArray($sql);
		if (!$result) {
			return 0;
		}
		$uid = $result[0]['uid'];
		return (int)$uid;
	}

	public function blacklist() {
		// 黑名单列表
		$Act97Model = Master::getAct97($this->uid);
		$Act97Model->back_data();
	}

	/*
	 * 加入黑名单
	 * params buid 要拉黑名单的buid
	 * */
	public function addblacklist($params) {
		$buid = Game::intval($params,"fuid");

		$Act97Model = Master::getAct97($this->uid);
		$Act97Model->add($buid);

		//好友两边都删除
		$Act130Model = Master::getAct130($this->uid);
		$Act130Model->sub($buid);
		$Act130Model = Master::getAct130($buid);
		$Act130Model->sub($this->uid);
	}

	/*
	 * 移除黑名单
	 * params buid 移除黑名单的buid
	 * */
	public function subblacklist($params) {

		$buid = Game::intval($params,"fuid");
		$Act97Model = Master::getAct97($this->uid);
		$Act97Model->sub($buid);
	}

	/*
	 * 送礼
	 * params buid 移除黑名单的buid
	 * */
	public function sendGift($params) {

		$buid = Game::intval($params,"fuid");
		$Act130Model = Master::getAct130($this->uid);
		if ( !isset($Act130Model->info['list'][$buid]) ) {
			Master::error(FRIEND_NEED_ADD);
		}

		$Act8023Model = Master::getAct8023($this->uid);
		$sendList = empty($Act8023Model->info["send"]) ? array() : $Act8023Model->info["send"];
		if (count($sendList) > 0 && in_array($buid, $sendList)) {
			Master::error(FRIEND_IS_SEND);
		}

		if (count($sendList) >= 5) {
			Master::error(FRIEND_SEND_IS_MAX);
		}

		$Act8023Model->sendFriendGift($buid);
		$FriendModel = Master::getFriend($this->uid);
        $FriendModel->add_love($buid, 1);

        $FriendModel = Master::getFriend($buid);
        $FriendModel->add_love($this->uid, 1, false);

		$UserModel = Master::getUser($this->uid);
		$title = FRIEND_SEND_GIFT;
        $content = FRIEND_CONTENT_1.$UserModel->info['name'].FRIEND_CONTENT_2;
        $mailModel = Master::getMail($buid);
        $mailModel->sendMail($buid, $title, $content, 1, array(array('id'=> 3,'count'=> 10000)));
        $cache1 = Common::getCacheByUid($buid);
        $key = $buid.'_mail';
        $cache1->delete($key);

        $Act130Model->back_data();
	}
}