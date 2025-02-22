<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr" />
<h2>sev查询</h2>
<div style="color:#F00"><?php echo $msg;?></div>
<form name="form1" method="post" action="">
<p>Key: <input type="text" style="width:400px;" name="key" value="<?php echo $_POST['key'];?>"  /></p>
<p>hcid: <input type="text" style="width:400px;" name="hcid" value="<?php echo $_POST['hcid'];?>"  /></p>
<p>did: <input type="text" style="width:400px;" name="did" value="<?php echo $_POST['did'];?>"  /></p>
<p><input type="submit" class="input" name="submit" value="查询" style="padding: "/>
</p>
</form>
<hr/>
<form name="form2" method="post" action="">
<input type="text" style="width:400px;display: none;"  name="key" value="<?php echo $_POST['key'];?>"  />
<input type="text" style="width:400px;display: none;"  name="hcid" value="<?php echo $_POST['hcid'];?>"  />
<input type="text" style="width:400px;display: none;"  name="did" value="<?php echo $_POST['did'];?>"  />
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
<?php include(TPL_DIR . 'footer.php');?>