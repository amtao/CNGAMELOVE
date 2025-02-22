<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<form name="form" method="post" action="">
    <table>
        <tr><th colSpan="2">deeplink</th></tr>
        <tr><th style="width: 40%">设置ID*</th><td><?php echo $deeplinkInfo['id'];?></td></tr>

        <tr><th style="width: 40%">开始时间*</th><td><input class='Wdate' type='text' size='40' id='startTime' name='startTime'
                           value='<?php echo (empty($deeplinkInfo['stime'])) ? date('Y-m-d 00:00:00') : date('Y-m-d H:i:s', $deeplinkInfo['stime']);?>'
                           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
                        maxDate:'#F{$dp.$D(\'endTime\')}'})" /></td></tr>

        <tr><th style="width: 40%">结束时间*</th><td> <input class='Wdate' type='text' size='40' id='endTime' name='endTime'
               value='<?php echo (empty($deeplinkInfo['etime'])) ? date('Y-m-d 23:59:59') : date('Y-m-d H:i:s', $deeplinkInfo['etime']);?>'
               onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
                        minDate:'#F{$dp.$D(\'startTime\')}'})" /></td></tr>

        <tr><th style="width: 40%">活动类型编号*</th><td>
            <select name="actid" id="actid">
                 <?php foreach ($actList as $key => $value):?>
                     <option value="<?php echo $key; ?>" <?php if($deeplinkInfo['actid'] == $key) echo "selected";?>><?php echo $value['id'].'-'.$value['act_key'].'-'.$value['title']; ?></option>
                 <?php endforeach;?>
            </select>
        </td></tr>

        <tr><th style="width: 40%">链接地址*</th><td><input type="text" name="url_path" style="width: 300px;" size="120" id="url_path"  value="<?php echo $deeplinkInfo['url_path']?>"></td></tr>

        <tr><th colspan="2"><input type="submit" class="input" value="<?php echo $deeplinkInfo['id'] > 0 ? '修改' : '新增'?>" /></th></tr>
    </table>
    <input type="hidden" value="update" name="type"></input>
    <input type="hidden" value="<?php echo $deeplinkInfo['id'];?>" name="id"></input>
</form>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>