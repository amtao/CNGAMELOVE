<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form method="post">
    <table style='width: 100%;'>
        <tbody>
        <tr>
            <th style="text-align: right;">区服:</th>
            <td style="text-align: left;">
                <select name="server">
<?php foreach($serverlist as $k => $v){?>
                    <option <?php if ($_POST['server'] != "all" && $sevid == $v['id']) { echo 'selected="selected"'; } ?> value="<?php echo $v['id'];?>"><?php echo $v['name']['zh'];?></option>
<?php } ?>
                    <option value="all" <?php if ($_POST['server'] == "all") { echo 'selected="selected"'; } ?>>全部</option>
                </select>
            </td>
        </tr>
        <tr>
        <th style="text-align:right;width: 100px;">统计类型：</th>
        <td style="text-align:left;" colspan="3">
            <input type="radio" name="stype" value="1"<?php if($_POST['stype'] == 1) echo "checked";?>/>创角时间
            <input type="radio" name="stype" value="2"<?php if($_POST['stype'] == 2) echo "checked";?>/>创角时间+登录时间
        </td>
    </tr>
        <tr>
            <th style="text-align: right;">创角日期:</th>
            <td style="text-align:left;">
                <input class='Wdate' type='text' size='40' id='startTime' name='startTime'
                       value='<?php echo (empty($startTime)) ? date('Y-m-d 00:00:00') : $startTime;?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
                    maxDate:'#F{$dp.$D(\'endTime\')}'})" />
                &nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;

                <input class='Wdate' type='text' size='40' id='endTime' name='endTime'
                       value='<?php echo (empty($endTime)) ? date('Y-m-d 23:59:59') : $endTime;?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
                    minDate:'#F{$dp.$D(\'startTime\')}'})" />
            </td>
        </tr>
        <tr>
            <th style="text-align: right;">登录日期:</th>
            <td style="text-align:left;">
                <input class='Wdate' type='text' size='40' id='loginstartTime' name='loginstartTime'
                       value='<?php echo (empty($loginstartTime)) ? date('Y-m-d 00:00:00') : $loginstartTime;?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
                    maxDate:'#F{$dp.$D(\'loginEndTime\')}'})" />
                &nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;

                <input class='Wdate' type='text' size='40' id='loginEndTime' name='loginEndTime'
                       value='<?php echo (empty($loginEndTime)) ? date('Y-m-d 23:59:59') : $loginEndTime;?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
                    minDate:'#F{$dp.$D(\'loginstartTime\')}'})" />
            </td>
        </tr>
        <tr>
            <th colspan="2"><input type="submit" value="确定查询"></th>
        </tr>
        </tbody>
    </table>
</form>
<hr class="hr"/>
<?php

if($task){
    ?>
    <table style="width: 100%;">
        <tr>
            <th>章节id</th>
            <th>人数</th>
            <th>比例</th>
        </tr>
        <?php foreach($task as $k => $v){?>
            <tr style="background-color:#f6f9f3;">
                <td style="text-align:center;"><?php echo $k; ?></td>
                <td style="text-align:center;"><?php echo $v;  ?></td>
                <td style="text-align:center;"><?php echo number_format($v*100/$total,2).'%';  ?></td>
            </tr>
        <?php } ?>
    </table>
<?php  }?>