<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php'); ?>
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
        <table>
            <tr><th colspan="6">条件</th></tr>
            <tr>
                <th style="text-align:right;">选择时间：</th>
                <td style="text-align:left;">
                    <input class='Wdate' type='text' size='40' id='startTime' name='startTime'
                           value='<?php echo $startTime;?>'
                           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						maxDate:'#F{$dp.$D(\'endTime\')}'})" />
                    &nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;

                    <input class='Wdate' type='text' size='40' id='endTime' name='endTime'
                           value='<?php echo $endTime;?>'
                           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						minDate:'#F{$dp.$D(\'startTime\')}'})" />
                </td>
            </tr>
            <tr>
                <th style="text-align:right;">平台分类：</th>
                <td style="text-align:left;">
                    <select name="platform">
                        <option value="all" <?php echo $_REQUEST['platform'] == 'all'?'selected="selected"':'';?>>所有</option>
                        <option value="android" <?php echo $_REQUEST['platform'] == 'android'?'selected="selected"':'';?>>安卓</option>
                        <option value="ios" <?php echo $_REQUEST['platform'] == 'ios'?'selected="selected"':'';?>>苹果</option>
                    </select>
                </td>
            </tr>
            <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
        </table>
    </form>
    <BR>
<?php
if($data){
    ?>
    <table>
        <tr>
            <th>平台</th>
            <th>注册人数</th>
            <th>充值</th>
            <?php if ($display != 1):?>
                <th>登录人数</th>
            <?php endif;?>
        </tr>
        <tr>
            <th>排序</th>
            <form name="chat" method="POST" action="">
            <th>
                <input name="startTime" style="display: none;" value="<?php echo $startTime;?>">
                <input name="endTime"  style="display: none;" value="<?php echo $endTime;?>">
                <input name="platform" style="display: none;" value="<?php echo $_REQUEST['platform'];?>">
                <input name="sort" type="submit" value="按注册人数倒序">
            </th>
            </form>
            <form name="chat" method="POST" action="">
            <th>
                <input name="startTime" style="display: none;" value="<?php echo $startTime;?>">
                <input name="endTime"  style="display: none;" value="<?php echo $endTime;?>">
                <input name="platform" style="display: none;" value="<?php echo $_REQUEST['platform'];?>">
                <input name="sort" type="submit" value="按充值倒序">
            </th>
            </form>
            <?php if ($display != 1):?>
                    <form name="chat" method="POST" action="">
                        <th>
                            <input name="startTime" style="display: none;" value="<?php echo $startTime;?>">
                            <input name="endTime"  style="display: none;" value="<?php echo $endTime;?>">
                            <input name="platform" style="display: none;" value="<?php echo $_REQUEST['platform'];?>">
                            <input name="sort" type="submit" value="按登录倒序">
                        </th>
                    </form>
            <?php endif;?>
        </tr>
        <tr style="background-color:#f6f9f3">
            <th>总计</th>
            <th style="text-align: center;"><?php echo $totalRegister; ?></th>
            <th style="text-align: center;"><?php echo $totalMoney; ?></th>
            <?php if ($display != 1):?>
                    <th style="text-align: center;"><?php echo $totalLogin; ?></th>
            <?php endif;?>
        </tr>
        <?php foreach($data as $k => $val){?>
            <?php if (!empty($k)):?>
            <tr style="background-color:#f6f9f3">
                <td style="text-align:center;"><?php echo $platformList[$k]?$platformList[$k]:$k; ?></td>
                <td style="text-align:center;"><?php echo $val['register']; ?></td>
                <td style="text-align:center;"><?php echo $val['total']; ?></td>
                <?php if ($display != 1):?>
                    <td style="text-align:center;"><?php echo $val['totalLogin']; ?></td>
                <?php endif;?>
            </tr>
            <?php endif;?>
        <?php } ?>
    </table>
<?php  }?>