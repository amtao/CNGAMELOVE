<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 8;?>
<?php include(TPL_DIR.'zi_header.php');?>
<div style="width: 100%;display: inline-block;">

<hr class="hr" />
<?php if (!empty($data)):?>
    <?php foreach($data as $key => $value):?>
        <table style="margin-bottom:10px;border:#CFBEBE solid 1px;" class="mytable">
            <tr style="background:#c6e4fe;border:#CFBEBE solid 1px;"><td colspan="2" style="border:#CFBEBE solid 1px;">【邮件：】<?php echo $key;?></td></tr>
            <tr><td style="width: 100px;border:#CFBEBE solid 1px;">邮件标题：</td><td style="border:#CFBEBE solid 1px;"><?php echo $value['title'];?></td></tr>
            <tr><td style="width: 100px;border:#CFBEBE solid 1px;">道具：</td><td style="border:#CFBEBE solid 1px;">
                    <?php if (!empty($value['items'])):?>
                        <?php
                            foreach ($value['items'] as $keys => $values){
                                if($this->exchageRate){
                                    echo '<span style="width: 250px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block"><b style="padding-left:10px;">道 具 : </b>';
                                    echo $items[$values['id']]['name'].'-'.$this->items[$values['id']];
                                }else{
                                    echo '<span style="width: 200px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block"><b style="padding-left:10px;">道 具 : </b>';
                                    echo $items[$values['id']]['name'];
                                }
                                echo ' <b style="padding-left:10px;"> 数 量 : </b>'.$values['count'].'</span>';
                            }
                        ?>
                    <?php endif;?>
            <?php if (empty($value['items'])){ echo '无';}?>
                </td></tr>
            <tr><td style="width:100px;border:#CFBEBE solid 1px;">时间：</td><td style="border:#CFBEBE solid 1px;">开始时间：<?php echo $value['startTime'];?> ~ 结束时间：<?php echo  $value['endTime'];?></td></tr>
            <tr><td style="width: 100px;border:#CFBEBE solid 1px;">等级：</td><td style="border:#CFBEBE solid 1px;"><?php echo  $value['level'];?></td></tr>
            <tr><td style="width: 150px;border:#CFBEBE solid 1px;">注册限制时间：</td><td style="border:#CFBEBE solid 1px;"><?php echo  $value['registerTime']?'是':'否';?></td></tr>
            <tr><td style="width: 100px;border:#CFBEBE solid 1px;">VIP类型：</td><td style="border:#CFBEBE solid 1px;"><?php echo  $value['vipType'];?></td></tr>
            <tr><td style="width: 100px;border:#CFBEBE solid 1px;">VIP信息：</td><td style="border:#CFBEBE solid 1px;"><?php echo  $value['vipData'];?></td></tr>
            <tr><td style="width: 100px;border:#CFBEBE solid 1px;">创建时间：</td><td style="border:#CFBEBE solid 1px;"><?php echo  date("Y-m-d H:i:s",$value['ctime']);?></td></tr>
            <tr><td style="width: 100px;border:#CFBEBE solid 1px;">是否全服：</td><td style="border:#CFBEBE solid 1px;"><?php echo  $value['is_all']?'全服邮件':'本服邮件';?></td></tr>
                </td></tr>
        </table>
    <?php endforeach;?>
<?php endif;?>
<?php include(TPL_DIR.'footer.php');?>
<script>
    $(document).ready(function () {
        $(".audit").on('click',function (e) {
            e.preventDefault();
            $(this).attr('disabled', true);
            if (confirm('确定要执行吗?')){
                var href = $(this).prop('href');
                window.location.href = href;
            }else{
                $(this).attr('disabled', false);
                return false;
            }
        });
        $(".delete").on('click',function (e) {
            e.preventDefault();
            $(this).attr('disabled', true);
            if (confirm('确定要删除吗?')){
                var href = $(this).prop('href');
                window.location.href = href;
            }else{
                $(this).attr('disabled', false);
                return false;
            }
        });
    });
</script>
