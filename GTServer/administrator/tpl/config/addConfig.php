<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<div class="header">
    <?php if($_GET['type'] == 'baseConfig' || $_GET['type'] == 'allConfig') {?>
        <a class='backGroundColor' <?php if ($_GET['type'] == 'baseConfig'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=baseConfig&uid=<?php echo $uid;?>" >基础配置</a>
        <?php if(SERVER_ID == 999 || SERVER_ID == 1){?>
            <a class='backGroundColor' <?php if ($_GET['type'] == 'allConfig'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=allConfig&uid=<?php echo $uid;?>">通服基础配置</a>
            <a class='backGroundColor' <?php if ($_GET['type'] == 'actNewConfig'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=actNewConfig">新服活动</a>    
        <?php }?>
    <?php }else{?>
        <a class='backGroundColor' <?php if ($_GET['type'] == 'actBaseConfig'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=actBaseConfig" >单服活动配置</a>
        <?php if(SERVER_ID == 999 || SERVER_ID == 1){?>
            <a class='backGroundColor' <?php if ($_GET['type'] == 'actAllConfig'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=actAllConfig">通服活动配置</a>
            <a class='backGroundColor' <?php if ($_GET['type'] == 'actNewConfig'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=actNewConfig">新服活动</a>
        <?php }?>
    <?php }?>
</div>
<hr class="hr"/>
<form name="form1" id="form1" method="post" action="">

<input type='hidden' id='type' name='type' value="<?php echo $row['type'];?>" />

<p>key: <input type="text" id="key" name="key" value="<?php echo $row['key'];?>"   /></p>

<p>value: <p>
<p>
<textarea id="value" name="value" cols="120" rows="30"><?php echo $row['value'] ;?></textarea>
</p>
  
<p><input type="submit" value="提交" /></p>
</form>

<?php include(TPL_DIR.'footer.php');?>
