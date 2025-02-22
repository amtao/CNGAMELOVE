<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
    <script type="text/javascript" src="<?php echo JS_DIR;?>js/My97DatePicker/WdatePicker.js"></script>
    <script type="text/javascript">
        function checkForm(){
            if(document.loginRegisterAll.startTime.value.length == 0){
                alert("起始时间不能为空,请输入起始时间");
                document.loginRegisterAll.startTime.focus();
                return false;
            }
            if(document.loginRegisterAll.endTime.value.length == 0){
                alert("结束时间不能为空,请输入结束时间");
                document.loginRegisterAll.endTime.focus();
                return false;
            }
            var todayTime = "<?php echo date("Y-m-d");?>";
            var todayTimeJsArray = todayTime.split("-");
            var todayTimeJsMake = new Date(todayTimeJsArray[0],todayTimeJsArray[1],todayTimeJsArray[2]);
            var todayTimeJs = todayTimeJsMake.getTime();

            var startTime = document.loginRegisterAll.startTime.value;
            var startTimeJsArray = startTime.split("-");
            var startTimeJsMake = new Date(startTimeJsArray[0],startTimeJsArray[1],startTimeJsArray[2]);
            var startTimeJs = startTimeJsMake.getTime();

            var endTime = document.loginRegisterAll.endTime.value;
            var endTimeJsArray = endTime.split("-");
            var endTimeJsMake = new Date(endTimeJsArray[0],endTimeJsArray[1],endTimeJsArray[2]);
            var endTimeJs = endTimeJsMake.getTime();

            if( todayTimeJs == startTimeJs ){
                //alert("起始时间不能为今日时间,请重新输入");
                //document.loginRegisterAll.startTime.focus();
                //return false;
            }

            if( todayTimeJs == endTimeJs ){
                //alert("结束时间不能为今日时间,请重新输入");
                //document.loginRegisterAll.endTime.focus();
                //return false;
            }

            if( endTimeJs < startTimeJs ){
                alert("结束时间不能小于起始时间,请重新输入");
                document.loginRegisterAll.endTime.focus();
                return false;
            }
            if( (endTimeJs - startTimeJs)/(24*3600*1000) > 7 ){
                //alert("只支持查询7天的数据,请重新选择时间");
                //return false;
            }
            return true;
        }
        $(function(){
            $(".showa").click(function(){
                var cc = $(this).attr("value");
                $(".c-"+cc).toggle();
            });
        });
    </script>
<hr class="hr" />
<form name="chat" method="POST" action="">
    <table style="width: 100%">
        <tr><th colspan="6">玩家聊天记录</th></tr>
        <tr>
            <th style="text-align: right;">区服:(全服all,区间如1-2)</th>
            <td style="text-align:left;">
                <input type="text" name="server" value="<?php echo $_POST['server']?$_POST['server']:''?>">
            </td>
        </tr>
        <tr>
            <th style="text-align: right;">玩家uid:</th>
            <td style="text-align:left;">
                <input type="text" name="uid" value="<?php echo $_POST['uid']?$_POST['uid']:''?>">
            </td>
        </tr>
        <tr id="toUid"<?php if($_POST['type'] != 5){echo 'style="display: none"'; }?>>
            <th style="text-align: right;">私聊玩家uid:</th>
            <td style="text-align:left;">
                <input type="text" name="toUid" value="<?php echo $_POST['toUid']?$_POST['toUid']:''?>">
            </td>
        </tr>
        <tr>
            <th style="text-align: right;">频道:</th>
            <td style="text-align:left;">
               <select name="type">
                   <option value="1" <?php if($_POST['type'] == 1){echo 'selected ="selected"';}?>>世界聊天</option>
                   <option value="2" <?php if($_POST['type'] == 2){echo 'selected ="selected"';}?>>跨服聊天</option>
                   <option value="3" <?php if($_POST['type'] == 3){echo 'selected ="selected"';}?>>工会聊天</option>
                   <option value="4" <?php if($_POST['type'] == 4){echo 'selected ="selected"';}?>>跨服宫斗聊天</option>
                   <option value="5" <?php if($_POST['type'] == 5){echo 'selected ="selected"';}?>>私聊</option>
               </select>
            </td>
        </tr>
        <tr id="club" <?php if($_POST['type'] != 3){echo 'style="display: none"'; }?>>
            <th style="text-align: right;">工会ID:</th>
            <td style="text-align:left;">
                <input type="text" name="gid" value="<?php echo $_POST['gid']?$_POST['gid']:''?>">
            </td>
        </tr>
        <tr>
            <th style="text-align: right;">选择日期:</th>
            <td style="text-align:left;">
                <input class='Wdate' type='text' size='40' id='startTime' name='startTime'
                       value='<?php echo (empty($startTime)) ? date('Y-m-d 00:00:00') : $startTime;?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
                    maxDate:'#F{$dp.$D(\'endTime\')}'})" />
                &nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;

                <input class='Wdate' type='text' size='40' id='endTime' name='endTime'
                       value='<?php echo (empty($endTime)) ? date('Y-m-d 23:59:59') : $endTime;?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
                    minDate:'#F{$dp.$D(\'startTime\')}'})" />
            </td>
        </tr>
        <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
    </table>
