<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>
<?php
//打印用户信息

//活动解释
$comm = array(
    1 => '资源经营',
    2 => '政务处理',
    3 => '关卡出战',
    4 => '蒙古来袭出战',
    5 => '葛二蛋来袭出战',
    9 => '徒弟招亲',
    10 => '徒弟提亲',
    11 => '知己精力',
    12 => '徒弟席位',
    15 => '书院桌子',
    16 => '学院学习',
    17 => '排行膜拜',
    18 => '皇宫请安',
    19 => '牢房',
    20 => '名望',
    21 => '蒙古军波数',
    22 => '割二蛋CD',
    23 => '副本积分兑换',
    25 => '称号',
    26 => '寻访',
    27 => '寻访-赈灾-运势恢复',
    28 => '寻访-赈灾-转运',
    29 => '寻访-NPC',
);
?>
 <hr class="hr" />
<h2 style="color: red;">活动数据</h2>
<style type="text/css">
.hero_div {
    float:left;
	border: medium double buttonface;
    margin: 2px;
    padding: 3px;
}
</style>
<!--<br>
<div style="margin: 6px 0px;">
    <form action="" method="post">
        <span><b>活动列表 :</b></span>
        <select name="add_Act_key" >
            <?php /*foreach ($comm as $key => $value):*/?>
                <option value="<?php /*echo $key;*/?>"><?php /*echo $value;*/?></option>
            <?php /*endforeach;*/?>
        </select>
        <input type="submit" class="input" value="新增">
    </form>
</div>-->
<hr class="hr" />
<div style="height:auto;">
    <?php
    foreach ($info as $k=>$v){
        //初始化这个类 取出活动解释
        $actname = 'getAct'.$k;
        Common::loadModel('Master');
        $acvModel = Master::$actname($uid);

        $com = isset($comm[$k])?$comm[$k]:'';
        echo '<div class="hero_div">';
        echo '<form action="" method="post">';
        echo '<input name="hero_key" value="'.$k.'" type="hidden" />';
        echo '<p>'.$k.' :'.$acvModel->comment.$cfg_hero[$k]['name'].'</p>';
        echo '<textarea rows="8" cols="30" name="info">';
        echo var_export($v);
        echo '</textarea>';
        echo '<br/>';
        echo '<input type="submit" value="修改">';
        echo '</form>';
        echo '</div>';
    }
    ?>
    <div class="acty_div">
	<form action="" method="post">
	<input type="hidden" name="uid" value="<?php echo $uid;?>" />
	<p></p>
	<br/>
	<textarea rows="8" cols="30" name="add_info"></textarea>
	<br/>
	<input type="submit" value="新增">
	</form></div>
    
</div>
<div style="width: 100%;clear: both;">
</div>

<?php include(TPL_DIR.'footer.php');?>