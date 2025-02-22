<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>
    <script>
        function itemSelect(){
            var obj = document.getElementById("type");
            var fitem = document.getElementById("items");
            var fhero = document.getElementById("hero");
          
            if (obj.value == "6")
            {
                fitem.style.display='';
                fhero.style.display='none';
            } else if(obj.value >7 && obj.value<13)  {
            	fhero.style.display='';
            	fitem.style.display='none';
            }else{
            	fitem.style.display='none';	
            	fhero.style.display='none';
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
                <th style="text-align: right;">旧流水记录:</th><td><input type="checkbox" name="old" value="1" <?php echo $_POST['old']?'checked="checked"':'';?> >旧流水</td>
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
                           value='<?php echo (empty($endTime)) ? date('Y-m-d 23:59:59') : date('Y-m-d 23:59:59', $endTime);?>'
                           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						minDate:'#F{$dp.$D(\'startTime\')}'})" />
                </td>
            </tr>
            <tr>
                <th style="text-align: right;">流水类型:</th>
                <td style="text-align: left;">
                    <select name="type" id="type" onchange="itemSelect();">
                        <option value="0">&lt;所有流水&gt;</option>
                        <?php
                        foreach($gameConfig as $v){
                            echo '<option value="'.$v['type'].'"'.($v['type'] == $_POST['type'] ? "selected" : '').'>'. $v['title'] . '</option>';
                        }
                        ?>
                    </select>
                    
                    <select name="items" id="items" <?php if($_POST['type'] != 6) echo 'style="display:none"';?> >
                        <option value="0">&lt;所有道具&gt;</option>
                        <?php
                        foreach($itemConfig as $v){
                            echo '<option value="'.$v['id'].'"'.($v['id'] == $_POST['items'] ? "selected" : '').'>'.$v['id'].'-'.$v['name_cn'] . '</option>';
                        }
                        ?>
                    </select>
                    
                    
                     <select name="hero" id="hero" <?php if($_POST['type'] <8 || $_POST['type'] >12) echo 'style="display:none"';?> >
                        <option value="0">&lt;所有伙伴&gt;</option>
                        <?php
                        foreach($heroConfig as $v){
                            echo '<option value="'.$v['heroid'].'"'.($v['heroid'] == $_POST['hero'] ? "selected" : '').'>'.$v['heroid'].'-'.$v['name'] . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
           <!-- <tr>
            <th style="text-align: right;">模块类型</th>
                <td style="text-align: left;">
                    <select name="mod" id="mod">
                        <option value="0">&lt;所有模块&gt;</option>
                        <?php
/*                        foreach($modelConfig as $v){
                            echo '<option value="'.$v.'"'.($v == $_POST['mod'] ? "selected" : '').'>'. $v . '</option>';
                        }
                        */?>
                    </select>
                </td>
            </tr>-->
            <tr>
                <th colspan="2"><input type="submit" value="确定查询"></th>
            </tr>
            </tbody>
        </table>
    </form>

    <br />
<?php if (isset($_POST['uid'])){?>
    <table style="text-align:center;border-color: #7a7a7a; width: 100%;">
        <caption style="border-color: #bdbfc3;background-color: #b5b5ff;font-size: 16px;color: #191715">【<?php echo $uid?>】<?php echo $UserModel->info['name'];?></caption>
        <tr>
            <th>模块</th>
            <th>控制器</th>
            <th>类型</th>
            <th>道具id</th>
            <th>旧值</th>
            <th>差值</th>
            <th>新值</th>
            <th>增减</th>
            <th>时间</th>
            <th>ip</th>
        </tr>
        <?php
        if (is_array($data) ) {
            $k = 0;
            foreach($data as $v){
                $color = array('#fff','#aecef2');
                if (is_array($v['record'])){
                    foreach ($v['record'] as $rk => $rv){
                        echo '<tr title="'.str_replace('"','`',$v['params']).'" style="background-color: '.$color[$k].'"><td>';
                        if (!empty($msg_lang['cs'][$v['model']])){
                            echo $msg_lang['cs'][$v['model']];
                        }elseif(!empty($other_lang[$v['model']])){
                            echo $other_lang[$v['model']];
                        }else{
                            echo $v['model'].'('.$v['params'].')';
                        }
                        echo '('.$v['model'].')</td><td>';
                        if (!empty($msg_lang[$v['model']][$v['ctrl']])){
                            echo $msg_lang[$v['model']][$v['ctrl']];
                        }elseif(!empty($other_lang[$v['ctrl']])){
                            echo $other_lang[$v['ctrl']];
                        }else{
                            echo $v['ctrl'];
                        }
                        echo '('.$v['ctrl'].')</td><td>'.$gameConfig[$rv['type']]['title'].'</td>';
                        echo '<td>';
                        if(($rv['type'] <= 13 && $rv['type'] > 7) || $rv['type'] == 26 || $rv['type'] == 25){
                            if (empty($heroConfig[$rv['itemid']]['name'])){
                                echo '英雄id:'.$rv['itemid'].'</td>';
                            }else{
                                echo $heroConfig[$rv['itemid']]['name'].'</td>';
                            }
                        }elseif ($rv['type'] <= 16 && $rv['type'] >= 14){
                            if (empty($wifeConfig[$rv['itemid']]['wname'])){
                                echo '道具id:'.$rv['itemid'].'</td>';
                            }else{
                                echo $wifeConfig[$rv['itemid']]['wname'].'</td>';
                            }
                        }elseif ($rv['type'] == 6 || $rv['type'] == 28){
                            if (empty($itemConfig[$rv['itemid']]['name'])){
                                echo '道具id:'.$rv['itemid'].'</td>';
                            }else{
                                echo $itemConfig[$rv['itemid']]['name'].'</td>';
                            }
                        }elseif ($rv['type'] == 6140){
                            if (empty($cloher_lang['user_cloher'][$rv['itemid']])){
                                echo '道具id:'.$rv['itemid'].'</td>';
                            }else{
                                echo $cloher_lang['user_cloher'][$rv['itemid']].'</td>';
                            }
                        }else{
                            echo $rv['itemid'].'</td>';
                        }
                        $old = $rv['next']-$rv['cha'];
                        echo '<td style="color: #f30ee0">'.$old.'</td>';
                        echo '<td style="color: red">'.$rv['cha'].'</td>';
                        echo '<td style="color: #0e25d2">'.$rv['next'].'</td>';
                        echo '<td>';
                        if ($rv['cha']>0){
                            echo "<span style='color: #0000FF'>增加</span>";
                        }elseif($rv['cha']==0){
                            echo "<span style='color: #0b7171'>不变</span>";
                        }else{
                            echo "<span style='color: red'>减少</span>";
                        }
                        echo'</td>';
                        echo "<td>".date("Y-m-d H:i:s", $v['ftime'])."</td>";
                        echo "<td>{$v['ip']}</td></tr>";

                    }
                    $k++;
                    if ($k>1){
                        $k = 0;
                    }
                }


        }
        }
        ?>
    </table>
<?php }?>

<?php include(TPL_DIR.'footer.php');?>