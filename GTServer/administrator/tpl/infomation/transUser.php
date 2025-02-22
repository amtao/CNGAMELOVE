<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
    <div style="display: inline-block;width: 200px; " class="mytable"><a class='backGroundColor' href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=jinyan&cur=transUser" <?php if ($_GET['act'] == 'jinyan') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;">禁言列表</a></div>
    <div style="padding: 0 20px;display: inline-block;float:left;width: 200px; " class="mytable"><a class='backGroundColor' href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=fenghao&cur=transUser" <?php if ($_GET['act'] == 'fenghao') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;">封号列表</a></div>
    <div style="padding: 0 20px;display: inline-block;float:left;width: 200px; " class="mytable"><a class='backGroundColor' href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=fengsb&cur=transUser" <?php if ($_GET['act'] == 'fengsb') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;">封设备列表</a></div>
    <div style="display: inline-block;width: 200px; " class="mytable"><a class='backGroundColor' href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=jinyankua&cur=transUser" <?php if ($_GET['act'] == 'jinyankua') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;">跨服禁言列表</a></div>
<?php include(TPL_DIR.'footer.php');?>