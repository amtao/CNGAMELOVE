<?php include(TPL_DIR . 'header.php');?>

<div class="mytable">
    <a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=servers&act=slist">服务器列表</a>
    <a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=servers&act=listStatus">查看服务器状态</a>
</div>
<hr class="hr"/>

<table style='width:100%;'>
    <tr>
        <th>区号</th>
        <th>名字</th>
        <th>域名</th>
        <th>mem状态</th>
        <th>redis状态</th>
        <th>选区图标开放时间</th>
    </tr>

    <?php
    if ( is_array($sList) ) {

        foreach ($sList as $k => $v) {
            ?>
            <tr>
                <td><?php echo $v['id'];?></td>
                <td>
                    <?php
                    echo $v['name']['zh'];
                    ?>
                </td>
                <td><?php echo $v['url'];?></td>

                <td><?php echo $v['mem'];?></td>
                <td><?php echo $v['redis'] ;?></td>
                <td><?php echo date("Y-m-d H:i:s", $v['showtime']) ;?></td>
            </tr>
            <?php
        }
    }
    ?>

</table>
<?php include(TPL_DIR.'footer.php');?>
<script>
    $(document).ready(function () {
        $(".delete").on('click',function (e) {
            e.preventDefault();
            var name_zh = $(this).data('name');
            if (confirm('确定要删除'+name_zh+'吗?')){
                var href = $(this).prop('href');
                window.location.href = href;
            }else{
                return false;
            }
        });
    });
</script>