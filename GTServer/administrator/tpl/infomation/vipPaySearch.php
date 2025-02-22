<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form id="form-search" method="POST" action="" >
    <table style="width: 100%">
        <tr><th colspan="6">活跃公会用户统计</th></tr>
        <tr>
            <th>日期范围*:</th>
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
            </td>
        </tr>
        <tr>
            <th>VIP选择*:</th>
            <td>
                <input type="text" name="vips" style="width: 400px;" size="120" id="title" value="<?php echo isset($_POST['vips']) ? $_POST['vips'] : 'all';?>">(默认all表示所有等级,连续等级用"-"隔开如1-10)
            </td>
        </tr>
        <tr>
            <th>区服选择*:</th>
            <td>
                <input type="text" name="server" style="width: 400px;" size="120" id="title" value="<?php echo isset($_POST['server']) ? $_POST['server'] : 'all';?>">(默认all表示所有服,连续服用"-"隔开如1-20)
            </td>
        </tr>
        <tr><th>道具类型*</th>
            <td>
                <select class="input" name="item">
                    <?php foreach ($item as $key => $value):?>
                        <option value="<?php echo $key; ?>"><?php echo $value['name']; ?></option>
                    <?php endforeach;?>
                </select>
            </td>
        </tr>
        <tr>
            <th>平台渠道*:</th>
            <td colspan="2" style='text-align:left;'>
                <input type='button' id='show' value='显示'>&nbsp;
                <hr class="hr"/>
                <div id="channel" style="display: none;">
                    <input type='button' id='allSelect' value='选中全部'>&nbsp;
                    <input type='button' id='cancelAll' value='取消全选'>&nbsp;
                    <input type='button' id='selectEven' value='游动全选'>&nbsp;
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
        <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
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

<hr class="hr" />
<?php
?>
<table>
    <tr>
        <th>VIP等级</th>
        <th>VIP人数</th>
        <th>充值笔数</th>
        <th>充值金额</th>
    </tr>
    <?php foreach ($vipList as $key => $value): ?>
    <tr>
        <td style="text-align:center;"><?php echo $value['vip'];?></td>
        <td style="text-align:center;"><?php echo $value['vipPeople'];?></td>
        <td style="text-align:center;"><?php echo $value['payCount'];?></td>
        <td style="text-align:center;"><?php echo $value['payMoney'];?></td>
    </tr>
    <?php endforeach;?>
</table>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
