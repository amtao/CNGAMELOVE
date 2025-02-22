<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form id="form-search" method="POST" action="" >
    <table style="width: 100%">
        <tr><th colspan="6">用户丢失统计</th></tr>
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
                       value='<?php echo (empty($startTime)) ? date('Y-m-d 00:00:00') : date('Y-m-d H:i:s', $startTime);?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true})" />
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
        <th>VIP</th>
        <th>人数</th>
    </tr>
    <?php foreach ($userList as $key => $value): ?>
    <tr>
        <td style="text-align:center;"><?php echo $value['vip'];?></td>
        <td style="text-align:center;"><?php echo $value['count'];?></td>
    </tr>
    <?php endforeach;?>
</table>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
