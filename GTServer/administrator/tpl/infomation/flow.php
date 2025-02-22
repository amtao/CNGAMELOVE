<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<style type="text/css">
    .span{border:1px solid grey;width:60px;height: 30px;padding: 1px 3px;}
</style>
<?php include(TPL_DIR.'zi_header.php');?>
    <script>
        function itemSelect(){
            var obj = document.getElementById("ftype");
            if (obj.value == "5")
            {
                fitem.disabled = false;
            } else {
                fitem.disabled = true;
            }
        }
    </script>

    <form method="post">
        <table style='width: 100%;'>
            <tbody>
            <tr>
                <th colspan="2">玩家流水查询</th>
            </tr>
            <tr>
                <th style="text-align: right;">角色ID:</th>
                <td style="text-align: left;"><input type="text"
                                                     value="<?php echo $uid?$uid:'';?>" name="uid" id="uid"></td>
            </tr>
            <tr>
                <th style="text-align: right;">选择日期:</th>
                <td style="text-align: left;"><input type="text"
                                                     onfocus="WdatePicker({isShowClear:true,readOnly:true,maxDate:'%y-%M-#{%d}'})"
                                                     value="<?php echo $_POST['startTime']?$_POST['startTime']:date('Y-m-d');?>"
                                                     name="startTime" id="startTime" class="Wdate" />
                    &nbsp;&nbsp;到&nbsp;&nbsp; <input type="text"
                                                     onfocus="WdatePicker({isShowClear:true,readOnly:true,maxDate:'%y-%M-#{%d}'})"
                                                     value="<?php echo $_POST['endTime']?$_POST['endTime']:date('Y-m-d');?>"
                                                     name="endTime" id="endTime" class="Wdate" /></td>
            </tr>
            <tr>
                <th style="text-align: right;">流水类型:</th>
                <td style="text-align: left;">
                    <select name="type" id="type" onchange="itemSelect()">
                        <option value="ALL">&lt;所有流水&gt;</option>
                        <?php
                        foreach($gameConfig as $v){
                            echo '<option value="'.$v['type'].'">'. $v['title'] . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th colspan="2"><input type="submit" value="确定查询"></th>
            </tr>
            </tbody>
        </table>
    </form>

    <br />
<?php if (isset($_POST['uid'])){?>
    <table style="table-layout: fixed;text-align:center;border-color: #7a7a7a; word-break: keep-all; white-space: nowrap; width: 100%;">
        <caption style="border-color: #bdbfc3;background-color: #b5b5ff;font-size: 16px;color: #191715">【<?php echo $uid?>】<?php echo $UserModel->info['name'];?></caption>
        <tr>
            <th>模块</th>
            <th>控制器</th>
            <th>参数</th>
            <th>时间</th>
            <th>ip</th>
            <th>事件</th>
        </tr>
        <?php
        if (is_array($data) ) {
            foreach($data as $v){
                echo "<td style=\"background-color:#f6f9f3;\">{$v['model']}</td>";
                echo "<td style=\"background-color:#f6f9f3;\">{$v['ctrl']}</td>";
                echo "<td style=\"background-color:#f6f9f3;\">{$v['params']}</td>";
                echo "<td style=\"background-color:#f6f9f3;\">".date('Y-m-d H:i:s',$v['ftime'])."</td>";
                echo "<td style=\"background-color:#f6f9f3;\">{$v['ip']}</td>";
                echo "<td ><div>";
                if (is_array($v['record'])){
                    echo "<table style='width: 100%;text-align: center;'>
                    <tr>
                        <th style='width: 25%;'>类型</th>
                        <th style='width: 25%;'>道具id</th>
                        <th style='width: 25%;'>差值</th>
                        <th style='width: 25%;'>新值</th>
                       
                    </tr>";
                    foreach ($v['record'] as $rk => $rv){
                        echo '<tr style="background-color:#f6f9f3;"><td>'.$gameConfig[$rv['type']]['title'].'</td>';
                        echo '<td>';
                        if($rv['type'] < 13 && $rv['type'] > 7){
                            echo $heroConfig[$rv['itemid']]['name'].'</td>';
                        }elseif ($rv['type'] == 6){
                            echo $itemConfig[$rv['itemid']]['name'].'</td>';
                        }
                        echo '<td>'.$rv['cha'].'</td>';
                        echo '<td>'.$rv['next'].'</td></tr>';
                    }
                    echo "</table> ";
                }else{
                    echo '无';
                }
                echo "</div></td></tr>";
            }
        }
        ?>
    </table>

<?php }?>
<?php include(TPL_DIR.'footer.php');?>