<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr" />
<form name="form1" id="form1" method="post" action="">
Uid:<input type="text" name="uid" value="<?php echo $uid;?>"  />&nbsp;
<input type="submit" value="查看" name="show" />
</form>
<?php if(!empty($uid)){?>
<form name="hero" id="hero" method="post" action="">
<input type="hidden" value="<?php echo $uid;?>" name="uid" id="uid" />
<table style="width: 550px;margin-top:10px;line-height:30px;font-size: 13px;">
    	<tr><th colspan="3">伙伴升级</th></tr>
    	<tr>
    		<td style="text-align:center;">伙伴:
    		  <select name="hero" id="hero">
        	     <?php foreach ($hero as $key => $value):?>
        			 <option value="<?php echo $key; ?>" <?php if($_POST['hero'] == $key) echo "selected";?>><?php echo $value['name'].'('.$value['level'].')'; ?></option>
        		 <?php endforeach;?>
	           </select>	
	         </td>
	         <td style="text-align:center;">
    		   <input type="text" value='' name="level" placeholder="这里填写要升级到的等级" onkeyup="this.value=this.value.replace(/[^0-9-]+/,'');">
	         </td>
    		<td style="text-align:center;">
    		<input type="submit" value="升级"/>
    		</td>
    	</tr>
</table>
</form>

<form name="guanka" id="guanka" method="post" action="">
<input type="hidden" value="<?php echo $uid;?>" name="uid" id="uid" />
<table style="width: 550px;margin-top:10px;line-height:30px;font-size: 13px;">
    	<tr><th colspan="3">关卡通关</th></tr>
    	<tr>
    		<td style="text-align:center;">
    		    <?php echo '大关:'.$userInfo['bmap'].'  小关:'.$userInfo['smap'];?>
	         </td>
	         <td style="text-align:center;">
    		   <input type="text" value='' name="guanka" placeholder="这里填写要达到的小关关卡数" onkeyup="this.value=this.value.replace(/[^0-9-]+/,'');">
	         </td>
    		<td style="text-align:center;">
    		<input type="submit" value="通关"/>
    		</td>
    	</tr>
</table>
</form>



<?php }?>

<?php include(TPL_DIR.'footer.php');?>
<script>
    $(document).ready(function () {
        $(':input[type="submit"]').on('click',function () {
            $(this).hide();
        });
    });
</script>