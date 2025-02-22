<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
    <div style="display: inline-block;width: 200px; "><a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=showMemcache&cur=showCache" <?php if ($_GET['act'] == 'showMemcache') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;" class='backGroundColor' >Memcache缓存</a></div>
    <div style="padding: 0 20px;display: inline-block;float:left;width: 200px; "><a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=showRedis&cur=showCache" <?php if ($_GET['act'] == 'showRedis') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;" class='backGroundColor'>Redis缓存</a></div>
    <div style="padding: 0 20px;display: inline-block;float:left;width: 200px; "><a class='backGroundColor' href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=sevInfo&cur=showCache" <?php if ($_GET['act'] == 'sevInfo') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;">sev信息</a></div>
<hr class="hr" />
<h2>Memcache查询</h2>
<div style="color:#F00"><?php echo $msg;?></div>
<form name="form1" method="post" action="">
<p>Key: <input type="text" style="width:400px;" name="key" value="<?php echo $_POST['key'];?>"  /></p>
<p>hcid: <input type="text" style="width:400px;" name="hcid" value="<?php echo $_POST['hcid'];?>"  /></p>
<p>did: <input type="text" style="width:400px;" name="did" value="<?php echo $_POST['did'];?>"  /></p>
<p><input type="submit" class="input" name="submit" value="查询" style="padding: "/>
</p>
</form>
<hr/>
<h2>Memcache修改</h2>
<form name="form2" method="post" action="">
<input class="input" type="hidden" name="jsontype" value="1"  />
Key:<input class="input" style="width:400px;" type="text" name="key" value="<?php echo $_POST['key'];?>"  /><br/>
<textarea id="json_data" rows="6" cols="100" style="margin: 5px 0px; " name="json_data">
<?php echo json_encode($data);?>
</textarea><br/>
<input type="submit" name="submit" value="提交" />
</form>
<br/>
<?php
if(!empty($_POST['key']))
{
    echo '<hr/><div style="color: red;">查询结果:</div>';
    echo '<pre>';
    var_export($data);
    echo '</pre>';
}
?>

<br/>
<?php include(TPL_DIR.'footer.php');?>