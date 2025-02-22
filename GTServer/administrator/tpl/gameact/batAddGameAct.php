<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'gameact/header.php');?>

<form id="formid" method="POST" action="" >
    <table style='width:100%;' class="mytable">
        <tr><th colspan="6">添加活动第一步：选择模板</th></tr>
        <tr>
            <td style='text-align: right;' width="15%">复制到专服：</td>
            <td>
                <input type="hidden"  name='flag' value='1' />
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
                <div id='template_id' name='template_id'></div>
                <input type="button" name="checkAllTep" value="全选">
                <input type="button" name="nocheckAllTep" value="全不选">
            </td>
        </tr>
        <tr>
            <td style='text-align: right;'>操作：</td>
            <td>
                <a href="javascript:checkArg()" style="color: green;display: inline-block;">【--添加--】</a>
            </td>
        </tr>
    </table>
</form>
<script>
    function checkArg(){
        if ($("input[name='template[]']:checkbox:checked").length <= 0) {
            alert('未选择模板');
            return;
        }
        $("#formid").submit();
    }
    $(function(){
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
        $(':input[name="checkAllTep"]').click(function () {
            $(':input[name="template[]"]').each(function () {
                $(this).attr('checked', true);
            });
        });
        $(':input[name="nocheckAllTep"]').click(function () {
            $(':input[name="template[]"]').each(function () {
                $(this).attr('checked', false);
            });
        });

        $('#category_id').change(function(){
            var cate = $(this).val();
            $.ajax({
                type:"POST",
                url:'?sevid=<?php echo $_GET['sevid'];?>&mod=gameAct&act=getTemplateByCate&resType=box&cate='+cate,
                success:function(data){
                    $('#template_id').html(data);
                }
            });
        });
        $('#category_id').change();
    });
</script>
<?php include(TPL_DIR.'footer.php');?>
