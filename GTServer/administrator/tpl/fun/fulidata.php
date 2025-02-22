<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form id="form-search" method="POST" action="" >
    <table style="width: 100%">
        <tr><th colspan="6">充值福利查询</th></tr>
        <tr>
            <th style="text-align:right;width: 50px;">区服：</th>
            <td style="text-align:left;">
                <input type="text" name="serverid" value="<?php echo $_POST['serverid']; ?>">
            </td>
            <th style="text-align:right;width: 50px;">角色id：</th>
            <td style="text-align:left;">
                <input type="text" name="roleid" value="<?php echo $_POST['roleid']; ?>">
            </td>
            <th style="text-align:right;width: 50px;">时间：</th>
            <td style="text-align:left;">
                <input class='Wdate' type='text' size='40' id='startTime' name='startTime'
                       value='<?php echo (empty($startTime)) ? date('Y-m-d 00:00:00') : date('Y-m-d H:i:s', $startTime);?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						maxDate:'#F{$dp.$D(\'endTime\')}'})" />
                &nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;

                <input class='Wdate' type='text' size='40' id='endTime' name='endTime'
                       value='<?php echo (empty($endTime)) ? date('Y-m-d 23:59:59') : date('Y-m-d 23:59:59', $endTime);?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						minDate:'#F{$dp.$D(\'startTime\')}'})" />
            </td>
            </td>
        </tr>
        <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
    </table>
</form>
<hr class="hr" />
<?php
?>
<table>
    <tr>
        <th>序号</th>
        <th>游戏订单号</th>
        <th>账号</th>
        <th>角色ID</th>
        <th>平台标识</th>
        <th>充值金额(元)</th>
        <th>兑换游戏币</th>
        <th>支付时间</th>
    </tr>
    <?php foreach ($all as $key => $value): ?>
    <tr>
        <td style="text-align:center;"><?php echo $key+1;?></td>
        <td style="text-align:center;"><?php echo $value['orderid'];?></td>
        <td style="text-align:center;"><?php echo $adminName[$value['tradeno']]['name'];?></td>
        <td style="text-align:center;"><?php echo $value["roleid"];?></td>
        <td style="text-align:center;"><?php echo $value["platform"];?></td>
        <td style="text-align:center;"><?php echo $value["money"];?></td>
        <td style="text-align:center;"><?php echo $value["diamond"];?></td>
        <td style="text-align:center;"><?php echo date('Y-m-d H:i:s',$value["ptime"]);?></td>
    </tr>
    <?php endforeach;?>
</table>
<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