</form>
<BR>
<?php
if($chatData){
    ?>
    <table style="width: 100%" class="mytable">
        <tr>
            <th>玩家UID</th>
            <?php if ($_POST['type'] == 3):?>
                <th>工会id</th>
            <?php endif;?>
            <th>玩家昵称</th>
            <th>vip等级</th>
            <th>身份</th>
            <th>聊天频道</th>
            <th>内容</th>
            <th>时间</th>
            <th>操作</th>
        </tr>
        <?php foreach($chatData as $k => $val){
            if (empty($val['name'])){
                $userInfo = new UserModel($val['uid']);
            }
            ?>
            <tr style="background-color:#f6f9f3;">
                <td style="text-align:center;"><?php echo $val['uid']; ?></td>
            <?php if ($_POST['type'] == 3):?>
                <td style="text-align:center;"><?php echo $val['other']; ?></td>
            <?php endif;?>
                <td style="text-align:center;"><?php echo $val['name']?$val['name']:$userInfo->info['name']; ?></td>
                <td style="text-align:center;"><?php if (!empty($userInfo->info['vip'])){echo $val['vip']?$val['vip']:$userInfo->info['vip']; }else{ echo $val['vip']?$val['vip']:0;}?></td>
                <td style="text-align:center;"><?php echo $guan[$val['level']]['name']?$guan[$val['level']]['name']:$guan[$userInfo->info['level']]['name']; ?></td>
                <td style="text-align:center;">
                    <?php echo $val['type'];?>
                </td>
                <td style="text-align:center;">
                    <?php echo $val['content']; ?>
                </td>
                <td style="text-align:center;">
                    <?php echo  date("Y-m-d H:i:s",$val['time']); ?>
                </td>
                <td style="text-align:center;" >
                    <?php
                        if (in_array($val['uid'],$sev23)){
                            echo '<span style="color: red;">禁言中 | </span>';
                        }else{
                            echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=chat&type=banTalk&banUid='.$val['uid'].'&content='.$val['content'].'">禁言 </a> | <a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=chat&type=banTalk&banUid='.$val['uid'].'&status=1&content='.$val['content'].'">永久禁言 </a>';
                        }
                    
                        if (empty($auth['ban']['information']['closure'])){
                            if (in_array($val['uid'],$sev26)){
                                echo '<span style="color: red;"> 封号中 | </span>';
                            }else{
                                echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=chat&type=closure&closureUid='.$val['uid'].'">封号 </a>';
                            }
                        }
                        if (empty($auth['ban']['information']['sb'])){
                            echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=chat&type=sb&sbUid='.$val['uid'].'">封设备 </a>';
                        }
                    ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php  }?>
<script>
    $(document).ready(function () {
        $('[name="type"]').on('change',function () {
            var type = $(this).val();
            if (type == 3){
                $("#club").show();
            }else{
                $("#club").hide();
            }
            if (type == 5){
                $("#toUid").show();
            }else{
                $("#toUid").hide();
            }
        });
    });
</script>
