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
    <form name="chat" method="POST" action="">
        <table style="width: 100%">
            <tr><th colspan="6">后台操作日志查询</th></tr>
            <tr style="background-color:#f6f9f3;">
                <th style="text-align:right;">操作人：</th>
                <td>
                    <span>
                        <select name="admin" style="width: 100px;margin: 2px;">
                            <option value="">请选择操作人</option>
                            <?php foreach ($adminName as $key => $value): ?>
                                <option <?php if ($_POST['admin'] == $key) { echo 'selected="selected"';} ?> value="<?php echo $key; ?>"><?php echo $value['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </span>
                </td>
            </tr>
            <tr style="background-color:#f6f9f3;">
                <th style="text-align:right;">模块：</th>
                <td style="text-align:left;">
                    <input type="text" name="models" value="<?php echo $_POST['models']; ?>"/>
                </td>
            </tr>
            <tr style="background-color:#f6f9f3;">
                <th style="text-align:right;">控制器：</th>
                <td style="text-align:left;">
                    <input type="text" name="controls" value="<?php echo $_POST['controls']; ?>"/>
                </td>
            </tr>
            <tr style="background-color:#f6f9f3;">
                <th style="text-align:right;">UID：</th>
                <td style="text-align:left;">
                    <input type="text" name="user" value="<?php echo $_POST['user']; ?>"/>
                </td>
            </tr>
            <tr style="background-color:#f6f9f3;">
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
<hr class="hr">
<?php
if($data){
    ?>
    <table style="width: 100%;line-height: 26px;">
        <tr>
            <th style="text-align:center;width:100px;">操作人</th>
            <th style="text-align:center;width:100px;">模块</th>
            <th style="text-align:center;width:100px;">控制器</th>
            <th style="text-align:center;width:500px;overflow: hidden;">参数</th>
            <th style="width: 150px;">时间</th>
            <th style="width: 150px;">IP</th>
        </tr>
        <?php foreach($data as $k => $val){?>
            <tr style="background-color:#f6f9f3;">
                <td style="text-align:center;"><?php echo $adminName[$val['admin']]['name']?$adminName[$val['admin']]['name']:$val['admin']; ?></td>
                <td style="text-align:center;"><?php echo $val['model']; ?></td>
                <td style="text-align:center;"><?php echo $val['control']; ?></td>
                <td style="text-align:center;width: 60%;overflow: hidden;"><?php print_r(json_decode($val['data'], true)); ?></td>
                <td style="text-align:center;"><?php echo date('Y-m-d H:i:s', $val['time']); ?></td>
                <td style="text-align:center;"><?php echo $val['ip']; ?></td>
            </tr>
        <?php } ?>
    </table>
<?php  }?>