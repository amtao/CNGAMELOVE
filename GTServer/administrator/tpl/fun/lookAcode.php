<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php'); if(!empty($code) && empty($info)){echo "<script>alert('没有这个兑换码!');</script>";};?>
<hr class="hr"/>
<form name="form" method="POST" action="" >
<table>
    <tr><th colspan="2">兑换码查询 </th></tr>
    <tr>
        <th style="text-align:right;">code：</th>
        <td style="text-align:left;">
            <input type="text" name="code" value="<?php echo $code;?>" />
        </td>
    </tr>
	 <tr><th colspan="2"><input type="submit" value="确定查询" /></th></tr>
</table>
</form>
<BR>

<hr class="hr" />
<table>
    <tr>
        <th colspan="2">兑换码查询 </th>
    </tr>
    <tr>
        <th>玩家UID</th>
        <th>创建时间</th>
        <th>使用时间</th>
    </tr>
    <?php if(!empty($info)){ ?>
        <?php foreach ($info as $key => $value): ?>
            <tr>
                <td style="text-align:center;"><?php echo $value['uid']?$value['uid']:'未领取';?></td>
                <td style="text-align:center;"><?php echo date('Y-m-d H:i:s', $value["ctime"]);?></td>
                <td style="text-align:center;"><?php echo $value['uid']?date('Y-m-d H:i:s', $value["utime"]):'';?></td>
            </tr>
        <?php endforeach;?>
    <?php } ?>
</table>
<br>
<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
