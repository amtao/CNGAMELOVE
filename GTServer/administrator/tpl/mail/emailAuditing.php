<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 8;?>
<?php include(TPL_DIR.'zi_header.php');?>
<div style="width: 100%;display: inline-block;">
<hr class="hr" />
<div class="header">
        <a  class='backGroundColor' <?php if ($_GET['type'] == 1){echo 'style="color:red;"';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=mail&act=emailAuditing&type=1">无奖励邮件</a>
        <a  class='backGroundColor' <?php if ($_GET['type'] == 2){echo 'style="color:red;"';} ?>  href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=mail&act=emailAuditing&type=2">含奖励邮件</a>
        <a class='backGroundColor' <?php if ($_GET['type'] == 3){echo 'style="color:red;"';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=mail&act=emailAuditing&type=3">单服邮件<span style="color: red">(哪服审核发哪服)</span></a>
        <a class='backGroundColor' <?php if ($_GET['type'] == 4){echo 'style="color:red;"';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=mail&act=emailAuditing&type=4">全服邮件</a>
</div>
<hr class="hr" />
    <div class="header">
        <a  class='backGroundColor' <?php if ($getStatus == 0){echo 'style="color:red;"';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=mail&act=emailAuditing&nowStatus=0&type=<?php echo $_GET['type'];?>">待审核邮件</a>
        <a  class='backGroundColor' <?php if ($getStatus == 1){echo 'style="color:red;"';} ?>  href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=mail&act=emailAuditing&nowStatus=1&type=<?php echo $_GET['type'];?>">已审核邮件</a>
        <a  class='backGroundColor' <?php if ($getStatus == 2){echo 'style="color:red;"';} ?>  href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=mail&act=emailAuditing&nowStatus=2&type=<?php echo $_GET['type'];?>">被拒邮件</a>
    </div>
    <hr class="hr" />
<?php if (!empty($emailData)):?>
    <?php foreach($emailData as $key => $value):?>
        <?php if ($getType == $value['type'] && $getStatus == $value['status']):?>
        <table style="margin-bottom:10px;border:#CFBEBE solid 1px;" class="mytable">
            <tr style="background:#c6e4fe;border:#CFBEBE solid 1px;"><td colspan="2" style="border:#CFBEBE solid 1px;">【邮件标题：】<?php echo $value['title'];?></td></tr>
            <?php if ($_GET['type'] == 1 || $_GET['type'] == 2){
                    echo '<tr><td style="width: 50px;border:#CFBEBE solid 1px;">玩家id：</td><td style="border:#CFBEBE solid 1px;">'.$value['uids'].'</td></tr>';
                }
            ?>
            <tr><td style="width: 50px;border:#CFBEBE solid 1px;">内容：</td><td style="border:#CFBEBE solid 1px;"><?php echo $value['message'];?></td></tr>
            <tr><td style="width: 50px;border:#CFBEBE solid 1px;">道具：</td><td style="border:#CFBEBE solid 1px;">
                    <?php if (!empty($value['items'])):?>
                        <?php
                            foreach ($value['items'] as $keys => $values){
                                echo '<span style="width: 200px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block"><b style="padding-left:10px;">道 具 : </b>'.$items[$values['id']]['name'].' <b style="padding-left:10px;"> 数 量 : </b>'.$values['count'].'</span>';
                            }
                        ?>
                    <?php endif;?>
            <?php if (empty($value['items'])){ echo '无';}?>
                </td></tr>
            <tr><td style="width: 50px;border:#CFBEBE solid 1px;">时间：</td><td style="border:#CFBEBE solid 1px;">开始时间：<?php echo $value['startTime'];?> ~ 结束时间：<?php echo  $value['endTime'];?></td></tr>
            <tr><td style="width: 50px;border:#CFBEBE solid 1px;">提交人：</td><td style="border:#CFBEBE solid 1px;"><?php echo $value['user'];?></td></tr>
            <tr><td style="width: 50px;border:#CFBEBE solid 1px;">备注：</td><td style="border:#CFBEBE solid 1px;"><?php echo $value['remarks']?$value['remarks']:'无';?></td></tr>
            <tr><td style="width: 50px;border:#CFBEBE solid 1px;">状态：</td><td style="border:#CFBEBE solid 1px;">
                    <?php if ($value['status'] == 1) {
                        echo '已发放  ';
                        echo ' <a class="delete" style="background:#f5dfe3" href="?sevid='.$SevidCfg['sevid'].'&mod=mail&act=emailAuditing&type='.$value['type'].'&status=4&nowStatus='.$getStatus.'&emailKey='.$key.'">删除</a>';
                    } elseif ($value['status'] == 0){
                        echo '<a class="audit" style="background:#f5dfe3" href="?sevid='.$SevidCfg['sevid'].'&mod=mail&act=emailAuditing&type='.$value['type'].'&status=1&nowStatus='.$getStatus.'&emailKey='.$key.'">通过</a>';
                        echo '<a class="audit" style="background:#f5dfe3" href="?sevid='.$SevidCfg['sevid'].'&mod=mail&act=emailAuditing&type='.$value['type'].'&status=2&nowStatus='.$getStatus.'&emailKey='.$key.'">拒绝</a>';
                    }elseif ($value['status'] == 2){
                        echo '已拒绝  ';
                        echo ' <a class="delete" style="background:#f5dfe3" href="?sevid='.$SevidCfg['sevid'].'&mod=mail&act=emailAuditing&type='.$value['type'].'&status=4&nowStatus='.$getStatus.'&emailKey='.$key.'">删除</a>';
                    }
                    ?>
                </td></tr>
        </table>
        <?php endif;?>
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
