<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>
<hr class="hr" />
<h2 style="color: red;" >知己数据</h2>
<style type="text/css">
.wife_div {
    float:left;
	border: medium double buttonface;
    margin: 2px;
    padding: 3px;
}
</style>
<div style="margin: 6px 0px;">
    <form action="" method="post">
        <span><b>知己列表 :</b></span>
        <select name="add_wife_key" >
            <?php foreach ($userNoWife as $key => $value):?>
                <option value="<?php echo $value['wid'];?>"><?php echo $value['wname'];?></option>
            <?php endforeach;?>
        </select>
        <input type="submit" class="input" value="新增">
    </form>
</div>
<hr class="hr"/>
<br>
<div style="height:auto;">
    <p><b>玩家的知己信息 :</b></p>
    <?php
    //打印用户信息
    foreach ($info as $k=>$v){
        echo '<div class="wife_div">';
        echo '<form action="" method="post">';
        echo '<input name="wife_key" value="'.$k.'" type="hidden" />';
        echo '<p>'.$k.' : '.$cfg_wife[$k]['wname'].'</p>';
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
<div style="width: 100%;clear: both;">
    <hr class="hr" />
    <p><b>所有知己信息 :</b></p>
    <?php if($allwife){?>
        <pre><?php echo $allwife;?></pre>
    <?php }?>
</div>
<?php include(TPL_DIR.'footer.php');?>
