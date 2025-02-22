<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form id="form-search" method="POST" action="" >
    <table style="width: 100%">
        <tr><th colspan="6">服装拥有数量统计</th></tr>
        <tr>
            <th style="text-align:right;width: 50px;">区服：</th>
            <td style="text-align:left;">
                <select name="serverid" id="serverid">
                     <option value="0" <?php if($_POST['serverid'] == 0) echo "selected";?>>全服</option>
                     <?php foreach ($serverList as $key => $value):?>
                         <option value="<?php echo $key; ?>" <?php if($_POST['serverid'] == $key) echo "selected";?>><?php echo $value['id'].'区'.$value['name']['zh']; ?></option>
                     <?php endforeach;?>
                </select>
            </td>
            <th style="text-align:right;width: 50px;">时间：</th>
            <td style="text-align:left;">
                <input class='Wdate' type='text' size='40' id='startTime' name='startTime'
                       value='<?php echo (empty($startTime)) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $startTime);?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						maxDate:'#F{$dp.$D(\'endTime\')}'})" />
                &nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;

                <input class='Wdate' type='text' size='40' id='endTime' name='endTime'
                       value='<?php echo (empty($endTime)) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $endTime);?>'
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
        <th>服装ID</th>
        <th>服装名称</th>
        <th>服装数量</th>
    </tr>
    <?php foreach ($clothesList as $key => $value): ?>
    <tr>
        <td style="text-align:center;"><?php echo $value['id'];?></td>
        <td style="text-align:center;"><?php echo $value['name'];?></td>
        <td style="text-align:center;"><?php echo $value["count"];?></td>
    </tr>
    <?php endforeach;?>
</table>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
