<?php
class Config
{
	/**
     * 主页
     * */
    public function index(){
        $SevidCfg = Common::getSevidCfg($_GET['sevid']);
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
	public function common(){
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    //基础配置
    public function baseConfig()
    {
        Common::loadModel('HoutaiModel');
        $data = HoutaiModel::read_base_peizhi();
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    //通服基础配置
    public function allConfig()
    {
        Common::loadModel('HoutaiModel');
        $data = HoutaiModel::read_all_peizhi();
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    
    //单服活动配置
	public function actBaseConfig()
	{
		Common::loadModel('HoutaiModel');
		$data = HoutaiModel::read_base_hd();
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	//通服活动配置
	public function actAllConfig()
	{
		Common::loadModel('HoutaiModel');
		$data = HoutaiModel::read_all_hd();
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	//新服活动配置
	public function actNewConfig()
	{
		Common::loadModel('HoutaiModel');
		$data = HoutaiModel::read_new_hd();
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	
	//服务器列表
	public function servers()
	{
		Common::loadModel('HoutaiModel');
		$data['serverList'] = HoutaiModel::read_servers();
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	
	//   基础配置     通服基础配置     单区活动配置     通服活动配置    新服活动配置   添加 / 修改
	public function addConfig()
	{
		Common::loadModel('HoutaiModel');
        //添加修改
		if(!empty($_REQUEST['key']) && !empty($_REQUEST['value']) ){
		    if($_REQUEST['key'] == 'add999'){
		        echo "<script>alert('key错误');</script>";
		    }
		    $value = @eval('return ' . $_REQUEST['value'] . ';');
		    if(!is_array($value)){
		        echo "<script>alert('格式有问题');</script>";
		    }else{
    			switch($_REQUEST['type']){
    				case 'actBaseConfig':
    					HoutaiModel::write_base_hd($_REQUEST['key'],$_POST['value']);
    					break;
    				case 'actAllConfig':
    					HoutaiModel::write_all_hd($_REQUEST['key'],$_POST['value']);
    					break;
    				case 'actNewConfig':
    					HoutaiModel::write_new_hd($_REQUEST['key'],$_POST['value']);
    					break;
    				case 'baseConfig':
    					HoutaiModel::write_base_peizhi($_REQUEST['key'],$_POST['value']);
    					break;
    				case 'allConfig':
    					HoutaiModel::write_all_peizhi($_REQUEST['key'],$_POST['value']);
    					break;
                    case 'ggConfig':
                        HoutaiModel::write_googgao_peizhi($_REQUEST['key'],$value);
    				case 'servers':
    					$_POST['value'] = json_decode($_POST['value'],true);
    					HoutaiModel::write_servers($_POST['value']);
    					$data['serverList'] = HoutaiModel::read_servers();
    					break;		
    				default :
    					echo "<script>alert('type_err');</script>";
    					break;
        			
                }
                //后台操作日志
                Common::loadModel('AdminModel');
                AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($_REQUEST['type'] => array($_REQUEST['key'] => $_POST['value'])));
                echo "<script>alert('提交成功!');</script>";
                $function = $_REQUEST['type'];
		    }
		}
		
		
		//页面展示
		switch($_REQUEST['type']){
			case 'actBaseConfig':
				$data = HoutaiModel::read_base_hd();
				break;
			case 'actAllConfig':
				$data = HoutaiModel::read_all_hd();
				break;
			case 'actNewConfig':
				$data = HoutaiModel::read_new_hd();
				break;
			case 'baseConfig':
				$data = HoutaiModel::read_base_peizhi();
				break;
			case 'allConfig':
				$data = HoutaiModel::read_all_peizhi();
				break;	
			case 'servers':
				$slist = HoutaiModel::read_servers();
				$data['serverList'] = json_encode($slist,JSON_UNESCAPED_UNICODE);
				break;	
			default :
				echo "<script>alert('页面type_err');</script>";
				break;
		}
		
		$row['type'] = $_REQUEST['type'];
		$row['key'] = $_REQUEST['key'];
		$row['value'] = $data[$_REQUEST['key']];
		$function = empty($function) ? __FUNCTION__ : $function;
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.$function.'.php';
	}
	
	//基础配置     通服基础配置     单区活动配置     通服活动配置    删除
	public function delConfig()
	{
		Common::loadModel('HoutaiModel');

		switch($_REQUEST['type']){
			case 'actBaseConfig':
				HoutaiModel::del_base_hd($_REQUEST['key']);
				$data = HoutaiModel::read_base_hd();
				break;
			case 'actAllConfig':
				HoutaiModel::del_all_hd($_REQUEST['key']);
				$data = HoutaiModel::read_all_hd();
				break;
			case 'actNewConfig':
				HoutaiModel::del_all_hd($_REQUEST['key']);
				$data = HoutaiModel::read_new_hd();
				break;
			case 'baseConfig':
				HoutaiModel::del_base_peizhi($_REQUEST['key']);
				$data = HoutaiModel::read_base_peizhi();
				break;
			case 'allConfig':
				HoutaiModel::del_all_peizhi($_REQUEST['key']);
				$data = HoutaiModel::read_all_peizhi();
				break;	
			default :
				echo "<script>alert('type_err');</script>";
				break;
		}
        //后台操作日志
        Common::loadModel('AdminModel');
        AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($_REQUEST['type'] => trim($_REQUEST['key'])));
		echo "<script>alert('删除成功!');</script>";
		
		
		$row['type'] = $_REQUEST['type'];
		$row['key'] = $_REQUEST['key'];
		$row['value'] = $data[$_REQUEST['key']];
		include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.$_REQUEST['type'].'.php';
	}
    /*
     * 生效列表
     * */
	public function effectivelist(){
	    $serid = $_GET['sevid'];
	    $key = 'huodong_list_'.$serid;
	    $cache 	= Common::getCacheBySevId($serid);
	    $list = $cache->get($key);
	    
	    include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
	}
	/*
	 * 格式化添加公告配置
	 */
	public function gsConfig(){
	    $key_d = empty($_REQUEST['key'])?$_POST['key']:$_REQUEST['key'];
        Common::loadModel('HoutaiModel');
	    if($_POST['type'] == 'edit'){
            $body = array();
            $body_s = '';
	        //构造详情数组
	        foreach ($_POST as $k => $v){
	            if(strstr($k,'body')){//查询body 字段
	                $key = explode('_',$k);
	               if($key['2'] == 'color'){
	                   $body[$key[1]][$key[2]][$key[3]] = $v;
                   }else{
                       $body[$key[1]][$key[2]] = $v;
                   }
                }
            }
            foreach ($body as $k => $v){
	            $body_s.=  "{\"{$v['word']}\\n \\n\",{$v['size']},cc.c3b({$v['color'][0]},{$v['color'][1]},{$v['color'][2]})},\n";
            }
            $string = <<<EOL
        //公告
array(
  array(
     'header' => 'local ee ={[1]={"{$_POST['header_word']}",{$_POST['header_size']},cc.c3b({$_POST['header_color_0']},{$_POST['header_color_1']},{$_POST['header_color_2']})}} return ee',
     'title' => 'local ee ={[1]={"{$_POST['title_word']}",{$_POST['title_size']},cc.c3b({$_POST['title_color_0']},{$_POST['title_color_1']},{$_POST['title_color_2']})}} return ee',
     'body' => 'local ee ={
	 $body_s
	}return ee',
  ),
);
EOL;
            HoutaiModel::write_all_peizhi($key_d,$string);
        }
        $data = HoutaiModel::read_all_peizhi($key_d);
        $data = @eval('return '.$data.';');
        $rule = '/\".*?(\\n \\n)?\"\,[0-9]+\,[0-9a-z]+\.[0-9a-z]+\([0-9]+,[0-9]+,[0-9]+\)/';
        preg_match($rule,$data[0]['header'],$header);
        preg_match($rule,$data[0]['title'],$title);
        preg_match_all($rule,$data[0]['body'],$body);
        $header = $this->geshihua($header[0]);
        $title = $this->geshihua($title[0]);
        foreach ($body[0] as $k => $v){
            $body[$k] = $this->geshihua($v,'body');
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }

    public function geshihua($data,$type = 'default'){
	    $return = array();
	    $string_d = explode(',',$data);
	    $return['word'] = explode('"',$string_d[0]);
	    $return['word'] = $return['word'][1];
	    if($type != 'default'){$return['word'] = substr($return['word'],0,-5);}
	    $return['size'] = $string_d[1];
	    $string_d2 = explode("(",$string_d[2]);
        $return['font'] = $string_d2[0];
        $return['color'][2]= substr($string_d[4],0,-1);
        $return['color'][1] = $string_d[3];
        $return['color'][0] = $string_d2[1] ;
        return $return;
    }
    /*
     * 公告内容
     */
    public function ggConfig(){
        Common::loadModel('NoticeModel');
        $data = NoticeModel::noticeData();
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /*
     * 添加公告内容
     */
    public function addGg(){
        Common::loadModel('NoticeModel');
        if($_POST){
            $data = NoticeModel::noticeData();
            $data[$_POST['id']] = array(
                'header' => $_POST['header'],
                'title' => $_POST['title'],
                'body' => $_POST['body'],
                'top' => $_POST['top'],
            );
            NoticeModel::addNotice($data);
            NoticeModel::noticeVer();
            $this->ggConfig();
			//后台操作日志
			Common::loadModel('AdminModel');
			AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, $data);
            exit();
        }
        if(isset($_GET['key'])){
            $show = NoticeModel::noticeData();
            $show = $show[$_GET['key']];
            $show['key'] = $_GET['key'];
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }


    /*
     * 字符转换
     */
    public function dotran($str) {
        $str = str_replace('"','\\"',$str);
        return $str;
    }
    /*
     * 删除公告内容
     */
    public function delgg(){
        if(isset($_GET['delkey'])){
            Common::loadModel('NoticeModel');
            NoticeModel::delNotice($_GET['delkey']);
            NoticeModel::noticeVer();
			//后台操作日志
			Common::loadModel('AdminModel');
			AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('delete' => $_GET['delkey']));
            $this->ggConfig();
        }
    }
    /*
     * 公告配置
     */
    public function ggConfig2(){
        Common::loadModel('NoticeModel');
        $data = NoticeModel::noticeConfig();
        //平台
        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();
        $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');

        if (!empty($auth['qd']['pt'])) {
            if (!empty($platformList)) {
                foreach ($platformList as $key => $pl) {
                    if (!in_array($key, $auth['qd']['pt'])) {
                        unset($platformList[$key]);
                    }
                }
            }
        }else{
            $platformList['local'] = "后台";
        }
        $channels = array();
        if (!empty($platformList)) {
            foreach ($platformList as $k => $pl) {
                $channels[] = $k;
            }
        }
        if (isset($_GET['del'])) {
            unset($data[$_GET['key']]);
            NoticeModel::addNoticeConfig($data);
            $location = "location.href='?sevid={$_GET['sevid']}&mod=config&act=ggConfig2';";
			//后台操作日志
			Common::loadModel('AdminModel');
			AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('delete' => $_GET['del']));
            echo "<script>alert('删除成功！');{$location}</script>";
        }
        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /*
     * 添加/更新公告配置
     */
    public function addggConfig(){
        Common::loadModel('NoticeModel');

        //获取配置
        Common::loadVoComModel('ComVoComModel');
        //公告内容
        $ComVoComModel = new ComVoComModel('notice');
        $content = $ComVoComModel->getValue();
        if($_POST){
            if(!empty($_POST['include'])){
                $include = array_unique($_POST['include']);
                $include = implode(',',$include);
            }
            if(!empty($_POST['exclusive'])){
                $exclusive = array_unique($_POST['exclusive']);
                $exclusive = implode(',',$exclusive);
            }
            $data = array(
                'sTime' => $_POST['sTime'],
                'eTime' => $_POST['eTime'],
                'serv' => $_POST['serv'],
                'include' => $include,
                'exclusive' => $exclusive,
                'top' => $_POST['top'],
                'header' => $_POST['header'],
                'title' => $_POST['title'],
                'body' => $_POST['body'],
            );

            $config_data = NoticeModel::noticeConfig();
            if($_POST['updata_key'] === ''){
                array_push($config_data,$data);
            }else{
                $config_data[$_POST['updata_key']] = $data;
            }
            NoticeModel::addNoticeConfig($config_data);
            NoticeModel::noticeVer();

            if($_POST['updata_key'] === '') {
                $location = "location.href='?sevid={$_GET['sevid']}&mod=config&act=ggConfig2';";
                echo "<script>alert('添加成功！');{$location}</script>";
            }
			//后台操作日志
			Common::loadModel('AdminModel');
			AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, $config_data);
        }
        $key = '';
        if(isset($_GET['key'])){
            $key = $_GET['key'];
            $config_data = NoticeModel::noticeConfig();
            $data = $config_data[$key];
        }
        //平台
        Common::loadModel('OrderModel');
        $platformList = OrderModel::get_platform();
        $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');

        if (!empty($auth['qd']['pt'])) {
            if (!empty($platformList)) {
                foreach ($platformList as $key => $pl) {
                    if (!in_array($key, $auth['qd']['pt'])) {
                        unset($platformList[$key]);
                    }
                }
            }
        }else{
            $platformList['local'] = "后台";
        }
        $channels = array();
        if (!empty($platformList)) {
            foreach ($platformList as $k => $pl) {
                $channels[] = $k;
            }
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }
    /*
     * 删除公告配置
     */
    public function delggConfig(){
        Common::loadModel('NoticeModel');
        $data = NoticeModel::noticeConfig();
        NoticeModel::addNoticeConfig($data);
        $this->addggConfig($_GET['update_key']);
        //后台操作日志
        Common::loadModel('AdminModel');
        AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array($_GET['update_key']));
    }

    public function getGG(){
        $id = $_POST['id'];
        Common::loadModel('NoticeModel');

        //获取配置
        Common::loadVoComModel('ComVoComModel');
        //公告内容
        $ComVoComModel = new ComVoComModel('notice');
        $content = $ComVoComModel->getValue();
        echo json_encode($content[$id],JSON_UNESCAPED_UNICODE);
    }

    /*
     * 客服跑马灯
     */
    public function kefupmd(){

        $Sev93Model = Master::getSev93();

        if(!empty($Sev93Model->info)){
            ksort($Sev93Model->info);
            end($Sev93Model->info);
            $now_id = key($Sev93Model->info);//把指针指向最后一个key
        }

        $data = array(
            'pmdno' => empty($now_id)?1:$now_id+1,  //编号
            'pmdserv' => 'all', //生效服务器
            'sTime' => 0,  //播放开始时间
            'eTime' => 0,  //播放结束时间
            'time' => 1,   //播放间隔分钟  默认1分钟
            'num' => 1,   //播放次数 默认一次
            'pmdef' => 1, //播放使用特效
            'msg' => '',   //语句 【通知】
        );

        if($_POST){

            $data = array(
                'pmdno' => $_POST['pmdno'],  //编号
                'pmdserv' => $_POST['pmdserv'], //生效服务器
                'sTime' => $_POST['sTime'],  //播放开始时间
                'eTime' => $_POST['eTime'],  //播放结束时间
                'time' => $_POST['time'],   //播放间隔分钟  默认1分钟
                'num' => $_POST['num'],   //播放次数 默认一次
                'pmdef' => $_POST['pmdef'], //播放使用特效
                'msg' => $_POST['msg'],   //语句
            );

            //存储1区
            $Sev93Model->add_msg($data);

            $location = "location.href='?sevid={$_GET['sevid']}&mod=config&act=kefupmd';";
            echo "<script>alert('添加成功！');{$location}</script>";
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, $data);
        }

        if(isset($_GET['updatekey'])){
            $key = $_GET['updatekey'];
            $data = empty($Sev93Model->info[$key])?array():$Sev93Model->info[$key];
        }

        if(isset($_GET['delkey'])){
            $Sev93Model->del_msg($_GET['delkey']);
            //后台操作日志
            Common::loadModel('AdminModel');
            AdminModel::admin_log($_SESSION['CURRENT_USER'], __CLASS__, __FUNCTION__, array('del' => $_GET['delkey']));
        }

        include TPL_DIR . str_replace('controller','',strtolower(__CLASS__)).'/'.__FUNCTION__.'.php';
    }


}
