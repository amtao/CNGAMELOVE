<?php
$thisHeaderList = array(
    array('title'=>'审核通过列表', 'act'=>'passList', 'auditType'=>1, 'src' => '?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=passList&auditType=1'),
    array('title'=>'等待审核列表', 'act'=>'passList', 'auditType'=>0, 'src' =>'?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=passList&auditType=0'),
    array('title'=>'审核不过列表', 'act'=>'passList', 'auditType'=>2, 'src' =>'?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=passList&auditType=2'),
    array('title'=>'添加活动', 'act'=>'selectTemplate', 'src' =>'?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=selectTemplate'),
    array('title'=>'添加活动（通过id）', 'act'=>'selectActivityById', 'src' =>'?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=selectActivityById'),
    array('title'=>'批量添加活动', 'act'=>'batAddGameAct', 'src' =>'?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=batAddGameAct'),
    array('title'=>'模板列表', 'act'=>'templateList', 'src' =>'?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=templateList'),
    array('title'=>'添加模板', 'act'=>'addTemplate', 'src' =>'?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=addTemplate'),
    array('title'=>'导入模板', 'act'=>'importTemplate', 'src' =>'?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=importTemplate'),
    array('title'=>'活动预览', 'act'=>'effectiveList', 'src' =>'?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=effectiveList'),
    array('title'=>'预览设置', 'act'=>'newTime', 'src' =>'?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=newTime'),
    array('title'=>'专服配置', 'act'=>'serverConfig', 'src' =>'?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=serverConfig'),
    array('title'=>'导入活动', 'act'=>'importActivity', 'src' =>'?sevid='.$SevidCfg['sevid'].'&mod=gameAct&act=importActivity'),
);
?>
<hr class="hr"/>
<div class="header">
    <?php if(SERVER_ID == 999 || SERVER_ID == 1):?>
        <?php foreach ($thisHeaderList as $vv):?>
            <a class='backGroundColor' <?php
            $condition = $_GET['act'] == $vv['act'];
            if (isset($vv['auditType'])) {
                $condition = $condition && $_GET['auditType'] == $vv['auditType'];
            }
            if ($condition){echo 'style="color:red;"';}
            ?> href="<?php echo $vv['src'];?>"><?php echo $vv['title'];?></a>
        <?php endforeach;?>
    <?php endif;?>
</div>
<hr class="hr"/>