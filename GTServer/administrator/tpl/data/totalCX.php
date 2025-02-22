<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 3;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form action="" method="POST" id="form-search">
<table style='width: 100%;'>

<tr><tr>
<th style='text-align: right;'>缓存查询</th>
<td colspan="2" style='text-align:left;'>
    <label><input name="select_type" type="radio" value="1" <?php echo $_POST['select_type'] == 1 ? 'checked="checked"' : '';?> />缓存查询（第一次实时）</label>
    <label><input name="select_type" type="radio" value="2" <?php echo $_POST['select_type'] == 2 ? 'checked="checked"' : '';?> />实时查询</label>
    &nbsp;
    缓存KEY:<?php echo $cacheKey;?>，缓存有效期为2个小时
</td>
</tr>
<tr>
<th style='text-align: right;'>登录查询</th>
<td colspan="2" style='text-align:left;'>
    <label><input name="select_type_lc" type="radio" value="1" <?php echo $_POST['select_type_lc'] != 2 ? 'checked="checked"' : '';?> />不查询</label>
    <label><input name="select_type_lc" type="radio" value="2" <?php echo $_POST['select_type_lc'] == 2 ? 'checked="checked"' : '';?> />查询</label>
    &nbsp; (不查询情况下用户登录人数和留存为空)
</td>
</tr>
<th>日期范围</th>
<td>
<input class='Wdate' type='text' size='40' id='startTime' name='startTime'
       value='<?php echo (empty($startTime)) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $startTime);?>'
       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
        maxDate:'#F{$dp.$D(\'endTime\')}'})" />
<font> 至 </font>
<input class='Wdate' type='text' size='40' id='endTime' name='endTime'
       value='<?php echo (empty($endTime)) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $endTime);?>'
       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
        minDate:'#F{$dp.$D(\'startTime\')}'})" />
<input type="submit" value="查询">
</td>
<td><input name="excel-all" id="excel-input-all" type="hidden" value="0"><input id="excel-submit-all" type="button" value="导出各区服数据">
    | <input name="excel" id="excel-input" type="hidden" value="0"><input id="excel-submit" type="button" value="下载excel"></td>
</tr>
<tr><th>区服选择*:</th><td><input type="text" name="server" style="width: 400px;" size="120" id="title" value="<?php echo isset($_POST['server']) ? $_POST['server'] : 'all';?>">(默认all表示所有服,连续服用"-"隔开如1-20)</td></tr>
<tr>
<th style='text-align: right;'>平台渠道</th>
<td colspan="2" style='text-align:left;'>
    <input type='button' id='show' value='显示'>&nbsp;
    <hr class="hr"/>
    <div id="channel" style="display: none;">
<input type='button' id='allSelect' value='选中全部'>&nbsp;
<input type='button' id='cancelAll' value='取消全选'>&nbsp;
<input type='button' id='selectEven' value='游动全选'>&nbsp;
<!--<input type='button' id='selectOdd' value='选中偶数'>&nbsp;-->
<input type='button' id='reverseSelect' value='反向勾选'>&nbsp;
        <hr class="hr"/>
