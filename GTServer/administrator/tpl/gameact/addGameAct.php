<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'gameact/header.php');?>

<script type="text/javascript">
    function checkArg(){
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
    $(document).ready(function() {
        $(':input[name="checkAll"]').click(function () {
            $(':input[name="mark_copy[]"]').each(function () {
                $(this).attr('checked', true);
            });
        });
        $(':input[name="nocheckAll"]').click(function () {
            $(':input[name="mark_copy[]"]').each(function () {
                $(this).attr('checked', false);
            });
        });
    });
</script>

<?php if (isset($_REQUEST['flag'])):?>
<div style="color:red"><?php echo $msg;?></div>
<?php endif;?>

<form id="formid" method="POST" action="" >
    <input type="hidden"  name='flag' value='1' />
    <table style='width:100%;' class="mytable">
        <tr><th colspan="6">添加活动第二步：填写活动信息</th></tr>
        <tr>
            <td style='text-align: right;' width="15%">复制到专服：</td>
            <td>
                <?php
                foreach ($allMark as $mark => $allMark_v){
                    $selected = in_array($mark, $mark_copy) ? 'checked="checked"' : '';
                    echo "<label><input type='checkbox' name='mark_copy[]' {$selected} value='{$mark}'>{$allMark_v['title']}&nbsp;</label>";
                }
                ?>
                <br/>
                <input type="button" name="checkAll" value="全选">
                <input type="button" name="nocheckAll" value="全不选">
            </td>
        </tr>
        <tr>
            <td style='text-align: right;'>活动编号：</td>
            <td><?php echo $template['act_key'];?></td>
        </tr>
        <tr>
            <td style='text-align: right;'>活动区服：</td>
            <td>
                <input type='text' size='40' id='server' name='server' value='<?php echo empty($_POST['server']) ? 'all' : $_POST['server'];?>' />
                （all为全服，单服填写服务器编号，多个服用“,”隔开，连续服用"-"隔开）
            </td>
        </tr>
        <tr>
            <td style='text-align: right;'>详细信息：</td>
            <td>
                <textarea rows="40" cols="150" id="contents" name="contents"><?php echo empty($_POST['contents']) ? $template['contents'] : $_POST['contents'];?></textarea>
            </td>
        </tr>
        <tr>
            <td style='text-align: right;'>操作：</td>
            <td>
                <a href="?sevid=<?php echo $_GET['sevid'];?>&mod=gameAct&act=selectTemplate&template_id=<?php echo $_REQUEST['template_id'];?>&category_id=<?php echo $_REQUEST['category_id'];?>" style="color: green;display: inline-block;" >【--取消返回--】</a>
                <a href="javascript:checkArg()" style="color: green;display: inline-block;">【--添加--】</a>
            </td>
        </tr>
    </table>
</form>
<?php include(TPL_DIR.'footer.php');?>
