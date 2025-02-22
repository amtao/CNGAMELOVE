<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form id="form-search" method="POST" action="" >
    <table style="width: 100%">
        <tr><th colspan="6">活跃用户统计</th></tr>
        <tr>
            <th style="text-align: right;">区服:(全服all,区间如1-2)</th>
            <td style="text-align:left;">
                <input type="text" name="server" value="<?php echo $_POST['server']?$_POST['server']:'all'?>">
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
        <th>区服</th>
        <th>人数</th>
    </tr>
    <?php foreach ($userList as $key => $value): ?>
    <tr>
        <td style="text-align:center;"><?php echo $value['sevid'];?></td>
        <td style="text-align:center;"><?php echo $value['count'];?></td>
    </tr>
    <?php endforeach;?>
</table>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
