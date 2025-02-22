<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr/>
<div class="mytable">
<a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=ggConfig">公告内容</a>
<a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=ggConfig2">公告配置</a>
<hr/>
<a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=addGg">添加公告配置</a>
</div>
<hr/>
<table style='width:100%;' class="mytable">
    <tr>
        <th>编号</th>
        <th>header</th>
        <th>title</th>
        <th>body</th>
        <th>top</th>
        <th style="width: 100px;">操作</th>
    </tr>

    <?php
    if ( is_array($data) ) {

        foreach ($data as $k => $v) {
            ?>
            <tr>
                <td><?php echo $k;?></td>
                <td><?php echo $v['header']; ?></td>
                <td><?php echo $v['title'];?></td>
                <td><?php echo $v['body'] ;?></td>
                <td><?php echo $v['top'] ;?></td>
                <td>
                        <input type='button' value='编辑'
                               onclick="location.href='?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=addGg&key=<?php echo $k;?>'" />
                        <a class="delete" data-name="<?php echo $v['name']['zh']?>" href='?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=delgg&delkey=<?php echo $k;?>'>删除</a>
                </td>

            </tr>
            <?php
        }
    }
    ?>

</table>
<hr/>

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