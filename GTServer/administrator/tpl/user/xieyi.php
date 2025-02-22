<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>

<br>
<br>
<form method="post" action="">

<select id='modids' name='modids'>
		<?php 
		foreach ($cfg_modid as $k => $v) {
			if ($modcheck == $k){
				echo " <option value= ".$v['ctl'].'__'.$k." selected='selected' >".$v['msg']."	接口: ".$k."</option>";
			}else{
				echo " <option value= ".$v['ctl'].'__'.$k."   >".$v['msg']."	接口: ".$k."</option>";
			}
		}
		?>
</select>
<br>
<br>
接口数据:
<br>
<br>
例如:<br>
array (<br>
  'openkey' => 'qwqweqwe',<br>
  'platform' => 'local',<br>
)
<br>
<br>
<textarea rows="8" cols="30" name="post_data"><?php var_export($post_data);?></textarea>


<input type="submit" value="提交" />

</form>


<pre>
<?php var_export($rdata);?>
</pre>


<?php include(TPL_DIR.'footer.php');?>