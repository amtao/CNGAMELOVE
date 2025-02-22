<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'gameact/header.php');?>
<script>
    $(function(){
        $("#header").hide();
        $(".header").hide();
        $(".mytable").hide();
        $("hr").hide();
    });

    function checkTempArg(){
        $("#formid").submit();
    }
</script>

<form id="formid" method="POST" action="" >
    <input type="hidden"  name='flag' value='1' />
    <table style='width:100%;'>
        <tr><th colspan="6">批量修改</th></tr>
        <tr>
            <td style='text-align: right;'>活动区服：</td>
            <td>
                <input type='text' size='40' id='server' name='server' value='<?php echo $_POST['server'];?>' />
                （all为全服，单服填写服务器编号，多个服用“,”隔开，连续服用"-"隔开）
            </td>
        </tr>
        <tr>
            <td style='text-align: right;'>ID：</td>
            <td><input type='text' size='40' id='gid' name='gid' value='<?php echo $_POST['gid'];?>' /></td>
        </tr>
        <tr>
            <td style='text-align: right;'>startDay：</td>
            <td><input type='text' size='40' id='startDay' name='startDay' value='<?php echo $_POST['startDay'];?>' /></td>
        </tr>
        <tr>
            <td style='text-align: right;'>endDay：</td>
            <td><input type='text' size='40' id='server' name='endDay' value='<?php echo $_POST['endDay'];?>' /></td>
        </tr>
        <tr>
            <td style='text-align: right;'>startTime：</td>
            <td><input class='Wdate' id='startTime' type='text' name='startTime' value='<?php echo $_POST['startTime'];?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true, minDate:'2016-01-01 00:00:00'})" /></td>
        </tr>
        <tr>
            <td style='text-align: right;'>endTime：</td>
            <td><input class='Wdate' id='endTime' type='text' name='endTime' value='<?php echo $_POST['endTime'];?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true, minDate:'2016-01-01 00:00:00'})" /></td>
        </tr>
        <tr>
            <td style='text-align: right;'>操作：</td>
            <td>
                <a href="javascript:checkTempArg()" style="color: green;display: inline-block;">【--保存--】</a>
            </td>
        </tr>
    </table>
</form>
<?php include(TPL_DIR.'footer.php');?>