<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>

<script>
    function checkTempArg(){
        var server = $("#server").val();
        if (server == null || server == "") {
            alert("服数不能为空");
            return;
        } else if(server.indexOf("，") >= 0) {
            alert("服数不能包含中文逗号");
            return;
        }
        var act_key = $("#config_key").val();
        var contents = $("#contents").val();
        if (act_key == null || act_key == "") {
            alert("KEY不能为空");return;
        }
        $.ajax({
            type:"POST",
            url:'?sevid=<?php echo $_GET['sevid'];?>&mod=gameConfig&act=checkTempAjax',
            dataType:"json",
            async:false,
            data:"r="+Math.random()+"&contents="+contents,
            success:function(msg){
                if(msg["ok"]){
                    $("#formid").submit();
                }else{
                    alert(msg["result"]);
                }
            }
        });
    }
</script>

<?php if (isset($_REQUEST['flag'])):?>
<div style="color:red"><?php echo $msg;?></div>
<?php endif;?>

<form id="formid" method="POST" action="" >
    <input type="hidden"  name='flag' value='1' />
    <table style='width:100%;' class="mytable">
        <tr><th colspan="6">添加模板</th></tr>
        <tr>
            <td style='text-align: right;'>KEY：</td>
            <td><input type='text' size='40' id='config_key' name='config_key' value='<?php echo $_POST['config_key'];?>' /></td>
        </tr>
        <tr>
            <td style='text-align: right;'>生效区服：</td>
            <td>
                <input type='text' size='40' id='server' name='server' value='<?php echo empty($_POST['server']) ? 'all' : $_POST['server'];?>' />
                （all为全服，单服填写服务器编号，多个服用“,”隔开，连续服用"-"隔开）
            </td>
        </tr>
        <tr>
            <td style='text-align: right;'>详细信息：</td>
            <td>
                <textarea rows="40" cols="150" id="contents" name="contents"><?php echo $_POST['contents'];?></textarea>
            </td>
        </tr>
        <tr>
            <td style='text-align: right;'>操作：</td>
            <td style='text-align: center;'>
                <a href="javascript:checkTempArg()" style="color: green;display: inline-block;">【--添加--】</a>
            </td>
        </tr>
    </table>
</form>
<?php include(TPL_DIR.'footer.php');?>
