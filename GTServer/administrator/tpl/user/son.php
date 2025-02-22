<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>
<hr class="hr" />
<h2 style="color: red;" >徒弟数据</h2>
<style type="text/css">
    .wife_div {
        float:left;
        border: medium double buttonface;
        margin: 2px;
        padding: 3px;
    }
</style>
<hr class="hr"/>
<br>
<div style="height:auto;">
    <p><b>玩家的徒弟信息 :</b></p>
    <?php
    //打印用户信息
    foreach ($info as $k => $v){
        echo '<div class="wife_div">';
        echo '<form action="" method="post">';
        echo '<input name="son_key" value="'.$k.'" type="hidden" />';
        echo '<p>'.$k.' : '.$v['name'].'</p>';
        echo '<textarea rows="8" cols="30" name="info">';
        echo var_export($v);
        echo '</textarea>';
        echo '<br/>';
        echo '<input type="submit" value="修改">';
        echo '</form>';
        echo '</div>';
    }
    ?>
</div>
<?php include(TPL_DIR.'footer.php');?>
