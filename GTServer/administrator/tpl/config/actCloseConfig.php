<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<div class="header">
    <a class='backGroundColor' <?php if ($_GET['act'] == 'actBaseConfig'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=actBaseConfig" >单服活动配置</a>
    <?php if(SERVER_ID == 999 || SERVER_ID == 1){?>
    <a class='backGroundColor' <?php if ($_GET['act'] == 'actAllConfig'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=actAllConfig">通服活动配置</a>
    <a class='backGroundColor' <?php if ($_GET['type'] == 'actNewConfig'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=actNewConfig">新服活动</a>
    <a class='backGroundColor' <?php if ($_GET['type'] == 'actCloseConfig'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=actCloseConfig">关闭活动</a>
    <?php }?>
</div>
<hr class="hr"/>
<table style="text-align: center;" class="mytable">
<tr><th></th><th>关闭活动</th><th></th></tr>
<tr><th>key</th><th>value</th><th>operate</th></tr>
<?php
if(!empty($data) && is_array($data))
{
    foreach ($data as $key => $value)
    {
        $url = "<a class='backGroundColor' href='?sevid={$SevidCfg['sevid']}&mod=config&act=addConfig&type=actCloseConfig&key={$key}'>{$key}</a>";
        $delete = '';
        $delete = "<a class='backGroundColor' onclick='return confirm(\"您确定要删除吗(Are you sure?)\");' href='?sevid={$SevidCfg['sevid']}&mod=config&act=delConfig&type=actCloseConfig&key={$key}'>删除</a>";
        echo "<tr><td>{$url}</td><td>".htmlspecialchars(substr($value,0,80))."</td><td>$delete</td></tr>";
    }
}
$addKey = '添加活动配置';
$url = "<a class='backGroundColor' href='?sevid={$SevidCfg['sevid']}&mod=config&act=addConfig&type=actCloseConfig&key='>{$addKey}</a>";
$delete = '';
echo "<tr><td colspan='3' style='padding: 10px;'>{$url}</td></tr>";
?>
</table>
<br />

<?php include(TPL_DIR.'footer.php');?>