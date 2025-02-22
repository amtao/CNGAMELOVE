<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
    <hr class="hr"/>
    <div class="header">
        <a class='backGroundColor' <?php if ($_GET['act'] == 'baseConfig'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=baseConfig&uid=<?php echo $uid;?>" >基础配置</a>
        <?php if(SERVER_ID == 999 || SERVER_ID == 1){?>
            <a class='backGroundColor' <?php if ($_GET['act'] == 'allConfig'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=allConfig&uid=<?php echo $uid;?>">通服基础配置</a>
        <?php }?>
    </div>
<hr class="hr"/>
<table style="text-align: center;" class="mytable">
<tr><th></th><th>新服配置</th><th></th></tr>
<tr><th>key</th><th>value</th><th>operate</th></tr>
<?php
if(!empty($data) && is_array($data))
{
    foreach ($data as $key => $value)
    {
        $url = "<a class='backGroundColor' href='?sevid={$SevidCfg['sevid']}&mod=config&act=addConfig&type=allConfig&key={$key}'>{$key}</a>";
        $delete = '';
        $delete = "<a class='backGroundColor' onclick='return confirm(\"您确定要删除吗(Are you sure?)\");' href='?sevid={$SevidCfg['sevid']}&mod=config&act=delConfig&type=allConfig&key={$key}'>删除</a>";
        echo "<tr><td>{$url}</td><td>".htmlspecialchars(substr($value,0,80))."</td><td>$delete</td></tr>";
    }
}
$addKey = '添加基础配置';
$url = "<a class='backGroundColor' href='?sevid={$SevidCfg['sevid']}&mod=config&act=addConfig&type=allConfig&key='>{$addKey}</a>";
$delete = '';
echo "<tr><td colspan='3' style='padding: 10px;'>{$url}</td></tr>";
?>
</table>
<br />

<?php include(TPL_DIR.'footer.php');?>