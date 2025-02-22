<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 3;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form action="" method="POST" id="form-search">
<table style='width: 100%;'>
<tr>
<th>日期范围</th>
<td>
<input class='Wdate' id='keyword5' type='text'  name='startTime' value='<?php echo $_POST['startTime']; ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00',isShowClear:true,readOnly:true, maxDate:'<?php echo date('Y-m-d');?>'})" />
<font> 至 </font>
<input class='Wdate' id='keyword6' type='text' name='endTime' value='<?php echo $_POST['endTime']; ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59',isShowClear:true,readOnly:true, minDate:'1900-01-01', maxDate:'%y-%M-%d'})" />
<input type="submit" value="查询">
</td>
</tr>
<tr>
    <th style='text-align: center;width: 100px;'>平台渠道</th>
    <td colspan="2" style='text-align:left;'>
        <input type='button' id='show' value='显示'>&nbsp;
        <hr class="hr"/>
        <div id="channel" style="display: none;">
            <input type='button' id='allSelect' value='选中全部'>&nbsp;
            <input type='button' id='cancelAll' value='取消全选'>&nbsp;
            <?php
            $button = array('duweiming', 'liangmeimei', 'wumin', 'sulu');
            if (in_array($_SESSION["CURRENT_USER"], $button)):?>
                <input type='button' id='selectEven' value='其他包'>&nbsp;
                <input type='button' id='meimei' value='美美包'>&nbsp;
                <input type='button' id='sulu' value='苏璐包'>&nbsp;
                <input type='button' id='wumin' value='吴禹包'>&nbsp;
            <?php endif;?>
            <input type='button' id='platform_ios' value='所有ios包'>&nbsp;
            <!--<input type='button' id='selectOdd' value='选中偶数'>&nbsp;-->
            <input type='button' id='reverseSelect' value='反向勾选'>&nbsp;
            <hr class="hr"/>
            <?php
            $tmp = 0;
            $you = 0;
            $ios = 0;
            $meimei = 0;
            $sulu = 0;
            $wumin = 0;
            $checkboxHtml = '';
            $youdongHtml = '';
            $iosdongHtml = '';
            $meimeiHtml = '';
            $suluHtml = '';
            $wuminHtml = '';
            $youdongPlat = include (ROOT_DIR . '/administrator/config/youdong.php');
            if(!empty($platformList)){
                foreach ($platformList as $k => $v) {

                    if(empty($channels)){
                        $channels = array();
                    }
                    $isChecked = in_array($k, $channels) ? 'checked' : '';
                    if (!in_array($k,$youdongPlat['android']) && !in_array($k,$youdongPlat['ios'])){
                        if ($platformClassify[$k] != 'ios'){
                            $platform_ios = "";
                        }else{
                            $platform_ios = "data-platform='ios'";
                        }
                        $tmp++;
                        $brSting = ($tmp%5) ? '' : '<br/>';
                        $checkboxHtml .= sprintf("<input type='checkbox' name='channels[]' value='%s' %s %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $platform_ios , $v, $brSting);
                    }elseif(in_array($k,$youdongPlat['android'])){
                        if ($platformClassify[$k] != 'ios'){
                            $platform_ios = "";
                        }else{
                            $platform_ios = "data-platform='ios'";
                        }
                        $you++;
                        $youSting = ($you%5) ? '' : '<br/>';
                        $youdongHtml .= sprintf("<input type='checkbox' class='youdong' name='channels[]' value='%s' %s %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $platform_ios , $v, $youSting);
                    }elseif(in_array($k,$youdongPlat['ios'])){
                        if ($platformClassify[$k] != 'ios'){
                            $platform_ios = "";
                        }else{
                            $platform_ios = "data-platform='ios'";
                        }
                        $ios++;
                        $iosSting = ($ios%5) ? '' : '<br/>';
                        $iosdongHtml .= sprintf("<input type='checkbox' class='soeasy' name='channels[]' value='%s' %s %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $platform_ios , $v, $iosSting);
                    }

                    if(!empty($youdongPlat['wumin']) && in_array($k,$youdongPlat['wumin'])){
                        if ($platformClassify[$k] != 'ios'){
                            $platform_ios = "";
                        }else{
                            $platform_ios = "data-platform='ios'";
                        }
                        $wumin++;
                        $wuminSting = ($wumin%5) ? '' : '<br/>';
                        $wuminHtml .= sprintf("<input type='checkbox' class='wumin' name='channels[]' value='%s' %s %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked,$platform_ios , $v, $wuminSting);
                    }
                    if(!empty($youdongPlat['meimei']) &&  in_array($k,$youdongPlat['meimei'])){
                        if ($platformClassify[$k] != 'ios'){
                            $platform_ios = "";
                        }else{
                            $platform_ios = "data-platform='ios'";
                        }
                        $meimei++;
                        $meimeiSting = ($meimei%5) ? '' : '<br/>';
                        $meimeiHtml .= sprintf("<input type='checkbox' class='meimei' name='channels[]' value='%s' %s %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked,$platform_ios , $v, $meimeiSting);
                    }
                    if(!empty($youdongPlat['sulu']) && in_array($k,$youdongPlat['sulu'])){
                        if ($platformClassify[$k] != 'ios'){
                            $platform_ios = "";
                        }else{
                            $platform_ios = "data-platform='ios'";
                        }
                        $sulu++;
                        $suluSting = ($sulu%5) ? '' : '<br/>';
                        $suluHtml .= sprintf("<input type='checkbox' class='sulu' name='channels[]' value='%s' %s %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked,$platform_ios , $v, $suluSting);
                    }

                }
            }
            echo $checkboxHtml;
            if ($suluHtml != ''){
                echo '<hr class="hr"/>';
                echo $suluHtml;
            }
            if ($meimeiHtml != ''){
                echo '<hr class="hr"/>';
                echo $meimeiHtml;
            }
            if ($youdongHtml != ''){
                echo '<hr class="hr"/>';
                echo $youdongHtml;
            }

            if ($iosdongHtml != ''){
                echo '<hr class="hr"/>';
                echo $iosdongHtml;
            }
            if ($wuminHtml != ''){
                echo '<hr class="hr"/>';
                echo $wuminHtml;
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
                    <th>45日留存</th>
                    <th>60日留存</th>
                    <th>90日留存</th>
                </tr>
            <?php
            if(!empty($volist)){
                foreach ($volist as $k => $v) {
                    if(!empty($v['r']) && !empty($v['l'])){
                        echo '<tr style="background-color:#f6f9f3;">';
                        echo '<td>' . $v['t'] . '</td>';
                        echo '<td>' . $v['r'] . '</td>';
                        echo '<td>' . $v['l'] . '</td>';
                        echo '<td>' . $v['d1'].'('.(empty($v['r']) ? 0 :number_format($v['d1']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d2'].'('.(empty($v['r']) ? 0 :number_format($v['d2']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d3'].'('.(empty($v['r']) ? 0 :number_format($v['d3']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d4'].'('.(empty($v['r']) ? 0 :number_format($v['d4']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d5'].'('.(empty($v['r']) ? 0 :number_format($v['d5']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d6'].'('.(empty($v['r']) ? 0 :number_format($v['d6']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d7'].'('.(empty($v['r']) ? 0 :number_format($v['d7']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d8'].'('.(empty($v['r']) ? 0 :number_format($v['d8']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d9'].'('.(empty($v['r']) ? 0 :number_format($v['d9']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d10'].'('.(empty($v['r']) ? 0 :number_format($v['d10']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d11'].'('.(empty($v['r']) ? 0 :number_format($v['d11']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d12'].'('.(empty($v['r']) ? 0 :number_format($v['d12']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d13'].'('.(empty($v['r']) ? 0 :number_format($v['d13']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d14'].'('.(empty($v['r']) ? 0 :number_format($v['d14']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d15'].'('.(empty($v['r']) ? 0 :number_format($v['d15']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d16'].'('.(empty($v['r']) ? 0 :number_format($v['d16']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d17'].'('.(empty($v['r']) ? 0 :number_format($v['d17']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d18'].'('.(empty($v['r']) ? 0 :number_format($v['d18']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d19'].'('.(empty($v['r']) ? 0 :number_format($v['d19']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d20'].'('.(empty($v['r']) ? 0 :number_format($v['d20']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d21'].'('.(empty($v['r']) ? 0 :number_format($v['d21']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d22'].'('.(empty($v['r']) ? 0 :number_format($v['d22']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d23'].'('.(empty($v['r']) ? 0 :number_format($v['d23']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d24'].'('.(empty($v['r']) ? 0 :number_format($v['d24']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d25'].'('.(empty($v['r']) ? 0 :number_format($v['d25']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d26'].'('.(empty($v['r']) ? 0 :number_format($v['d26']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d27'].'('.(empty($v['r']) ? 0 :number_format($v['d27']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d28'].'('.(empty($v['r']) ? 0 :number_format($v['d28']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d29'].'('.(empty($v['r']) ? 0 :number_format($v['d29']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d44'].'('.(empty($v['r']) ? 0 :number_format($v['d44']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d59'].'('.(empty($v['r']) ? 0 :number_format($v['d59']*100/$v['r'],2)).'%)' . '</td>';
                        echo '<td>' . $v['d89'].'('.(empty($v['r']) ? 0 :number_format($v['d89']*100/$v['r'],2)).'%)' . '</td>';
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
//选中游动
        $('#platform_ios').click(function(){
            $('[data-platform="ios"]').attr('checked', 'true');
        });
        // 全不选
        $('#cancelAll').click(function(){
            $("input[name='channels[]']").removeAttr('checked');
        });
//选中游动
        $('#meimei').click(function(){
            $(".meimei").attr('checked', 'true');
        });
        //选中游动
        $('#wumin').click(function(){
            $(".wumin").attr('checked', 'true');
        });
        //选中游动
        $('#platform_ios').click(function(){
            $('[data-platform="ios"]').attr('checked', 'true');
        });
        //选中游动
        $('#sulu').click(function(){
            $(".sulu").attr('checked', 'true');
        });
        //选中所有偶数
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
