<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<style>
    .th{
        text-align: right;
    }
    .th2{
        text-align: left;
    }
</style>
<form name="form2" id="form2" method="post" action="">
    <table style='width:100%;' class="mytable">

        <tr>
            <th class="th">编号：</th>
            <td><?php echo $data['pmdno'];?><input type='text' size='20'  style="display: none" id='pmdno' name='pmdno' value="<?php echo $data['pmdno'];?>" /></td>
        </tr>

        <tr>
            <th class="th">生效区服：</th>
            <td><input type='text' size='20'   id='pmdserv' name='pmdserv' value="<?php echo $data['pmdserv'];?>" />格式:1-20,25</td>
        </tr>

        <tr>
            <th class="th">
                开始时间：
            </th>
            <td class="th2">
                <input class='Wdate' type='text' size='40' id='startTime' name='sTime'
                       value='<?php echo (empty($data['sTime'])) ? date('Y-m-d 00:00:00') : $data['sTime'];?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						maxDate:'#F{$dp.$D(\'endTime\')}'})" />
            </td>

        </tr>
        <tr>
            <th class="th">
                结束时间：
            </th>
            <td class="th2">
                <input class='Wdate' type='text' size='40' id='endTime' name='eTime'
                       value='<?php echo (empty($data['eTime'])) ? date('Y-m-d 23:59:59') : $data['eTime'];?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						minDate:'#F{$dp.$D(\'startTime\')}'})" />
            </td>

        </tr>

        <tr>
            <th class="th">间隔分钟：</th>
            <td>
                <input type='text' size='20'   id='time' name='time' value="<?php echo $data['time'];?>" /> 默认1分钟发一次(最小一分钟)
            </td>
        </tr>

        <tr>
            <th class="th">播放次数：</th>
            <td>
                <input type='text' size='20'   id='num' name='num' value="<?php echo $data['num'];?>" /> 默认发一次
            </td>
        </tr>

        <tr>
            <th class="th">使用特效：</th>
            <td>
                <input type='text' size='20'   id='pmdef' name='pmdef' value="<?php echo $data['pmdef'];?>" /> 默认特效
            </td>
        </tr>

        <tr>
            <th class="th">语句:</th>
            <td class="th2">
                <textarea type='text' rows="6" cols="140"  id='msg' name='msg' ><?php echo $data['msg'];?></textarea>
            </td>
        </tr>

        <tr>
            <td colspan='2' align='center'>
                <input type='hidden' id='save' name='save' value='save' />
                <input type='submit' value='保存' />
            </td>
        </tr>

    </table>
</form>
<br><br>
<form name="form2" id="form2" method="post" action="">
    <table style='width:100%;' class="mytable">

        <tr>
            <th class="th2">编号</th>
            <th class="th2">生效区服</th>
            <th class="th2">开始时间</th>
            <th class="th2">结束时间</th>
            <th class="th2">间隔分钟</th>
            <th class="th2">播放次数</th>
            <th class="th2">使用特效</th>
            <th class="th2">语句</th>
            <th class="th2">操作</th>
        </tr>

        <?php if(!empty($Sev93Model->info)){ krsort($Sev93Model->info); ?>

        <?php foreach ($Sev93Model->info as $k => $value ){ ?>

            <tr>
                <td class="th2"><?php echo $value['pmdno'];?></td>
                <td class="th2"><?php echo $value['pmdserv'];?></td>
                <td class="th2"><?php echo $value['sTime'];?></td>
                <td class="th2"><?php echo $value['eTime'];?></td>
                <td class="th2"><?php echo $value['time'];?></td>
                <td class="th2"><?php echo $value['num'];?></td>
                <td class="th2"><?php echo $value['pmdef'];?></td>
                <td class="th2"><?php echo $value['msg'];?></td>
                <td class="th2">
<a href='?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=kefupmd&updatekey=<?php echo $value['pmdno'];?>'>编辑</a>
<a class="delete" data-name="<?php echo $value['pmdno'] ?>" href='?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=kefupmd&delkey=<?php echo $value['pmdno'];?>'>删除</a>
                </td>
            </tr>




        <?php }} ?>

    </table>
</form>


<?php include(TPL_DIR.'footer.php');?>
<script>
    $(document).ready(function () {
        $(".delete").on('click',function (e) {
            e.preventDefault();
            var name_zh = $(this).data('name');
            if (confirm('确定要删除编号: '+name_zh+' 吗?')){
                var href = $(this).prop('href');
                window.location.href = href;
            }else{
                return false;
            }
        });
    });
</script>