<hr class="hr" />
<form name="form1" id="form1" method="post" action="">
Uid:<input type="text" name="uid" value="<?php echo $uid;?>"  />&nbsp;
<input type="submit" value="查看" name="show" />
</form>
<?php if(!empty($uid) && $noShow != true):?>
<?php
    Common::loadVoComModel('ComVoComModel');
    $authKey = 'authConfig';
    $ComVoComModel = new ComVoComModel($authKey, true);
    $userConfig = $ComVoComModel->getValue();
    $auth = $userConfig[$_SESSION["CURRENT_USER"]];
    if (empty($auth)){
        $auth = include(ROOT_DIR . '/administrator/config/auth_config.php');
    }
?>
<?php
    $userchange = array(
        1 => array('title' =>  '用 户', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=userChange&uid=".$uid."&cur=userChange", 'ban' => 'userChange'),
        2 => array('title' =>  '道 具', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=userItem&uid=".$uid."&cur=userChange", 'ban' => 'userItem'),
        3 => array('title' =>  '活 动', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=activitys&uid=".$uid."&cur=userChange", 'ban' => 'activitys'),
        4 => array('title' =>  '服 装', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=clothes&uid=".$uid."&cur=userChange", 'ban' => 'clothes'),
        5 => array('title' =>  '伙 伴', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=hero&uid=".$uid."&cur=userChange", 'ban' => 'hero'),
        6 => array('title' =>  '信 物', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=herotokens&uid=".$uid."&cur=userChange", 'ban' => 'herotokens'),
        7 => array('title' =>  '知 己', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=wife&uid=".$uid."&cur=userChange", 'ban' => 'wife'),
        8 => array('title' =>  '徒 弟', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=son&uid=".$uid."&cur=userChange", 'ban' => 'son'),
        9 => array('title' =>  '卡 牌', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=userCard&uid=".$uid."&cur=userChange", 'ban' => 'userCard'),
        10 => array('title' =>  '四海奇珍', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=userBaowu&uid=".$uid."&cur=userChange", 'ban' => 'userBaowu'),
        11 => array('title' =>  '流 水', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=flow&uid=".$uid."&cur=userChange", 'ban' => 'flow'),
        12 => array('title' =>  '邮 件', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=mail&uid=".$uid."&cur=userChange", 'ban' => 'mail'),
        13 => array('title' =>  '聊 天', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=chat&uid=".$uid."&cur=userChange", 'ban' => 'chat'),
        14 => array('title' =>  '宫殿', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=club&uid=".$uid."&cur=userChange", 'ban' => 'club'),
        15 => array('title' =>  '流 水(后端)', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=flowAdmin&uid=".$uid."&cur=userChange", 'ban' => 'flowAdmin'),
        16 => array('title' =>  'IP', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=getip&uid=".$uid."&cur=userChange", 'ban' => 'getip'),
    );
?>
    <hr class="hr" />
<div class="header">
    <?php foreach ($userchange as $k => $v):?>
        <?php if(empty($auth['ban']['user'][$v['ban']])):?>
    <a  class='backGroundColor' <?php if ($_GET['act'] == $v['ban']){echo 'style="color:red;"';} ?> href="<?php echo $v['src'];?>"><?php echo $v['title'];?></a>
        <?php endif;?>
    <?php endforeach;?>
</div>
<?php endif;?>

