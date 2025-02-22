<?php if(!empty($uid)):?>
<hr class="hr"/>
<div class="header" >
<a  class='backGroundColor' <?php if ($_GET['act'] == 'goldFlow'){echo 'style="color:red;';} ?> href="?mod=user&act=goldFlow&uid=<?php echo $uid;?>">元宝流水</a>
    <a  class='backGroundColor' <?php if ($_GET['act'] == 'silverFlow'){echo 'style="color:red;';} ?> href="?mod=user&act=goldFlow&uid=<?php echo $uid;?>">阅历流水</a>
    <a  class='backGroundColor' <?php if ($_GET['act'] == 'foodFlow'){echo 'style="color:red;';} ?> href="?mod=user&act=foodFlow&uid=<?php echo $uid;?>">银两流水</a>
    <a  class='backGroundColor' <?php if ($_GET['act'] == 'soldierFlow'){echo 'style="color:red;';} ?> href="?mod=user&act=soldierFlow&uid=<?php echo $uid;?>">名声流水</a>

<a  class='backGroundColor' <?php if ($_GET['act'] == 'itemFlow'){echo 'style="color:red;';} ?>  href="?mod=user&act=itemFlow&uid=<?php echo $uid;?>">道具流水</a>
<a class='backGroundColor' <?php if ($_GET['act'] == 'activityFlow'){echo 'style="color:red;';} ?> href="?mod=user&act=activityFlow&uid=<?php echo $uid;?>">活动</a>
<a class='backGroundColor' <?php if ($_GET['act'] == 'heroFlow'){echo 'style="color:red;';} ?> href="?mod=user&act=heroFlow&uid=<?php echo $uid;?>">伙伴流水</a>
<a class='backGroundColor' <?php if ($_GET['act'] == 'wifeFlow'){echo 'style="color:red;';} ?>  href="?mod=user&act=wifeFlow&uid=<?php echo $uid;?>">知己流水</a>
<a  class='backGroundColor' <?php if ($_GET['act'] == 'sonFlow'){echo 'style="color:red;';} ?>  href="?mod=user&act=sonFlow&uid=<?php echo $uid;?>">徒弟流水</a>
</div>
<?php endif;?>