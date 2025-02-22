<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>


</form>
</div>
    <div style="width: 400px;display: inline-block;float: left;">
        <p>道具添加:</p>
        <form action="" method="post">
            <table style="width: 300px;">
                <tr>
                    <td style="text-align: center;background-color: #B0CFED;">卡牌名称</td>
                    <td style="text-align: center;background-color: #B0CFED;">操作</td>
                </tr>
                <tr>
                    <td>
                        <select name="add_item_key">
                            <?php unset($all_item[1],$all_item[2],$all_item[3],$all_item[4],$all_item[5]); ?>
                            <?php foreach ($all_item as $key=>$value){
                                echo '<option value="'.$key.'">'.$key.'-'.$value.'</option>';
                            }?>
                        </select>
                    </td>
                    <td><input type="submit" value="新增"/></td>
                </tr>
            </table>
        </form></div>

<div style="width: 400px;display: inline-block;">
    <p>玩家卡牌信息:</p>
<form method="post" action="">
<input type="hidden" value="<?php echo $uid;?>" name="uid" id="uid" />
<input type="hidden" value="1" name="add_change" id="add_change" />
<table style="width: 400px;margin: 5px 0px;float: center">
	<tr>
		<td style="text-align: center;background-color: #B0CFED;">id</td>
		<td style="text-align: center;background-color: #B0CFED;">名称</td>
        <td style="text-align: center;background-color: #B0CFED;">等级</td>
        <td style="text-align: center;background-color: #B0CFED;">星级</td>
        <td style="text-align: center;background-color: #B0CFED;">数量</td>
        <td style="text-align: center;background-color: #B0CFED;">品质</td>
		<td style="text-align: center;background-color: #B0CFED;">编辑</td>
	</tr>
	<?php if(!empty($daoju)){ foreach ($daoju as $k => $v){?>
	<tr>
		<td style="text-align: center;"><?php echo $k;?></td>
		<td style="text-align: center;"><?php echo $all_item[$k];?></td>
        <td style="text-align: center;"><?php echo $v["level"];?></td>
        <td style="text-align: center;"><?php echo $v["star"];?></td>
        <td style="text-align: center;"><?php echo $v["count"];?></td>
        <td style="text-align: center;"><?php echo $v["quality"];?></td>
		<td>  <input type="text" value="" name="<?php echo $k;?>" id="<?php echo $k;?>"></td>
	</tr>
	<?php } }?>
	<tr>
		<td colspan="4" style="text-align: center;"><input type="submit" value="提交" /></td>
	</tr>
</table>

<div style="width: 400px;display: inline-block;">
    <p>玩家卡牌拥有个数:</p>
<form method="post" action="">
<table style="width: 400px;margin: 5px 0px;">
	<tr>
        <td style="text-align: center;background-color: #B0CFED;">等级</td>
		<td style="text-align: center;background-color: #B0CFED;">数量</td>
	</tr>
	<?php if(!empty($daojuCount)){ foreach ($daojuCount as $k => $v){?>
	<tr>
        <td style="text-align: center;"><?php echo $k;?></td>
        <td style="text-align: center;"><?php echo $v;?></td>
	</tr>
	<?php } }?>
</table>

<?php include(TPL_DIR.'footer.php');?>