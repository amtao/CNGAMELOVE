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
        var contents = $("#contents").val();
        $.ajax({
            type:"POST",
            url:'?sevid=<?php echo $_GET['sevid'];?>&mod=gameAct&act=checkTempAjax',
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

<form id="formid" method="POST" action="" >
    <input type="hidden"  name='flag' value='1' />
    <input type="hidden" id='id'  name='id' value='<?php echo $info['id'];?>' />
    <table style='width:100%;'>
        <tr><th colspan="6">代码查看活动</th></tr>
        <tr>
            <td style='text-align: right;'>序号：</td>
            <td><?php echo $info['id'];?></td>
        </tr>
        <tr>
            <td style='text-align: right;'>活动区服：</td>
            <td>
                <input type='text' size='40' id='server' name='server' value='<?php echo $info['server'];?>' />
                （all为全服，单服填写服务器编号，多个服用“,”隔开，连续服用"-"隔开）
            </td>
        </tr>
        <tr>
            <td style='text-align: right;'>活动编号：</td>
            <td><?php echo $info['act_key'];?></td>
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