<?php
$tmp = 0;
$you = 0;
$ios = 0;
$checkboxHtml = '';
$youdongHtml = '';
$iosdongHtml = '';
$youdongPlat = include (ROOT_DIR . '/administrator/config/youdong.php');
if(!empty($platformList)){
    foreach ($platformList as $k => $v) {

        if(empty($channels)){
            $channels = array();
        }
        $isChecked = in_array($k, $channels) ? 'checked' : '';
        if (!in_array($k,$youdongPlat['android']) && !in_array($k,$youdongPlat['ios'])){
            $tmp++;
            $brSting = ($tmp%5) ? '' : '<br/>';
            $checkboxHtml .= sprintf("<input type='checkbox' name='channels[]' value='%s' %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $v, $brSting);
        }elseif(in_array($k,$youdongPlat['android'])){
            $you++;
            $youSting = ($you%5) ? '' : '<br/>';
            $youdongHtml .= sprintf("<input type='checkbox' class='youdong' name='channels[]' value='%s' %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $v, $youSting);
        }elseif(in_array($k,$youdongPlat['ios'])){
            $ios++;
            $iosSting = ($ios%5) ? '' : '<br/>';
            $iosdongHtml .= sprintf("<input type='checkbox' class='youdong' name='channels[]' value='%s' %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $v, $iosSting);
        }


    }
}
echo $checkboxHtml;
if ($youdongHtml != ''){
    echo '<hr class="hr"/>';
    echo $youdongHtml;
}
if ($iosdongHtml != ''){
    echo '<hr class="hr"/>';
    echo $iosdongHtml;
}
?>
    </div>
            </td>
        </tr>
    </table>
</form>
<hr class="hr"/>
    <form name='datalist'>
        <table style="width:100%;">
            <caption>统计信息总览</caption>
            <tbody>
            <tr>
                <th>日期</th>
                <th>新增注册</th>
                <?php if($_POST['select_type_lc'] == 2) echo '<th>登录用户</th>';?>
                <?php if($_POST['select_type_lc'] == 2) echo '<th>活跃付费</th>';?>
                <th>营收</th>
                <th>付费人数</th>
                <th>付费总人数</th>
                <th>付费笔数</th>
                <?php if($_POST['select_type_lc'] == 2) echo '<th>付费率</th>';?>
                <th>新增营收</th>
                <th>新增ARPU</th>
                <th>新增付费人数</th>
                <th>新增付费率</th>
                <th>ARPPU</th>
                <th>累计注册</th>
                <th>累计营收</th>
                <th>累计营收(美元)</th>
                <th>累计LTV</th>
                <th>美金LTV</th>
            </tr>
            <?php
            if(!empty($volist)){
                $add_reg = 0;
                $add_money = 0;
                $add_doller = 0;
                foreach ($volist as $k => $v) {
                    echo '<tr style="background-color:#f6f9f3;">';
                    echo '<td>' . $v['time'] . '</td>';
                    echo '<td>' . $v['reg_pnum'] . '</td>';
                    if($_POST['select_type_lc'] == 2) echo '<td>' . $v['login_pnum'] . '</td>';
                    if($_POST['select_type_lc'] == 2) echo '<td>' . $v['aup_rate']. '</td>';
                    echo '<td>' . $v['total_money'] . '</td>';
                    echo '<td>' . $v['total_rechange_pnum'] . '</td>';
                    echo '<td>' . $v['max_rechange_pnum'] . '</td>';
                    echo '<td>' . $v['rechange_num'] . '</td>';
                    if($_POST['select_type_lc'] == 2) echo '<td>' . $v['rechange_rate'].'%' . '</td>';
                    echo '<td>' . $v['new_money'] . '</td>';
                    echo '<td>' . ($v['new_money'] == 0 ? 0 : number_format($v['new_money']/$v['reg_pnum'],2)) . '</td>';
                    echo '<td>' . $v['new_rechange_pnum'] . '</td>';
                    echo '<td>' . $v['new_rechange_rate'].'%' . '</td>';
                    echo '<td>' . ($v['total_rechange_pnum'] == 0 ? 0 : (empty($v['total_rechange_pnum']) ? 0 :number_format($v['total_money']/$v['total_rechange_pnum'],2))) . '</td>';
                    echo '<td>' . $add_reg +=$v['reg_pnum'] . '</td>';
                    echo '<td>' . $add_money += $v['total_money'] . '</td>';
                    echo '<td>' . $add_doller +=  sprintf("%.2f", $v['total_doller']) . '</td>';
                    echo '<td>' . ($add_reg == 0 ? 0 : number_format($add_money/$add_reg,2)) . '</td>';
                    echo '<td>' . ($add_reg == 0 ? 0 : number_format($add_doller/$add_reg,2)) . '</td>';
                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>
    </form>

    <script>
        $(document).ready(function () {
            $("#excel-submit").click(function () {
                $("#excel-input").val(1);
                $("#form-search").submit();
            });
            $("#excel-submit-all").click(function () {
                $("#excel-input-all").val(1);
                if($("[name='server']").val() == ''){
                    layer.alert('请填写区服区间！');
                    return false;
                }
                $("#form-search").submit();
            });
            // 全选
            $('#allSelect').click(function(){
                $("input[name='channels[]']").attr('checked', 'true');
            });

            // 全不选
            $('#cancelAll').click(function(){
                $("input[name='channels[]']").removeAttr('checked');
            });

            //选中游动
            $('#selectEven').click(function(){
                $(".youdong").attr('checked', 'true');
            });

            //选中所有偶数
            $('#selectOdd').click(function(){
                $("input[name='channels[]']:odd").attr('checked', 'true');
            });
        // 渠道显示切换
            $('#show').click(function(){
                if ($(this).val() == "显示"){
                    $("#channel").show();
                    $(this).val("隐藏");
                }else {
                    $("#channel").hide();
                    $(this).val("显示");
                }
            });
            // 反选
            $('#reverseSelect').click(function(){
                $("input[name='channels[]']").each(function(){
                    if($(this).attr('checked')){
                        $(this).removeAttr('checked');
                    }else{
                        $(this).attr('checked', 'true');
                    }
                });
            });
        });
    </script>
