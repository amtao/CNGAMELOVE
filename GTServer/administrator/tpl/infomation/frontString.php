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
    <form method="post">
    <table>
        <tr>
            <th style="width: 80%;">参数</th>
            <th>日期</th>
        </tr>
    <?php
    if($data){
        ?>
        <?php foreach($data as $k => $val){?>
            <tr style="background-color:#f6f9f3;">
                <td style="text-align:center;"><?php echo $val['string']; ?></td>
                <td style="text-align:center;"><?php echo date("Y-m-d H:i:s",$val['time']); ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td colspan="2"><input type="submit" value="删除5天前的所有数据"></td>
        </tr>
    <?php  }?>
    </table>
    </form>
