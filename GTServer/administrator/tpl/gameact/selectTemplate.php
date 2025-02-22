<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'gameact/header.php');?>

<table style='width:100%;' class="mytable">
    <tr><th colspan="6">添加活动第一步：选择模板</th></tr>
    <tr>
        <td style='text-align: right;width:20%'>活动模板：</td>
        <td>
            <select id='category_id' name='category_id'>
                <?php
                foreach ($category as $cid => $val){
                    if(isset($_REQUEST['category_id']) && $_REQUEST['category_id'] == $cid) {
                        $selected = "selected='selected'";
                    }else{
                        $selected = "";
                    }
                    echo "<option value='{$cid}' {$selected} >$val</option>";
                }
                ?>
            </select>
            <select id='template_id' name='template_id'></select>
        </td>
    </tr>
    <tr>
        <td style='text-align: right;'>操作：</td>
        <td>
            <a href="javascript:nextOpt()" style="color: green;display: inline-block;">【--下一步--】</a>
        </td>
    </tr>
</table>
<script>
    function nextOpt(){
        var templateID = $('#template_id option:selected').val();
        if (templateID == null || templateID == "" || templateID == 0)
        {
            alert("请选择活动模板");
            return;
        }
        var cateID = $('#category_id option:selected').val();
        var url = '?sevid=<?php echo $_GET['sevid'];?>&mod=gameAct&act=addGameAct&template_id=' + templateID + '&category_id=' + cateID;
        window.location.href = url;
    }

    $(function(){
        $('#category_id').change(function(){
            var cate = $(this).val();
            $.ajax({
                type:"POST",
                url:'?sevid=<?php echo $_GET['sevid'];?>&mod=gameAct&act=getTemplateByCate&cate='+cate,
                success:function(data){
                    $('#template_id').html(data);
                }
            });
        });
        $('#category_id').change();
    });
</script>
<?php include(TPL_DIR.'footer.php');?>
