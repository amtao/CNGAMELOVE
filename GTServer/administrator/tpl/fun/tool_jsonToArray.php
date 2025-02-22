<?php include(TPL_DIR.'fun/tool.php');?>
<form name="form1" method="post" action="">
<p>json: <textarea name="json" cols="70" rows="5" id="json"><?php echo $json;?></textarea></p>
<p><input type="submit" value="提交" /></p>
</form>

<textarea cols="120" rows="15"><?php var_export($array);?></textarea>

<?php include(TPL_DIR.'footer.php');?>
