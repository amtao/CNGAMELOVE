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
        <hr class="hr"/>
         <table style="width:100%;">
            <caption>留存详情</caption>
            <tbody>
            <tr>
                <th>日期</th>
                <th>新增注册</th>
                <th>登录用户</th>
                <th>次日留存</th>
                <th>三日留存</th>
                <th>四日留存</th>
                <th>五日留存</th>
                <th>六日留存</th>
                <th>七日留存</th>
                <th>八日留存</th>
                <th>九日留存</th>
                <th>10日留存</th>
                <th>11日留存</th>
                <th>12日留存</th>
                <th>13日留存</th>
                <th>14日留存</th>
                <th>15日留存</th>
                <th>16日留存</th>
                <th>17日留存</th>
                <th>18日留存</th>
                <th>19日留存</th>
                <th>20日留存</th>
                <th>21日留存</th>
                <th>22日留存</th>
                <th>23日留存</th>
                <th>24日留存</th>
                <th>25日留存</th>
                <th>26日留存</th>
                <th>27日留存</th>
                <th>28日留存</th>
                <th>29日留存</th>
                <th>30日留存</th>
            </tr>
            <?php
            if(!empty($volist)){
                foreach ($volist as $k => $v) {
                    if(!empty($v['reg_pnum']) && !empty($v['login_pnum'])){
                        echo '<tr style="background-color:#f6f9f3;">';
                        echo '<td>' . $v['time'] . '</td>';
                        echo '<td>' . $v['reg_pnum'] . '</td>';
                        echo '<td>' . $v['login_pnum'] . '</td>';
                        echo '<td>' . $v['two_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['two_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['three_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['three_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day4_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day4_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['five_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['five_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day6_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day6_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['week_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['week_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day8_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day8_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day9_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day9_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day10_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day10_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day11_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day11_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day12_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day12_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day13_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day13_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['two_week_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['two_week_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day15_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day15_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day16_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day16_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day17_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day17_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day18_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day18_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day19_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day19_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day20_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day20_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day21_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day21_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day22_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day22_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day23_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day23_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day24_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day24_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day25_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day25_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day26_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day26_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day27_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day27_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day28_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day28_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day29_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day29_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';
                        echo '<td>' . $v['day30_pnum'].'('.(empty($v['reg_pnum']) ? 0 :number_format($v['day30_pnum']*100/$v['reg_pnum'],2)).'%)' . '</td>';


                        echo '</tr>';
                    }

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
