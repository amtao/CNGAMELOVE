<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>
    <form method="post">
        <table style='width: 100%;'>
            <tbody>
            <tr>
                <th colspan="2">苹果订单查询</th>
            </tr>
            <tr>
                <th style="text-align: right;">角色ID:</th>
                <td style="text-align: left;"><input type="text"
                                                     value="<?php echo $uid?$uid:'';?>" name="uid" id="uid"></td>
            </tr>
            <tr>
                <th style="text-align: right;">选择日期:</th>
                <td style="text-align:left;">
                    <input class='Wdate' type='text' size='40' id='startTime' name='startTime'
                           value='<?php echo (empty($startTime)) ? date('Y-m-d 00:00:00') : date('Y-m-d H:i:s', $startTime);?>'
                           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						maxDate:'#F{$dp.$D(\'endTime\')}'})" />
                    &nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;

                    <input class='Wdate' type='text' size='40' id='endTime' name='endTime'
                           value='<?php echo (empty($endTime)) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $endTime);?>'
                           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						minDate:'#F{$dp.$D(\'startTime\')}'})" />
                </td>
            </tr>
            <tr>
                <th colspan="2"><input type="submit" value="确定查询"></th>
            </tr>
            </tbody>
        </table>
    </form>
<?php
if(!empty($data) && is_array($data)){
    ?>
    <table style="width: 100%">
        <tr>
            <th colspan="9">订单信息</th>
        </tr>
        <tr>
            <th>cs1</th>
            <th>cs2</th>
            <th>uid(cs4)</th>
            <th>cs5</th>
            <th>cs6</th>
            <th>cs8</th>
            <th>type</th>
            <th>时间(cs3)</th>
            <th>cs7</th>

        </tr>
        <?php foreach($data as $k => $val){?>
            <tr style="background-color:#f6f9f3">
                <td style="text-align:center;"><?php echo $val['cs1']; ?></td>
                <td style="text-align:center;"><?php echo $val['cs2']; ?></td>
                <td style="text-align:center;"><?php echo $val['cs4']; ?></td>
                <td style="text-align:center;"><?php echo $val['cs5']; ?></td>
                <td style="text-align:center;"><?php echo $val['cs6']; ?></td>
                <td style="text-align:center;"><?php echo $val['cs8']; ?></td>
                <td style="text-align:center;">
                    <?php
                    if ($val['type'] == 1){
                        echo  "lua支付开始";
                    }elseif($val['type'] == 2){
                        echo  "c++支付中回回调";
                    }elseif($val['type'] == 3){
                        echo  "c++支付成功回调";
                    }elseif($val['type'] == 4){
                        echo  "c++支付成功回调，无lua回调函数";
                    }elseif($val['type'] == 5){
                        echo  "c++支付成功回调，无lua回调函数，有receipt";
                    }elseif($val['type'] == 6){
                        echo  "c++支付失败回调,第二个参数是PayFailedErr_1(拼接错误id，1：支付失败，2：支付取消 ";
                    }elseif($val['type'] == 7){
                        echo  "c++支付失败回调，无lua回调函数";
                    }elseif($val['type'] == 8){
                        echo  "c++支付restored回调";
                    }elseif($val['type'] == 9){
                        echo  "lua支付成功";
                    }elseif($val['type'] == 10){
                        echo  "lua支付失败";
                    }elseif($val['type'] == 11){
                        echo  "支付漏单，重新登录，是否有旧单信息 ";
                    }elseif($val['type'] == 12){
                        echo  "支付漏单，重新登录，漏单重新支付成功";
                    }
                    echo '('.$val['type'].')';
                    ?></td>
                <td style="text-align:center;"><?php echo date("Y-m-d H:i:s", $val['cs3']); ?></td>
                <td style="text-align:center;"><?php echo $val['cs7']; ?></td>
            </tr>
        <?php } ?>
    </table>
<?php  }?>