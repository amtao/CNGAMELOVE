<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 8;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<form name="form" method="post" action="">
<table>
<tr><th colSpan="2">发放邮件</th></tr>
<tr><th>选择时间*:</th><td style="text-align:left;">
        <input class='Wdate' type='text' size='40' id='startTime' name='startTime'
               value='<?php echo (empty($info['startTime'])) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $info['startTime']);?>'
               onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						maxDate:'#F{$dp.$D(\'endTime\')}'})" />
        &nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;

        <input class='Wdate' type='text' size='40' id='endTime' name='endTime'
               value='<?php echo  date('Y-m-d 23:59:59') ?>'
               onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						minDate:'#F{$dp.$D(\'startTime\')}'})" />
<input type="checkbox" name="registerTime" checked="checked" value="1">邮件发放开始时间之后注册的用户收不到</td></tr>
    <tr><th>区服选择*:</th><td><input type="text" name="server" style="width: 600px;" size="120" id="title" value="<?php echo isset($_POST['server']) ? $_POST['server'] : 'all';?>">(默认all表示所有服,连续服用"-"隔开如1-20)</td></tr>
    <tr><th>渠道*:</th><td>
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
        </td></tr>
    <tr><th>外部链接:</th><td><input type="text" name="link" style="width: 600px;" size="120" id="title" value="<?php echo isset($_POST['link']) ? $_POST['link'] : '';?>"></td></tr>
    <tr><th>vip类型*:</th><td>
            <select name="vipType">
                <option value="1" >最低等级</option>
                <option value="2" >区间</option>
                <option value="3" >间隔</option>
            </select>(<span style="color: red">最低等级</span> 在<span style="color: red">vip等级</span>里填:1表示大于vip需大于1  <span style="color: red">区间vip</span>请使用-隔开,如2-3,表示只有vip2,vip3可收到 <span style="color: red">间隔vip</span>使用,隔开如: 1,4,6表示vip1,vip4,vip6才能收到.<span style="color: red">不懂问技术</span>)</td></tr>
    <tr><th>VIP等级*:</th><td><input type="text" name="vip" style="width: 600px;" size="120" id="title" value="<?php echo isset($_POST['vip']) ? $_POST['vip'] : 0;?>"></td></tr>

<tr><th>身份等级*:</th><td><select name="level">
            <?php foreach ($guan as $k => $v):?>
                <option value="<?php echo $v['id'];?>" ><?php echo $v['name'];?> </option>
            <?php endforeach;?>
        </select></td></tr>
<tr><th>邮件标题*:</th><td><input type="text" name="title" style="width: 600px;" size="120" id="title" value="<?php echo isset($_POST['title']) ? $_POST['title'] : '';?>"></td></tr>
<tr><th>邮件内容:</th><td><textarea cols="100" style="width: 600px;" rows="8" name ="message" ><?php echo isset($_POST['message']) ? $_POST['message'] : '';?></textarea></td></tr>
<?php if(empty($auth['ban']['email']['item'])):?>
<tr><th>道具列表</th>
    <td>
        <select class="input" name="item">
        <?php foreach ($items as $key => $value):?>
            <option value="<?php echo $key.'-'.$value['name']; ?>"><?php echo $value['id'].' - '.$value['name']; ?></option>
        <?php endforeach;?>
        </select>
        <input name="num" class="input" value="1" />
        <input name="add" class="input" type="button" value="添加" />
    </td>
</tr>
<?php endif;?>
<tr><th>发送的道具信息</th>
    <td id="item">

    </td>
</tr>
    <tr><th>备注信息:</th><td><textarea cols="100" style="width: 600px;" rows="3" name ="remarks" ><?php echo isset($_POST['remarks']) ? $_POST['remarks'] : '';?></textarea></td></tr>
<tr><th></th><td><input type="submit" class="input" value="发送" /></td></tr>
</table>
</form>
<?php echo $msg;?>
<script type="text/javascript">
    $(function () {
        $(':input[name="add"]').click(function () {
            var item = $('[name="item"]').val();
            var num = $('[name="num"]').val();
            var item = item + '-' + num;
            var arr = item.split('-');
            var rand = Math.random()*Math.random();
            str = '<p data-number="'+rand+'"><input name="items[]" type="hidden" value="'+item+'" />'+'<span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block"><b style="padding-left:10px;">道 具 : </b>' + arr[1] + ' </span><span style="width: 160px;border: 1px solid #bda2a2;background-color: #e9e4da;display: inline-block;padding-left:10px;"><b style="padding-left:10px;"> 数 量 : </b>'+num+'</span><input class="input" onclick="del('+rand+')" type="button" value="删除" / ></p>';
            $('#item').append(str);
        });
    });
    function del(rand){
        if (confirm('确认删除?')){
            $('[data-number="'+rand+'"]').remove();
        }else {
            return false;
        }
    }
</script>
<?php include(TPL_DIR.'footer.php');?>