<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>


</form>
</div>
    <div style="width: 400px;display: inline-block;float: left;">
        <p>服装拥有信息:</p>
        <form action="" method="post">
            <table style="width: 300px;">
                <tr>
                    <td style="text-align: center;background-color: #B0CFED;">服装id</td>
                    <td style="text-align: center;background-color: #B0CFED;">服装名称</td>
                    <td style="text-align: center;background-color: #B0CFED;">部位</td>
                </tr>
                <?php if(!empty($clothesInfo)){ foreach ($clothesInfo as $k => $v){?>
                <tr>
                    <td style="text-align: center;"><?php echo $v["clotheId"];?></td>
                    <td style="text-align: center;"><?php echo $v["name"];?></td>
                    <td style="text-align: center;"><?php echo $v["part"];?></td>
                </tr>
                <?php } }?>
            </table>
        </form></div>

<div style="width: 400px;display: inline-block;float: left;">
<p>服装保存信息:</p>
<form action="" method="post">
    <table style="width: 300px;">
        <tr>
            <td style="text-align: center;background-color: #B0CFED;">日期</td>
            <td style="text-align: center;background-color: #B0CFED;">点击次数</td>
        </tr>
        <?php if(!empty($touchInfo)){ foreach ($touchInfo as $date => $count){?>
        <tr>
            <td style="text-align: center;"><?php echo  $date;?></td>
            <td style="text-align: center;"><?php echo  $count;?></td>
        </tr>
        <?php } }?>
    </table>
</form></div>

<div style="width: 400px;display: inline-block;float: center;">
    <p>服装拥有个数:</p>
<form method="post" action="">
<table style="width: 400px;margin: 5px 0px;">
	<tr>
        <td style="text-align: center;background-color: #B0CFED;">部位</td>
		<td style="text-align: center;background-color: #B0CFED;">数量</td>
	</tr>
	<?php if(!empty($partInfo)){ foreach ($partInfo as $k => $v){?>
	<tr>
        <td style="text-align: center;"><?php echo $k;?></td>
        <td style="text-align: center;"><?php echo $v;?></td>
	</tr>
	<?php } }?>
</table>

<div style="width: 400px;display: inline-block;float: center;">
    <p>服装套装信息:</p>
<form method="post" action="">
<input type="hidden" value="<?php echo $uid;?>" name="uid" id="uid" />
<table style="width: 400px;margin: 5px 0px;float: center">
	<tr>
        <td style="text-align: center;background-color: #B0CFED;">套装id</td>
        <td style="text-align: center;background-color: #B0CFED;">套装名称</td>
		<td style="text-align: center;background-color: #B0CFED;">套装等级</td>
	</tr>
	<?php if(!empty($suitInfo)){ foreach ($suitInfo as $k => $v){?>
	<tr>
        <td style="text-align: center;"><?php echo $k;?></td>
        <td style="text-align: center;"><?php echo $v['name'];?></td>
		<td style="text-align: center;"><?php echo $v['lv'];?></td>
	</tr>
    <?php } }?>
    <th style="text-align:center;">总计</th>
    <td style="text-align:center;"><?php echo $total; ?></td>
</table>

<BR>

<?php include(TPL_DIR.'footer.php');?>