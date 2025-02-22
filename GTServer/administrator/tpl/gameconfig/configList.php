<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>

<table style="text-align: center;" class="mytable">
    <tr><th colspan="5"><a id='addConfig' href='?sevid=<?php echo $_GET['sevid'];?>&mod=gameConfig&act=addConfig'>添加配置</a></th></tr>
    <tr><th colspan="5">模板列表</th></tr>
    <tr><th>序号</th><th>生效区服</th><th>活动编号</th><th>详情</th><th>操作</th></tr>
    <?php foreach ($list as $list_v):?>
        <tr>
            <td><?php echo $list_v['id'];?></td>
            <td><?php echo $list_v['server'];?></td>
            <td><?php echo $list_v['config_key'];?></td>
            <td><?php echo htmlspecialchars(substr($list_v['contents'],0,80));?></td>
            <td>
                <?php
                echo <<<STRING
			     <a id='view_detail_{$list_v['id']}' href='?sevid={$_GET['sevid']}&mod=gameConfig&act=viewConfig&id={$list_v['id']}' >查看</a>
STRING;
                echo <<<STRING
			    <a id='detail_{$list_v['id']}' href='?sevid={$_GET['sevid']}&mod=gameConfig&act=editConfig&id={$list_v['id']}' >编辑</a>
                <input type='button' value='删除' onclick="delSever('{$list_v['id']}')" />
STRING;
                ?>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<script type="text/javascript">
    var jquery = jQuery.noConflict(true);
    jquery(document).ready(function() {
        <?php foreach ($list as $key => $value):?>
        jquery("#detail_<?php echo $value['id'];?>").fancybox({
            'width'				: '100%',
            'height'			: '100%',
            'autoScale'			: false,
            'transitionIn'		: 'none',
            'transitionOut'		: 'none',
            'type'				: 'iframe',
            'onClosed' : function() {
                window.location.href = window.location.href;
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
    });
    //确认删除
    function delSever(tempID) {
        if ( window.confirm('删除该活动模板?') ) {
            location.href = '?sevid=<?php echo $_GET['sevid']?>&mod=gameConfig&act=configList&del=1&id=' + tempID;
        }
    }
</script>
<?php include(TPL_DIR.'footer.php');?>