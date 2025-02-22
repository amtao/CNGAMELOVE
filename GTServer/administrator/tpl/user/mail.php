<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>
<div style="width: 100%;display: inline-block;">
<p>玩家邮件信息:</p>


<!--<form name="form1" method="post" action="">
    <input type="hidden" name="uid" value="<?php /*echo $_REQUEST['uid'];*/?>"></input>
    <p>邮件标题或内容: <input type="text" name="id" value="<?php /*echo $_REQUEST['id'];*/?>"  />
        <?php
/*        $type_arr = EmailModel::$type_name;
        echo "类型:<select name='type'><option value='' >全部</option>";
        foreach ($type_arr as $key=>$val){
            if(is_numeric($_REQUEST['type']) && $_REQUEST['type'] == $key) {
                $selected = "selected='selected'";
            }else{
                $selected = "";
            }
            echo "<option value='{$key}' {$selected} >{$val}</option>";
        }
        echo "</select>";
        */?>
        <input type="submit" value="查看" />
    </p>
</form>-->
<?php if (!empty($data)):?>
    <?php foreach($data as $k=>$e):?>
        <table style="margin-bottom:10px" class="mytable">
            <tr style="background:#acd0ff"><td colspan="2"><?php echo date('Y-m-d H:i:s',$e['fts'])?>【邮件<?php echo $e['mid']?>】</td></tr>
            <tr><td style="width: 100px;">类型：</td><td><?php echo $e['mtype'];?></td></tr>
            <tr><td style="width: 100px;">标题：</td><td><?php echo $e['mtitle'];?></td></tr>
            <tr><td style="width: 100px;">内容：</td><td><?php echo $e['mcontent'];?></td></tr>
            <tr><td style="width: 100px;">发送时间：</td><td><?php echo date("Y-m-d H:i:s", $e['fts']);?></td></tr>
            <tr><td style="width: 100px;">领取时间：</td><td><?php echo $e['rts']?date("Y-m-d H:i:s", $e['rts']):'';?></td></tr>
            <tr><td style="width: 100px;">道具：</td>
                <td><?php
                    if (!empty($e['items'])){
                        foreach($e['items'] as $key => $v){
                            if ($v['kind']==10){
                                echo '<span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block"><b style="padding-left:10px;">道 具 : </b>'.$chenghao[$v['id']]['name'].' </span><span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block;padding-left:10px;"><b style="padding-left:10px;"> 数 量 : </b>'.$v['count'].'</span>';
                            }else{
                                echo '<span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block"><b style="padding-left:10px;">道 具 : </b>'.$items[$v['id']]['name_cn'].' </span><span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block;padding-left:10px;"><b style="padding-left:10px;"> 数 量 : </b>'.$v['count'].'</span>';
                            }

                        }
                    }else{
                        echo '无';
                    }
                    ?>
                </td></tr>
            <tr><td style="width: 100px;">状态：</td><td>
                    <?php echo $e['rts']?'已读 ':'未读 ';?>
                    <?php if($e['isdel'] == 2) {?>
                        &nbsp;&nbsp;&nbsp;已删除
                    <?php }else if (empty($e['rts'])) {
                        if ($e['isdel'] != 1) {
                            ?>
                            <a style="color: red;"
                               href="?sevid=<?php echo $SevidCfg['sevid']; ?>&mod=user&act=mail&uid=<?php echo $_REQUEST['uid']; ?>&del=1&emailId=<?php echo $e['mid'];?>"> 删除</a>
                        <?php } else {
                            ?>
                            &nbsp;&nbsp;&nbsp;已删除
                        <?php }
                    }
                    ?>
                </td></tr>
        </table>
    <?php endforeach;?>
<?php endif;?>
<?php include(TPL_DIR.'footer.php');?>