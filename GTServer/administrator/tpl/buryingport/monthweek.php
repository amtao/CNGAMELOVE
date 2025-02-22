<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 10;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form name="rechargeSearch" method="POST" action=""  onsubmit="return checkForm();">
<table style='width: 100%;'>
<tr><th colspan="6">周卡月卡购买</th></tr>
<tr>
<th>日期范围</th>
<td>
<input class='Wdate' id='keyword5' type='text'  name='beginDate' value='<?php echo $_POST['beginDate']; ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00',isShowClear:true,readOnly:true, maxDate:'<?php echo date('Y-m-d');?>'})" />
<font> 至 </font>
<input class='Wdate' id='keyword6' type='text' name='endDate' value='<?php echo $_POST['endDate']; ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59',isShowClear:true,readOnly:true, minDate:'1900-01-01', maxDate:'%y-%M-%d'})" />
</td>
</tr>
<tr>
<th style='text-align: center;width: 100px;'>区服</th>
<td style='text-align:left;'>
    <select name="servid" id="servid">
        <option value="0">全服</option>
        <?php foreach ($serverList as $key => $value):?>
            <option value="<?php echo $key; ?>" <?php if($_POST['servid'] == $key) echo "selected";?>><?php echo $value['id'].'区'.$value['name']['zh']; ?></option>
        <?php endforeach;?>
    </select>
</td>
</tr>
<tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
</table>
</form>

<?php if($dataList):?>

<table style="width: 100%" id="tableId">
    <thead>
    <tr>
        <th>id</th>
        <th>人数</th>
        <th>人次</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($dataList as $key => $value){
        echo '<td style="text-align:center;">'.$value['id'].'</td>';
        echo '<td style="text-align:center;">'.$value['count'].'</td>';
        echo '<td style="text-align:center;">'.$value['totalCount'].'</td>';
        echo '</tr>';
    } ?>
    </tbody>
</table>
<BR>

<?php else:?>
<BR>
<?php endif;?>
<?php include(TPL_DIR.'footer.php');?>