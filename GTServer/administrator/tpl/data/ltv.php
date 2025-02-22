<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 3;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form action="" method="POST" id="form-search">
<table style='width: 100%;'>
<tr>
<th>日期范围</th>
<td>
<input class='Wdate' id='keyword5' type='text'  name='beginDate' value='<?php echo $_POST['beginDate']; ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00',isShowClear:true,readOnly:true, maxDate:'<?php echo date('Y-m-d');?>'})" />
<font> 至 </font>
<input class='Wdate' id='keyword6' type='text' name='endDate' value='<?php echo $_POST['endDate']; ?>' onFocus="WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59',isShowClear:true,readOnly:true, minDate:'1900-01-01', maxDate:'%y-%M-%d'})" />
<input type="submit" value="查询">
</td>
</tr>
<tr>
<th style='text-align: center;width: 100px;'>区服</th>
<td style='text-align:left;'>
    <select name="servid" id="servid">
        <option value="0">全服</option>
        <?php foreach ($serverList as $key => $value):?>
            <option value="<?php echo $key; ?>" <?php if($_POST['servid'] == $key) echo "selected";?>><?php echo $value['id'].'区'.$value['name']['zh']; ?></option>
        <?php endforeach;?>
    </select>
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
                <input type='button' id='platform_ios' value='所有ios包'>&nbsp;
                <input type='button' id='reverseSelect' value='反向勾选'>&nbsp;
                <hr class="hr"/>
                <?php Common::loadModel('AdminModel'); AdminModel::wrap($platformList, $platformClassify, $channels);?>
            </div>
        </td>
    </tr>
    </table>
</form>
<hr class="hr"/>
    <form name='datalist'>
        <table style="width:100%;">
            <caption>ltv信息总览<span style="color: red;">缓存数据，需要实时数据请进行查询</span></caption>
            <tbody>
            <tr>
                <th style="text-align:center;">日期</th>
                <th style="text-align:center;">新建角色数</th>
                <?php
                for ($i=1;$i<=150;$i++){
//                    if ($i>60 && $i<90){
//                        continue;
//                    }
//                    if ($i>90 && $i<120){
//                        continue;
//                    }
                    if ($i>120 && $i<150){
                        continue;
                    }
                    echo ' <th style="text-align:center;">'.$i.'日</th>';
                }
                ?>
            </tr>
            <?php
            if(!empty($list)){
                foreach ($list as $k => $v) {
                    $now = strtotime(date('Y-m-d'));
                    $old = strtotime($k);
                    $times = ($now-$old)/86400;
                    $time = $times +1;
                    echo '<tr style="background-color:#f6f9f3;">';
                    echo '<td style="text-align:center;">' . $k . '</td>';
                    echo '<td style="text-align:center;">' . $v['reg_num'] . '</td>';
                    for ($i=1;$i<=150;$i++){
//                        if ($i>60 && $i<90){
//                            continue;
//                        }
//                        if ($i>90 && $i<120){
//                            continue;
//                        }
                        if ($i>120 && $i<150){
                            continue;
                        }
                        echo '<td style="text-align:center;">';
                        if ($time < $i){
                            echo '';
                        }else{
                            echo (empty($v['reg_num']) ? 0 : number_format($v['money'][$i]/$v['reg_num'],2));
                        }
                        echo '</td>';
                    }
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
        //选中游动
        $('#platform_ios').click(function(){
            $('[data-platform="ios"]').attr('checked', 'true');
        });
        //选中游动
        $('#selectEven').click(function(){
            $(".youdong").attr('checked', 'true');
        });

        //选中所有偶数
        $('#selectOdd').click(function(){
            $("input[name='channels[]']:odd").attr('checked', 'true');
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