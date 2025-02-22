<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>
<hr class="hr" />
<h1 style="color: red;">伙伴数据 :</h1>
<style type="text/css">
.hero_div {
    float:left;
	border: medium double buttonface;
    margin: 2px;
    padding: 3px;
}
</style>
<div style="margin: 6px 0px;">
    <form action="" method="post">
        <span><b>伙伴列表 :</b></span>
        <?php if (empty($userNoHero)){?>
            <span style="color: red;">暂无新英雄可添加</span>
        <?php }else{ ?>
        <select name="add_hero_key" >
            <?php foreach ($userNoHero as $key => $value):?>
                <option value="<?php echo $value['heroid'];?>"><?php echo $value['name'];?></option>
            <?php endforeach;?>
        </select>
        <input type="submit" class="input" value="新增">
        <?php } ?>
    </form>
</div>
<hr class="hr" />
<div style="height:auto;">
    <p><b>玩家的伙伴信息 :</b></p>
    <?php
        //打印用户信息
        foreach ($info as $k=>$v){
            echo '<div class="hero_div">';
            echo '<form action="" method="post">';
            echo '<input name="hero_key" value="'.$k.'" type="hidden" />';
            echo '<p>'.$k.' : '.$cfg_hero[$k]['name'].'</p>';
            echo '<textarea rows="8" cols="30" name="info">';
            echo var_export($v);
            echo '</textarea>';
            echo '<br/>';
            echo '<input type="submit" class="input" value="修改">';
            echo '</form>';
            echo '</div>';
        }
    ?>
</div>

<div style="width: 100%;clear: both;">
    <hr class="hr" />
    <p><b>所有伙伴信息 :</b></p>
    <?php if($allhero){?>
        <pre><?php echo $allhero;?></pre>
    <?php }?>
</div>

</form>
</div>
    <div style="width: 400px;display: inline-block;float: center;">
        <p>伙伴故事:</p>
        <form action="" method="post">
            <table style="width: 300px;">
                <tr>
                    <td style="text-align: center;background-color: #B0CFED;">伙伴id</td>
                    <td style="text-align: center;background-color: #B0CFED;">伙伴故事个数</td>
                </tr>
                <?php if(!empty($herostory)){ foreach ($herostory as $key => $value){?>
                <tr>
                    <td style="text-align: center;"><?php echo $key;?></td>
                    <td style="text-align: center;"><?php echo $value;?></td>
                </tr>
                <?php } }?>
            </table>
        </form></div>

<?php include(TPL_DIR.'footer.php');?>