<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<table style="width: 100%" class="mytable">
    <tr>
        <th>ID</th>
        <th>开始时间</th>
        <th>结束时间</th>
        <th>链接地址</th>
        <th>活动类型编号</th>
        <th>操作</th>
    </tr>
    <?php foreach($deeplinkList as $k => $val){?>
        <tr style="background-color:#f6f9f3" >
            <td style="text-align:center;"><?php echo $val['id']; ?></td>
            <td style="text-align:center;"><?php if ($val['id'] > 0) {echo date('Y-m-d H:i:s',$val["stime"]);} ?></td>
            <td style="text-align:center;"><?php if ($val['id'] > 0) {echo date('Y-m-d H:i:s',$val["etime"]);} ?></td>
            <td style="text-align:center;"><?php echo $val['url_path']; ?></td>
            <td style="text-align:center;"><?php if ($val['id'] > 0) {echo $actList[$val['actid']]['id'].'-'.$actList[$val['actid']]['act_key'].'-'.$actList[$val['actid']]['title'];} ?></td>
            <td style="text-align:center;">
                <?php

                    if ($val['id'] > 0) {

                        echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=fun&act=getDeeplink&type=select&id='.$val['id'].'">修改 </a>';

                        echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=fun&act=getDeeplink&type=delete&id='.$val['id'].'">删除 </a>';
                    }else{
                        echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=fun&act=getDeeplink&type=select&id='.$val['id'].'">新增 </a>';
                    }
                ?>
            </td>
        </tr>
    <?php } ?>
</table>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>