<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<script>
    $(function(){
        $("#header").hide();
        $(".header").hide();
        $(".mytable").hide();
        $("hr").hide();
    });

    function checkTempArg(){
        var server = $("#server").val();
        if (server == null || server == "") {
            alert("服数不能为空");
            return;
        } else if(server.indexOf("，") >= 0) {
            alert("服数不能包含中文逗号");
            return;
        }
        var contents = $("#contents").val();
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
    <input type="hidden" id='id'  name='id' value='<?php echo $info['id'];?>' />
    <table style='width:100%;' class="mytable">
        <tr><th colspan="6">编辑模板</th></tr>
        <tr>
            <td style='text-align: right;'>序号：</td>
            <td><?php echo $info['id'];?></td>
        </tr>
        <tr>
            <td style='text-align: right;'>KEY</td>
            <td><?php echo $info['config_key'];?></td>
        </tr>
        <tr>
            <td style='text-align: right;'>生效区服：</td>
            <td>
                <input type='text' size='40' id='server' name='server' value='<?php echo $info['server'];?>' />
            </td>
        </tr>
        <tr>
            <td style='text-align: right;'>详细信息：</td>
            <td>
                <textarea rows="40" cols="150" id="contents" name="contents"><?php echo $info['contents'];?></textarea>
            </td>
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
