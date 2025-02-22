<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr" />
<form name="chat" method="POST" action="">
    <table style="width: 100%" class="mytable">
        <tr><th colspan="6">GM关联UID(uid提交1将删除所有关联的UID)</th></tr>
        <tr>
            <th style="text-align:right;width: 200px;">UID：</th>
            <td style="text-align:left;">
                <input   type="text" id="uid" name="uids"  value=""/>
            </td>
        </tr>
        <tr>
            <th style="text-align:right;width: 200px;">关联信息</th>
            <td><div style="width: 60%">
                <?php
                foreach ($data as $key => $value){
                    echo $value.'&nbsp &nbsp'.'<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=chatSystem&key='.$key.'&delUid='.$value.'">删除 </a>  |  &nbsp &nbsp';
                }
                ?>
                    </div>
            </td>
        </tr>
        <tr><th colspan="6"><input type="submit" value="确定提交" /></th></tr>
    </table>
</form>
<hr class="hr" />
    <form name="chat" method="POST" action="">
        <table style="width: 100%">
            <tr><th colspan="6">聊天系统</th></tr>
            <tr>
                <th style="text-align:right;">UID：</th>
                <td style="text-align:left;">
                    <input   type="text" id="uid" name="uid"  value="<?php echo $uid;?>"/>
                </td>
            </tr>
            <tr>
                <th style="text-align:right;">发送内容：</th>
                <td style="text-align:left;">
                    <input  style="width: 100%" type="text" id="info" name="info" value="<?php echo $info;?>"/>
                </td>
            </tr>
            <tr><th colspan="6"><input type="submit" value="确定提交" /></th></tr>
        </table>
    </form>
    <BR>
<?php
if($chatData){
    ?>
    <table style="width: 100%" class="mytable">
        <tr>
            <th>玩家UID</th>
            <th>玩家昵称</th>
            <th>vip等级</th>
            <th>身份</th>
            <th>聊天频道</th>
            <th>内容</th>
            <th>时间</th>
            <th>操作</th>
        </tr>
        <?php foreach($chatData as $k => $val){?>
            <tr style="background-color:#f6f9f3" >
                <td style="text-align:center;"><?php echo $val['uid']; ?></td>
                <td style="text-align:center;"><?php echo $val['user']['name']; ?></td>
                <td style="text-align:center;"><?php echo $val['user']['vip']; ?></td>
                <td style="text-align:center;"><?php echo $guan[$val['user']['level']]['name']; ?></td>
                <td style="text-align:center;">
                    <?php echo $val['type'];?>
                </td>
                <td style="text-align:center;">
                    <?php echo $val['msg']; ?>
                </td>
                <td style="text-align:center;">
                    <?php echo  date("Y-m-d H:i:s",$val['time']); ?>
                </td>
                <td style="text-align:center;">
                    <?php
                        if(empty($jy_base)){
                            $jy_base = array();
                        }
                        if(empty($fh_base)){
                            $fh_base = array();
                        }
                        if (isset($sev23Model->info[$val['uid']])){
                            echo '<span style="color: red;">禁言中</span>';
                        }else{
                            echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=chatSystem&type=banTalk&fTime=1&banUid='.$val['uid'].'">禁言 </a>';
                            echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=chatSystem&type=banTalk&fTime=2&banUid='.$val['uid'].'">禁言7天 </a>';
                        }
                        if (empty($auth['ban']['information']['closure'])){
                            if (isset($sev26Model->info[$val['uid']])){
                                echo '<span style="color: red;"> 封号中</span>';
                            }else{
                                echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=chatSystem&type=closure&fTime=1&closureUid='.$val['uid'].'">封号 </a>';
                                echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=chatSystem&type=closure&fTime=2&closureUid='.$val['uid'].'">封号7天 </a>';
                            }
                        }
                        if (empty($auth['ban']['information']['sb'])){
                            if ($sev23Model->info['closure'][$val['uid']]){
                                echo '<span style="color: red;"> 封设备中</span>';
                            }else{
                                echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=chatSystem&type=sb&sbUid='.$val['uid'].'">封设备 </a>';
                            }
                        }
                    ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php  }?>