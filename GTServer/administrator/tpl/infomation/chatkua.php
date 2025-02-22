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
    <BR>
    <form name="chat" method="POST" action="">
        <table style="width: 100%">
            <tr><th colspan="6">玩家聊天记录</th></tr>
            <tr>
                <th style="text-align: right;">选择日期:</th>
                <td style="text-align:left;">
                    <input class='Wdate' type='text' size='40' id='startTime' name='startTime'
                           value='<?php echo (empty($startTime)) ? date('Y-m-d 00:00:00') : date('Y-m-d H:i:s', $startTime);?>'
                           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						maxDate:'#F{$dp.$D(\'endTime\')}'})" />
                    &nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;

                    <input class='Wdate' type='text' size='40' id='endTime' name='endTime'
                           value='<?php echo (empty($endTime)) ? date('Y-m-d 23:59:59') : date('Y-m-d 23:59:59', $endTime);?>'
                           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						minDate:'#F{$dp.$D(\'startTime\')}'})" />
                </td>
            </tr>
            <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
        </table>
    </form>
    <BR>
<?php if ($_SESSION['CURRENT_USER'] == 'chenhuiyun' || $_SESSION['CURRENT_USER'] == 'liaozhichao' || $_SESSION['CURRENT_USER'] == 'yangjinghong' || $_SESSION['CURRENT_USER'] == 'caipeng'){
    echo 'key : '.$chaKey.'<br/>  msgKey : '.$msgKey;
}?>
<?php
if($data){
    ?>
    <table style="width: 100%"  class="mytable">
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
        <?php foreach($data as $k => $val){?>
            <tr style="background-color:#f6f9f3">
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
                        if ($sev39Model->info[$val['uid']]){
                            echo '<span style="color: red;">禁言中</span>';
                        }else{
                            echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=chatkua&type=banTalk&banUid='.$val['uid'].'&content='.$val['msg'].'">禁言 </a> | <a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=chatkua&type=banTalk&banUid='.$val['uid'].'&status=1&content='.$val['msg'].'">强制禁言 </a>';
                        }
                        if (empty($auth['ban']['information']['closure'])){
                            if ($sev26Model->info[$val['uid']]){
                                echo '<span style="color: red;"> 封号中</span>';
                            }else{
                                echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=chatkua&type=closure&closureUid='.$val['uid'].'">封号 </a>';
                            }
                        }
                        if (empty($auth['ban']['information']['sb'])){
                            echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=infomation&act=chatkua&type=sb&sbUid='.$val['uid'].'">封设备 </a>';
                        }
                    ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php  }?>