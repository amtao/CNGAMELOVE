<?php include(TPL_DIR.'header.php');?>
<?php include(TPL_DIR.'zi_header.php');?>
<br/>
<hr class="hr"/>
<h2>UString</h2>
<form name="form1" method="post" action="">
<p>区服：
<select name="serverid" id="serverid">
     <option value="0" <?php if($_POST['serverid'] == 0) echo "selected";?>>全服</option>
     <?php foreach ($serverList as $key => $value):?>
         <option value="<?php echo $key; ?>" <?php if($_POST['serverid'] == $key) echo "selected";?>><?php echo $value['id'].'区'.$value['name']['zh']; ?></option>
     <?php endforeach;?>
</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
UString: <input type="text" name="ustring" value="<?php echo $_POST['ustring'];?>"  />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="查找" /></p>
</form>
<br />

<hr class="hr"/>
<h2>UString - UID</h2>
<form name="form1" method="post" action="">
<p>区服：
<select name="serverid" id="serverid">
     <option value="0" <?php if($_POST['serverid'] == 0) echo "selected";?>>全服</option>
     <?php foreach ($serverList as $key => $value):?>
         <option value="<?php echo $key; ?>" <?php if($_POST['serverid'] == $key) echo "selected";?>><?php echo $value['id'].'区'.$value['name']['zh']; ?></option>
     <?php endforeach;?>
</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Uid: <input type="text" name="uid" value="<?php echo $_POST['uid'];?>"  />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="查找" /></p>
</form>
<br />

<hr class="hr"/>
<h2>Name - UID</h2>
<form name="form1" method="post" action="">
<p>区服：
<select name="serverid" id="serverid">
     <option value="0" <?php if($_POST['serverid'] == 0) echo "selected";?>>全服</option>
     <?php foreach ($serverList as $key => $value):?>
         <option value="<?php echo $key; ?>" <?php if($_POST['serverid'] == $key) echo "selected";?>><?php echo $value['id'].'区'.$value['name']['zh']; ?></option>
     <?php endforeach;?>
</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
UName: <input type="text" name="uname" value="<?php echo $_POST['uname'];?>"  />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="查找" /></p>
</form>
<br />

<hr class="hr"/>
<h2>openid - serid</h2>
<form name="form1" method="post" action="">
	<p>openid: <input type="text" name="openid" value="<?php echo $_POST['openid'];?>"  />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="查找" /></p>
</form>
<br />

<hr class="hr"/>
<h2>滚服uid</h2>
<form name="form1" method="post" action="">
    <p>uid: <input type="text" name="rollUid" value="<?php echo $_POST['rollUid'];?>"  />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="查找" /></p>
</form>
<br />

<?php
if( !empty($row) && is_array($row) ) {
    echo '<hr class="hr" />查询结果:
<div style="color: red;">';
    echo '<pre>';
    print_r($row);
    echo '</div>';
}else if ($_POST){
    echo '<hr class="hr" />查询结果:
<div style="color: red;">未找到信息!请重新核对查找信息是否正确!';
    echo '</div>';
}
echo "<br/><br/>";
?>
<?php include(TPL_DIR.'footer.php');?>