<?php
class Servers{
	
	
	/**
	 * 服务器列表
	 */
	public function slist(){
		//返回服务器列表
		
		$statusCfg = array (
			5 => '新服',
			1 => '正常',
			2 => '拥挤',
			3 => '爆满',
			4 => '排队',
			6 => '维护',// 不是以上五个值的默认为维护
		);
	
	
		Common::loadModel('ServerModel');
		$sList = ServerModel::getServList();
        $sList = array_reverse($sList,true);
        $nowtime =strtotime(date('Y-m-d 23:00:00',$_SERVER['REQUEST_TIME']));
        Common::loadModel('HoutaiModel');
        foreach ($sList as $k=>$v){
            if ($v['showtime']<$_SERVER['REQUEST_TIME']){
                $cache 	= Common::getCacheBySevId($k);
                $listNow = $cache->get('huodong_list_'.$k);
                if(!empty($listNow)){
                    foreach ($listNow as $d){
                        if ($d['showTime']<$nowtime){
                            continue ;
                        }
                        $diff = $d['showTime'] - $nowtime;
                        if($diff < 86400){
                            $sList[$k]['hdend'] = 1;
                        }
                    }
                }

            }
        }

		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}


	/**
	 * 添加 / 保存   服务器列表
	 */
	public function addslist(){
		Common::loadModel('ServerModel');
		if($_REQUEST['save'] == 'save'){
            $id = trim($_REQUEST['id']);
            $url = trim($_REQUEST['url']);
            $zhname = trim($_REQUEST['zhname']);
            $status = trim($_REQUEST['status']);
            $showtime = trim($_REQUEST['showtime']);
            $skin = intval($_REQUEST['skin']);
			if(empty($id)|| !is_numeric($id) || empty($url) || empty($zhname)|| empty($status)|| empty($showtime) ){
				echo "<script>alert('小姐姐,参数错误了!');</script>";
			}else{
				$adddata = array(
					'id' => $id,
					'url' => $url,
					'name' => array(
						'zh' => $zhname,
					),
					'status' => $status,
                    'skin' => $skin,
					'showtime' => strtotime($showtime.':00:00'),
				);
				ServerModel::addServList($adddata);
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, $adddata);
				echo "<script>alert('保存成功!');</script>";
			}
		}
		$statusCfg = array (
			5 => '新服',
			1 => '正常',
			2 => '拥挤',
			3 => '爆满',
			4 => '排队',
			6 => '维护',// 不是以上五个值的默认为维护
		);
		//返回服务器列表
		
		$sList = ServerModel::getServList();
		$data = array();
		if(!empty($sList)){
			foreach($sList as $k => $v){
				if($k == 999){
					continue;
				}
				$data['id'] = $v['id'] +1;
				$data['url'] = $v['url'];
				$data['status'] = $v['status'];
                $data['skin'] = $v['skin'];
				$data['name'] = array();
			}
		}
		if(empty($data)){
			$data = array(
				'id' => 1,
				'url' => '',
				'name' => array(
					'zh' => '',
				),
				'status' => 0,
                'skin' => 1,
				'showtime' => 0,
			);
		}
		
		if(!empty($_REQUEST['key'])){
			$data = $sList[$_REQUEST['key']];
		}
		
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	
	/**
	 * 删除   服务器列表
	 */
	public function delslist(){
        if (empty($_REQUEST['delkey']) || !is_numeric($_REQUEST['delkey'])){
            return;
        }
		//返回服务器列表
		Common::loadModel('ServerModel');
		ServerModel::delServList($_REQUEST['delkey']);
        //后台操作日志
        Common::loadModel('AdminModel');
        AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('delslist' => $_REQUEST['delkey']));
        
		$statusCfg = array (
			5 => '新服',
			1 => '正常',
			2 => '拥挤',
			3 => '爆满',
			4 => '排队',
			6 => '维护',// 不是以上五个值的默认为维护
		);
	
		Common::loadModel('ServerModel');
		$sList = ServerModel::getServList();
		if(!empty($sList)){
			krsort($sList);
		}
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/slist.php';
	}

	/*
	 * 查看 服务器状态
	 */

	public function listStatus(){
        Common::loadModel('ServerModel');
        $sList = ServerModel::getServList();
        $sList = array_reverse($sList,true);
        foreach ($sList as $k => $v){
            if($k == 999) continue;
            $sList[$k]['mem'] = self::checkMem($k);
            $sList[$k]['redis'] = self::checkRedis($k);
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    private function checkMem($sevid){
	    $mes = "错误";
        $mem = Common::getCacheBySevId($sevid);
        $mem->set('serverStatus','正常');
        $data = $mem->get('serverStatus');
        if($data !== false){
            $mes = $data;
        }
        return $mes;
    }

    private function checkRedis($sevid){
        $mes = "错误";
        $redis = Common::getRedisBySevId($sevid);
        $redis->set('serverStatus','正常');
        $data = $redis->get('serverStatus');
        if($data !== false){
            $mes = $data;
        }
        return $mes;
    }
}
