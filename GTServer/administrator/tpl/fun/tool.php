<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<div class="header">
	<a class='backGroundColor' <?php if ($_GET['act'] == 'tool_jsonToArray'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=fun&act=tool_jsonToArray&cur=tool" >json转换</a>
	<a class='backGroundColor' <?php if ($_GET['act'] == 'tool_timeToDate'){echo 'style="color:red;';} ?>href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=fun&act=tool_timeToDate&cur=tool">time转换</a>
	<a class='backGroundColor' <?php if ($_GET['act'] == 'tool_phpTojson'){echo 'style="color:red;';} ?>href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=fun&act=tool_phpTojson&cur=tool">php转json</a>
	<a class='backGroundColor' <?php if ($_GET['act'] == 'tool_phpToexcl'){echo 'style="color:red;';} ?>href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=fun&act=tool_phpToexcl&cur=tool">php转excl</a>
	<a class='backGroundColor' <?php if ($_GET['act'] == 'tool_paixu'){echo 'style="color:red;';} ?>href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=fun&act=tool_paixu&cur=tool">json排序</a>
</div>
<hr class="hr"/>