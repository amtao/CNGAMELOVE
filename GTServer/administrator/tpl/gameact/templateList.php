<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'gameact/header.php');?>
<table style="text-align: center;" class="mytable">
    <tr><th colspan="5">模板列表</th></tr>
    <tr><th>序号</th><th>活动名称</th><th>活动编号</th><th>上次编辑人</th><th>操作</th></tr>
    <tr>
        <td colspan="5" style="text-align: left;">
            <input type="button" name="checkAll" value="全选">
            <input type="button" name="nocheckAll" value="全不选">
            <input type="button" name="check" value="反选">
            <input type="button" name="delete" value="确认删除">
            <input type='button' value='一键导出' name="exportEvent" />
        </td>
    </tr>
    <?php foreach ($list as $list_v):?>
        <tr>
            <td><input type="checkbox" name="id" value="<?php echo $list_v['id'];?>"><?php echo $list_v['id'];?></td>
            <td><?php echo $list_v['title'];?></td>
            <td><?php echo $list_v['act_key'];?></td>
            <td><?php echo $list_v['auser'];?></td>
            <td>
                <?php
                echo <<<STRING
			     <a id='view_detail_{$list_v['id']}' href='?sevid={$_GET['sevid']}&mod=gameAct&act=viewTemplate&id={$list_v['id']}' >查看</a>
STRING;
                echo <<<STRING
			    <a id='detail_{$list_v['id']}' href='?sevid={$_GET['sevid']}&mod=gameAct&act=editTemplate&id={$list_v['id']}' >编辑</a>
                <input type='button' value='删除' onclick="delSever('{$list_v['id']}')" />
STRING;
                ?>
            </td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="5" style="text-align: left;">
            <input type="button" name="checkAll" value="全选">
            <input type="button" name="nocheckAll" value="全不选">
            <input type="button" name="check" value="反选">
            <input type="button" name="delete" value="确认删除">
            <input type='button' value='一键导出' name="exportEvent" />
        </td>
    </tr>
</table>
<hr />
<input type='button' value='一键历史导入' onclick="importEvent()" />
<script type="text/javascript">
    var jquery = jQuery.noConflict(true);
    jquery(document).ready(function() {
        $(':input[name="check"]').click(function () {
            $(':input[name="id"]').each(function () {
                if ($(this).attr('checked') == true) {
                    $(this).attr('checked',false);
                }else {
                    $(this).attr('checked',true);
                }
            });
        });
        $(':input[name="checkAll"]').click(function () {
            $(':input[name="id"]').each(function () {
                $(this).attr('checked',true);
            });
        });
        $(':input[name="nocheckAll"]').click(function () {
            $(':input[name="id"]').each(function () {
                $(this).attr('checked',false);
            });
        });
        <?php foreach ($list as $key => $value):?>
        jquery("#detail_<?php echo $value['id'];?>").fancybox({
            'width'				: '100%',
            'height'			: '100%',
            'autoScale'			: false,
            'transitionIn'		: 'none',
            'transitionOut'		: 'none',
            'type'				: 'iframe',
            'onClosed' : function() {
                //window.location.href = window.location.href;
            }
        });

        jquery("#view_detail_<?php echo $value['id'];?>").fancybox({
            'width'				: '100%',
            'height'			: '100%',
            'autoScale'			: false,
            'transitionIn'		: 'none',
            'transitionOut'		: 'none',
            'type'				: 'iframe'
        });
        <?php endforeach;?>
        function getSelectID()
        {
            var id = '';
            $(':input[name="id"]').each(function () {
                if ($(this).attr('checked')) {
                    if(id == ''){
                        id = $(this).val();
                    }else {
                        id = id +'-' + $(this).val();
                    }
                }
            });
            return id;
        }
        $(':input[name="exportEvent"]').click(function(){
            var id = getSelectID();
            console.log(id);
            if (id == '') {
                alert('请选择要导出的模板');
                return false;
            }
            if ( window.confirm('一键导出活动模板?') ) {
                location.href = '?sevid=<?php echo $_GET['sevid']?>&mod=gameAct&act=exportTemplate&export=1&id='+id;
            }
        });
        $(':input[name="delete"]').click(function () {
            var id = getSelectID();
            console.log(id);
            if (id == '') {
                alert('请选择要删除的模板');
                return false;
            }
            if ( window.confirm('确认删除?') ) {
                delSever(id);
            }
        });
    });
    //确认删除
    function delSever(tempID) {
        if ( window.confirm('删除该活动模板?') ) {
            location.href = '?sevid=<?php echo $_GET['sevid']?>&mod=gameAct&act=templateList&del=1&id=' + tempID;
        }
    }
    function importEvent(){
        if ( window.confirm('一键导入所有活动模板?') ) {
            location.href = '?sevid=<?php echo $_GET['sevid']?>&mod=gameAct&act=templateList&import=1';
        }
    }
</script>
<?php include(TPL_DIR.'footer.php');?>