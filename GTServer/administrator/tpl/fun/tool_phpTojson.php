<?php include(TPL_DIR.'fun/tool.php');?>


php转JSON:<br/><br/>

<table>
<tr>

<td>
php代码<br/>
  <form method="post">
	<textarea name="value" cols="100" rows="10"><?php echo $value;?></textarea>
	<p><input type="submit" value="提交" /></p>
 </form>
</td>

<td>

</tr>
</table>

<p/>

输出<br/>

<?php 
$data = eval("return ".$value.";");


$data = arr_to_json($data);
//$data = (object)$data;
$str = json_encode($data);
?>

<textarea cols="100" rows="10">
<?php 
echo $str;
?>
</textarea>
<br/>
汉字转码
<br/>

<textarea cols="100" rows="10">
<?php 
$str=preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
echo $str;
?>
</textarea>



<?php include(TPL_DIR.'footer.php');?>
