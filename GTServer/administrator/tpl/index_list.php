<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>后台管理</title>
    <?php
    $static_uri = 'http://tanklw.tuziyouxi.com/';
    ?>
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.4.3.min.js"></script>
    <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.migrate/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery.cookie/1.4.1/jquery.cookie.min.js"></script>
    <link rel="stylesheet" href="https://ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/bootstrap.min.js"></script>
    <style type="text/css">
        *{ font-size:12px;}
        .header a{ margin-right:7px; border:#080808 1px solid;  line-height:25px; padding:3px;color: #666}
        .header a.ahover{ border:#F00 1px solid; color: #F00}
        a{text-decoration:none;}
        a:visited {color: #666}
        a:hover { color:#F00;}
        a:active {color: #F00}
        table{width:70%;border-collapse:collapse; line-height:20px;}
        table th { background-color:#B5B5FF;border:#06F solid 1px;}
        table td {  border:#080808 solid 1px; padding-left:5px;}

        .page a{ width:20px; line-height:20px; display:block; float:left; background:#B5B5FF; margin:2px; padding-left:10px;}
        .page b{width:20px; line-height:20px; display:block; float:left;  margin:2px;padding-left:10px;background:#CCC}
        .mytable{ margin-top:5px;margin-bottom:5px;}
        .mytable a{ border:#CCC 1px dotted; padding:2px; margin-right:5px;}
    </style>

</head>
<body>
<?php
// 显示切换语言
$langCfg = Common::getConfigAdmin('lang_cfg');
$liHtml = '';
// 服务器列表菜单
$defSrc = '';
Common::loadModel('ServerModel');
$defServerId = ServerModel::getDefaultServerId();
$links = array();
$servers= array();
foreach($serversList as $k => $v){
    if ( empty($v) || (0 && 0 < strpos($_SESSION['CURRENT_USER'], '_')) ) {
        continue;
    }
    $url = sprintf('http://%s/admin.php?sevid=%s&%s', $v['url'], $v['id'], http_build_query($_REQUEST));
    if ($v['id'] == 999){
        continue;
    }
    if ( empty($defSrc) && $defServerId == $v['id'] ) {
        $defSrc = $url;
    }
    $key = floor(($v['id']-1)/10);
    $servers[$key][] = array('server'=>$v['id'],'title' => $v['id'] . '服' . $v['name']['zh'], 'src' => $url);
}
$get = isset($_GET['user']) ? "&user=".$_GET['user'] : "";
echo '<div class="header" id="header_id">';
foreach ($servers as $k =>$v){
    if ($k ||$k==0) {
        $end = end($servers[$k]);
        echo '<div class="btn-group" style="margin:0px 0px 0px 5px;" data-id="'.$k.'" >
<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . $servers[$k][0]['server'].'  区 - '.$end['server'].' 区   <span class="caret"></span>
</button><ul class="dropdown-menu" style="position: absolute;top:88%;" >';
        foreach ($servers[$k] as $serkey => $serval ){
            echo '<li><a href="'.$serval["src"].$get.'" target="showframe">'.$serval["title"].'</a></li>';
        }
        echo '</ul></div>';
    }
}
echo '</div>';
?>
<hr style="margin:0px 0px;border-color:black;"/>
<div style="-webkit-overflow-scrolling: touch;overflow-y: scroll;height:1200px;">
    <iframe src="" name="showframe" width="100%" id="showframe" frameborder=0 style="height:1200px"></iframe>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.btn-group').on('mouseover',function () {
            $(this).addClass('open');
            $(this).prop('aria-expanded',true);
        });
        $('.btn-group').on('mouseout',function () {
            $(this).removeClass('open');
            $(this).prop('aria-expanded',false);
        });
    });
    function SetWinHeight(obj) {
        var test=obj;
        if (test && !window.opera)
        {
            if (test.contentDocument && test.contentDocument.body.offsetHeight)
                test.height = test.contentDocument.body.offsetHeight;

            else if(test.Document && test.Document.body.scrollHeight)
                test.height = test.Document.body.scrollHeight;
        }
    }
    $(function(){
        $("#header_id").find("a").click(function(){
            $("#header_id").find("a").removeClass("ahover");
            $(this).addClass("ahover");
            SetWinHeight(document.getElementsByName("showframe"));
        });
        var url=$("ul li a").first().prop("href");
        document.getElementById('showframe').src=url;
    });

</script>
</body>
</html>
