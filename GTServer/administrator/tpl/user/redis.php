<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>
<h2>redis数据</h2>

<style type="text/css">
.wife_div {
    float:left;
	border: medium double buttonface;
    margin: 2px;
    padding: 3px;
}
</style>
<?php echo $uid;?>
<br>
<?php 
var_export($info);
?>
<div class="wife_div">
<form action="" method="post">
<input name="add_wife_key" value="" />
<br/>
<textarea rows="8" cols="30" name="add_info"></textarea>
<br/>
<input type="submit" value="新增">
</form></div>


<div class="wife_div" style="clear:both">
<?php include(TPL_DIR.'footer.php');?>
</div>
