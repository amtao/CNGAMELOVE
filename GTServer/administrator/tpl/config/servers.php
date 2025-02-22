
<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'publiccfg/header.php');?>


<table class="mytable">
<tr><th></th><th>服务器列表</th></tr>
<tr><th>key</th><th>value</th></tr>
<?php
if(!empty($data) && is_array($data))
{
    foreach ($data as $key => $value)
    {
        $url = "<a href='?sevid={$SevidCfg['sevid']}&mod=publicCfg&act=addcfg&type=servers&key={$key}'>{$key}</a>";
        $delete = '';
        echo "<tr><td>{$url}</td><td>".htmlspecialchars(substr(json_encode($value,JSON_UNESCAPED_UNICODE),0,80))."</td></tr>";
    }
}
?>
</table>
<br />

<?php include(TPL_DIR.'footer.php');?>