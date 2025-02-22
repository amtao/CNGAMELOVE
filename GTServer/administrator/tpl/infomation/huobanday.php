<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form id="form-search" method="POST" action="" >
    <table style="width: 100%">
        <tr><th colspan="6">伙伴拥有数量统计</th></tr>
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
        <tr>
        <th style="text-align:right;width: 100px;">查询类型：</th>
        <td style="text-align:left;" colspan="3">
            <input type="radio" name="actid" value="6140"<?php if(empty($_POST['actid']) || $_POST['actid'] == 6140) echo "checked";?>/> 服装
            <input type="radio" name="actid" value="8"<?php if($_POST['actid'] == 8) echo "checked";?>/> 伙伴
            <input type="radio" name="actid" value="14"<?php if($_POST['actid'] == 14) echo "checked";?>/> 知己
        </td>
    </tr>
        <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
    </table>
</form>

<hr class="hr" />
<?php
?>
<table>
    <?php if($list):?>
        <tr>
            <?php foreach ($keyList as $key => $value): ?>
                <th><?php echo $key;?></th>
            <?php endforeach;?>
        </tr>
        <?php foreach ($list as $key => $value): ?>
        <tr>
            <?php foreach ($value as $k => $v): ?>
                <td style="text-align:center;"><?php echo $v;?></td>
            <?php endforeach;?>
        </tr>
        <?php endforeach;?>
    <?php endif;?>
</table>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
