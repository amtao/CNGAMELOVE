<?php include(TPL_DIR.'fun/tool.php');?>
php转EXCELL:<br/><br/>

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
$text = '';
foreach ($data as $k => $v){
	if (is_array($v)){
		foreach ($v as $k2 =>$v2){
			if (is_array($v2)){
				foreach ($v2 as $k3 =>$v3){
					if (is_array($v3)){
						foreach ($v3 as $k4 => $v4){
							$text.=$v4.'	';
						}
					}else{
						$text.=$v3.'	';
					}
				}
			}else{
				$text.=$v2.'	';
			}
		}
	}else{
		$text.=$v.'	';
	}
	$text.="\n";
}
?>

<textarea cols="100" rows="10">
<?php 
echo $text;
?>
</textarea>


<?php include(TPL_DIR.'footer.php');?>
