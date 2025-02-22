<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form method="post" action="">
    <p>群号: <input type="text" name="groupno" value="<?php echo $groupNo;?>"></p>
    <p><input type="submit" value="修改" /></p>
</form>