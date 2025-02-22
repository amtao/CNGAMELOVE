<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
    <div style="display: inline-block;width: 180px; " class="mytable"><a class='backGroundColor' href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=showMemcache&cur=showCache" <?php if ($_GET['act'] == 'showMemcache') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;">Memcache缓存</a></div>
    <div style="padding: 0 20px;display: inline-block;float:left;width: 200px; " class="mytable"><a class='backGroundColor' href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=showRedis&cur=showCache" <?php if ($_GET['act'] == 'showRedis') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;">Redis缓存</a></div>
<?php include(TPL_DIR.'footer.php');?>