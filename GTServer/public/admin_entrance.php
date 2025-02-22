
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
    table th {background-color:#c6e4fe; border:#DAB6A3 solid 1px;}
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
<h1>后台管理登录</h1>
</head>
<body>
    <form name="form_login" id="form_login" method="post" action="admin_entrance_ctl.php">
    用户名:<br>
    <input type="text" name="username" value=""><br>
    密码:<br>
    <input type="password" name="password" value="">
    <p>
    <input type="submit" name="login" value="登录"/>
    <!-- <input type="submit" name="updpwd" value="修改密码"/> -->
    <input type="submit" name="register" value="注册"/>
    </p>
    </form>
</body>


<hr class="hr"/>
当前服务器时间:<?php echo date("Y-m-d H:i:s");?><br />
时间戳: <?php echo  time();?><br />
</body>
</html>
