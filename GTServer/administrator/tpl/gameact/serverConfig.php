<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'gameact/header.php');?>
<form name="chat" method="POST" action="">
    <table style="width: 100%" >
        <tr><th colspan="2">添加专服</th></tr>
        <tr>
            <th style="text-align:right;">专服名称 :</th>
            <td style="text-align:left;">
                <input  style="width: 70%" type="text"  name="title"  value=""/>
            </td>
        </tr>
        <tr>
            <th style="text-align:right;">Url :</th>
            <td style="text-align:left;">
                <input style="width: 70%"  type="text"  name="url"  value=""/>
            </td>
        </tr>
        <tr><th colspan="2"><input type="submit" value="确定提交" /></th></tr>
    </table>
</form>
<hr class="hr">
<table style='width:100%;' class="mytable">
    <tr>
        <th colspan="3">专服信息：</th>
    </tr>
    <tr>
        <th>名称</th>
        <th>url</th>
        <th>操作</th>
    </tr>
    <?php foreach ($severConfig as $key => $value): ?>
        <tr>
            <td><?php echo $value['title'];?></td>
            <td><?php echo $value['url'];?></td>
            <td><?php echo '<a class="delete" style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=gameAct&act=serverConfig&delete='.$key.'">删除 </a>'; ?></td>
        </tr>
    <?php endforeach;?>
</table>
<script>
    $(document).ready(function () {
        $(".delete").on('click',function (e) {
            e.preventDefault();
            if (confirm('确定要删除吗?')){
                var href = $(this).prop('href');
                window.location.href = href;
            }else{
                return false;
            }
        });
    });
</script>