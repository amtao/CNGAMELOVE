<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 8;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr">
<table style='width:100%;' class="mytable">
    <tr>
        <th>邮件名称</th>
        <th>道具</th>
        <th>开始时间</th>
        <th>结束时间</th>
        <th>单服/全服</th>
        <th>操作</th>
    </tr>

    <?php
    if ( is_array($data) ) {

        foreach ($data as $k1 => $val) {
            ?>
            <tr>
                <td><?php echo $val['title'];?></td>
                <td>
                    <?php
                    foreach ($val['items'] as $k => $v){
                    echo '<span style="border:rosybrown solid 1px;margin:0 2px;padding:0px 3px;background-color: #F6E3DD">';
                        if ($v['kind'] == 7){
                            echo $hero[$v['id']]['name'];
                        }else{
                            echo $item[$v['id']]['name'];
                        }
                        echo ' | '.$v['count'].'</span>';
                    }
                    ?>
                </td>

                <td><?php echo $val['startTime'] ;?></td>
                <td><?php echo $val['endTime'] ;?></td>
                <td><?php if(empty($val['is_all'])){ echo '单服';}else{ echo '全服';}?></td>
                <td>
                    <a class="delete" data-name="<?php echo $val['title']?>" href='?sevid=<?php echo $SevidCfg['sevid'];?>&mod=mail&act=delMail&delkey=<?php echo $k1;?>'>删除</a>
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