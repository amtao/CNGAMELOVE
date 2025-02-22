<?php include(TPL_DIR.'fun/tool.php');?>

数组排序:<br/><br/>
<table>
<tr>
<td>
代码格式<br/>
  <form method="post">
	<textarea style="width: 500px;height: 399px;" name="value" ></textarea>
	<p><input type="submit" value="提交" /></p>
 </form>
</td>

<td>
JSON格式<br/>
 <form method="post">
	<textarea style="width: 500px;height: 399px;" name="json_value" ></textarea>
	<p><input type="submit" value="提交" /></p>
 </form>
</td>

</tr>
</table>

 

<p/>

输出<br/>
<textarea style="width: 600px;height: 399px;">
<?php 
var_export($data);
?>
</textarea>


<?php include(TPL_DIR.'footer.php');?>
