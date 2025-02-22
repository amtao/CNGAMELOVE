<?php
$auth = $_SESSION['USER_POWER_LIST'];
if(!$auth){
    if($_SESSION["CURRENT_USER"] != 'wyadmin') {
        echo "<script>alert('请让管理员设置权限');</script>";
        header('HTTP/1.1 404 Not Found'); exit();
    }
}


//ps: 因为有权限限制  所以key值 不可随意更改  
$SevidCfg = Common::getSevidCfg();
$links = array(
		1 => array( 'title'   =>  '账号管理' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=user&act=index' ),
        2 => array( 'title'   =>  '数据管理' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=index' ),
		3 => array( 'title'   =>  '充值查询' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=data&act=index' ),
		4 => array( 'title'   =>  '活动配置' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=config&act=index' ),
        5 => array( 'title'   =>  '功能管理' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=fun&act=tool' ),
		6 => array( 'title'   =>  '服务器管理' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=servers&act=slist' ),
		7 => array( 'title'   =>  '排行数据' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=paihang&act=index' ),
        8 => array( 'title'   =>  '邮件管理' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=mail&act=index' ),
        9 => array( 'title'   =>  '权限管理' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=auth&act=index' ),
        10 => array( 'title'   =>  '埋点需求' , 'src'  =>  '?sevid='.$SevidCfg['sevid'].'&mod=buryingport&act=index' ),
);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理</title>
<style type="text/css">
    *{ font-size:12px;}
    .header a{ margin-right:8px; border:#bda2a2 1px solid; border-radius:0.2em; line-height:25px; padding:1px 6px 1px 6px;margin-top:5px;margin-bottom:4px;}
    .header a{text-decoration:none;}
    .header a:link {color: black;}
    .header a:visited {color: black;}
    .header a:hover {color:#F00;}
    .header a:active {color: #F00}
    .mytable a{ margin-right:8px; border:#bda2a2 1px solid; border-radius:0.2em; line-height:25px; padding:1px 6px 1px 6px;margin-top:5px;margin-bottom:4px;}
    .mytable a{text-decoration:none;}
    .mytable a:link {color: black;}
    .mytable a:visited {color: black;}
    .mytable a:hover {color:#F00;}
    .mytable a:active {color: #F00}
    .hr {color:#f9f0f0;margin: 10px 0px;}
    table{width:70%;border-collapse:collapse; line-height:20px;}
    table caption {background-color:#c6e4fe; border:#A49898 solid 0.5px;border-bottom: none; font-size: 16px;}
    <?php
    if (isset($_GET['auditType']) && $_GET['auditType']==1){
        echo 'table th {background-color:#ffc18166; border:#DAB6A3 solid 1px;}';
    }else{
        echo 'table th {background-color:#c6e4fe; border:#DAB6A3 solid 1px;}';
    }
        ?>
    table td {border:#DAB6A3 solid 1px; padding-left:5px;}
    .aColor{border-color: #92799a;background-color: #fbd4c2;}
    .deleteColor{border-color: #92799a;background-color: #fcb9b4;}
	.input {margin:1px 3px;}
    .backGroundColor{background-color: #f5edea;}
    .page a{width:20px; line-height:20px; display:block; float:left; background:#B5B5FF; margin:2px; padding-left:10px;}
	.page b{width:20px; line-height:20px; display:block; float:left;  margin:2px;padding-left:10px;background:#CCC}
</style>
<script type="text/javascript" src="/js/jquery.js"></script>
    <script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/common.inc.js"></script>
<script type="text/javascript" src="/js/highcharts.js"></script>
<script type="text/javascript" src="/js/modules/exporting.js"></script>
<script type="text/javascript" src="/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="/js/layer/layer.js"></script>
<link rel="stylesheet" type="text/css" href="/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
</head>
<body>
<div>
	<?php 
	echo sprintf('<span>【缓存前缀】：%s</span>&nbsp;', MEMCACHED_PREFIX_KEY), PHP_EOL;
	echo sprintf('<span style="color: red">【%s】：%s 区</span>&nbsp;', AGENT_CHANNEL_NAME, $SevidCfg['sevid']), PHP_EOL;
	?>
</div>
<hr class="hr" />
<?php

$account = require( CONFIG_ADM_DIR . "/auth_config.php" );
$adminAuth = array('wyadmin');
echo '<div class=\'mytable\'>';
foreach ($links as $k => $lk) {
	//有配置权限
	if(!empty($auth)){
		//第一级目录为空 过滤
		if(empty($auth['ml'][$k])){
			continue;
		}
	}
    if ($k == 9){
        if (!in_array($_SESSION["CURRENT_USER"], $adminAuth)){
            continue;
        }
    }
    if($_GET['mod'] == substr($lk['src'], strpos($lk['src'], '&mod=')+5, strpos($lk['src'], '&act')-strpos($lk['src'], '&mod=')-5)){
        $sty = 'style=\'color:#F00;\'';
    }else{
        $sty='';
    }
    echo "<a href='{$lk['src']}'  {$sty} style='background-color: #f5edea;' class='backGroundColor'>{$lk['title']}</a>";
}
echo '</div>';
?>
<hr class="hr"/>
<script>
    $(document).ready(function(){
        layer.config({
            offset: '300px',
        })
    });
</script>